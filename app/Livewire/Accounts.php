<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Accounts extends Component
{
    public $currentUser;

    public $username;
    public $password;
    public $role;

    public $newUsername;
    public $newPassword;
    public $newRole;

    public $users;

    public $deletingUserId = null;

    public function mount()
    {
        $this->currentUser = auth()->user();

        $this->username = $this->currentUser->username;
        $this->role = $this->currentUser->role;

        $this->users = User::all();
    }

    public function updateAccount()
    {
        $this->validate([
            'username' => 'required|string|unique:users,username,' . $this->currentUser->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string'
        ]);

        $this->currentUser->username = $this->username;
        $this->currentUser->role = $this->role;

        if ($this->password) {
            $this->currentUser->password = Hash::make($this->password);
        }

        $this->currentUser->save();

        // Clear password input after saving
        $this->password = '';

        // Reload values from DB to ensure latest data
        $this->username = $this->currentUser->username;
        $this->role = $this->currentUser->role;

        $this->users = User::all();
    }

    public function addAccount()
    {
        $this->validate([
            'newUsername' => 'required|string|unique:users,username',
            'newPassword' => 'required|string|min:6',
            'newRole' => 'required|string',
        ]);

        User::create([
            'username' => $this->newUsername,
            'password' => Hash::make($this->newPassword),
            'role' => $this->newRole,
        ]);

        $this->reset(['newUsername', 'newPassword', 'newRole']);

        $this->users = User::all();
    }

    // Confirm delete workflow
    public function confirmDeleteUser($userId)
    {
        $this->deletingUserId = $userId;
    }

    public function cancelDeleteUser()
    {
        $this->deletingUserId = null;
    }

    public function deleteAccountConfirmed()
    {
        User::findOrFail($this->deletingUserId)->delete();

        $this->users = User::all();
        $this->deletingUserId = null;
    }

    public function render()
    {
        return view('livewire.accounts');
    }
}

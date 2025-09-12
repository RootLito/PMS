<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            ACCOUNT MANAGEMENT
        </h2>
    </div>
    <div class="flex-1 flex gap-10">
        <div class="w-[30%] bg-white rounded-xl p-6" x-data="{ showPassword: false }">
            <h2 class="text-2xl text-gray-700 font-bold mb-12">My Account</h2>

            {{-- Username --}}
            <label for="username" class="block text-xs text-gray-700">Username</label>
            <input type="text" wire:model.defer="username" id="username"
                class="text-sm mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">

            {{-- Password --}}
            <label for="password" class="block text-xs text-gray-700 mt-2">Password</label>
            <div class="relative mt-1">
                <input :type="showPassword ? 'text' : 'password'" wire:model.defer="password" id="password"
                    class="text-sm block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 pr-10"
                    autocomplete="new-password" placeholder="">
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-2 flex items-center px-2 text-gray-600 hover:text-gray-900"
                    tabindex="-1">
                    <template x-if="showPassword">
                        <i class="fa-solid fa-eye"></i>
                    </template>
                    <template x-if="!showPassword">
                        <i class="fa-solid fa-eye-slash"></i>
                    </template>
                </button>
            </div>

            <label for="role" class="block text-xs text-gray-700 mt-2">Role</label>
            <select wire:model.defer="role" id="role"
                class="text-sm mt-1 block w-full h-10 border border-gray-200 bg-white rounded-md px-2">
                <option value="sysadmin">sysadmin</option>
                <option value="admin">admin</option>
            </select>

            <button wire:click="updateAccount"
                class="text-xs mt-6 w-full h-10 bg-green-700 hover:bg-green-900 text-white font-semibold rounded-md transition duration-150 ease-in-out cursor-pointer">
                Update
            </button>
        </div>


        <div class="w-[70%] h-full flex flex-col gap-10">
            <div class="w-full h-[30%] bg-white rounded-xl p-6">
                <h2 class="text-2xl text-gray-700 font-bold">Add Account</h2>

                @if (session()->has('message'))
                    <div class="text-green-600 text-sm mt-2">
                        {{ session('message') }}
                    </div>
                @endif

                @error('newUsername')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
                @error('newPassword')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
                @error('newRole')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <input type="text" wire:model.defer="newUsername" placeholder="Username"
                        class="text-sm h-10 border border-gray-200 bg-gray-50 rounded-md px-2">

                    <div x-data="{ show: false }" class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model.defer="newPassword"
                            class="text-sm h-10 w-full border border-gray-200 bg-gray-50 rounded-md px-2 pr-10"
                            placeholder="Password">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-2 flex items-center text-gray-500 text-sm">
                            <template x-if="show">
                                <i class="fa-solid fa-eye"></i>
                            </template>
                            <template x-if="!show">
                                <i class="fa-solid fa-eye-slash"></i>
                            </template>
                        </button>
                    </div>

                    <select wire:model.defer="newRole"
                        class="text-sm h-10 border border-gray-200 bg-white rounded-md px-2">
                        <option value="">Select Role</option>
                        <option value="sysadmin">sysadmin</option>
                        <option value="admin">admin</option>
                    </select>
                </div>

                <button wire:click="addAccount"
                    class="mt-4 w-full h-10 bg-slate-600 hover:bg-slate-800 text-white font-semibold rounded-md text-sm">
                    Add Account
                </button>
            </div>


            <div class="w-full h-[70%] bg-white rounded-xl p-6">
                <h2 class="text-2xl text-gray-700 font-bold mb-4">Account List</h2>

                <table class="w-full text-sm text-left text-gray-700">
                    <thead>
                        <tr class="bg-gray-200 border-b border-gray-300">
                            <th class="p-3">Username</th>
                            <th class="p-3">Role</th>
                            <th class="p-3" width="20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $account)
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-3">{{ $account->username }}</td>
                                <td class="py-2 px-3">{{ $account->role }}</td>
                                <td class="py-2 px-3 flex gap-2">

                                    {{-- Update button with edit icon --}}
                                    {{-- <button wire:click="updateAccount({{ $account->id }})"
                                        class="bg-green-700 hover:bg-green-900 text-white font-semibold rounded-md text-xs px-3 py-1 transition duration-150 ease-in-out cursor-pointer flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Update
                                    </button> --}}

                                    {{-- Delete confirmation buttons --}}
                                    @if ($deletingUserId === $account->id)
                                        <button wire:click="cancelDeleteUser"
                                            class="bg-red-500 hover:bg-red-600 text-white font-semibold rounded-md text-xs px-3 py-1 transition duration-150 ease-in-out cursor-pointer flex items-center gap-1"
                                            title="Cancel Delete">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button wire:click="deleteAccountConfirmed"
                                            class="bg-green-500 hover:bg-green-600 text-white font-semibold rounded-md text-xs px-3 py-1 transition duration-150 ease-in-out cursor-pointer flex items-center gap-1"
                                            title="Confirm Delete">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <button wire:click="confirmDeleteUser({{ $account->id }})"
                                            class="bg-red-700 hover:bg-red-900 text-white font-semibold rounded-md text-xs px-3 py-1 transition duration-150 ease-in-out cursor-pointer flex items-center gap-1"
                                            title="Delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<form wire:submit.prevent="save" class="space-y-4">
    <div class="flex items-center gap-2 mb-10">
        <a href="{{ route('regular-employee') }}" class="text-red-400">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h2 class="text-xl text-gray-700 font-bold">
            New Regular Employee
        </h2>
    </div>

    <div class="w-full grid grid-cols-4 gap-2">
        <div>
            <label for="last_name" class="block text-sm text-gray-700">
                Last Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="last_name" wire:model="last_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('last_name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="first_name" class="block text-sm text-gray-700">
                First Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="first_name" wire:model="first_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('first_name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="middle_initial" class="block text-sm text-gray-700">Middle Name</label>
            <input type="text" id="middle_initial" wire:model="middle_initial"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('middle_initial')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="suffix" class="block text-sm text-gray-700">Suffix</label>
            <input type="text" id="suffix" wire:model="suffix" maxlength="5" placeholder="e.g. Jr., Sr., III"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('suffix')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
    </div>


    <div class="w-full grid grid-cols-4 gap-2">
        <div>
            <label for="gender" class="block text-sm text-gray-700">
                Gender <span class="text-red-400">*</span>
            </label>
            <select id="gender" wire:model="gender"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled selected>Select gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            @error('gender')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="office_pap" class="block text-sm text-gray-700">SL Code</label>
            <input type="text" id="office_pap" wire:model.live="officePap"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('officePap')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="position" class="block text-sm text-gray-700">
                Office <span class="text-red-400">*</span>
            </label>
            <select id="position" wire:model="position"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled selected>Select a position</option>
                @foreach ($positions as $pos)
                    <option value="{{ $pos->name }}">{{ $pos->name }}</option>
                @endforeach
            </select>
            @error('position')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>


        <div>
            <label for="office_name" class="block text-sm text-gray-700">Position</label>
            <select id="office_name" wire:model.live="office_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2"
                {{ !$designation ? 'disabled' : '' }}>
                <option value="" disabled>Select office</option>
                @if ($designation && isset($officeOptions[$designation]))
                    @foreach ($officeOptions[$designation] as $office => $code)
                        <option value="{{ $office }}">{{ $office }}</option>
                    @endforeach
                @endif
            </select>
            @error('office_name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
    </div>


    <div class="w-full grid grid-cols-2 gap-2 mt-4">
        <div>
            <label for="monthly_rate" class="block text-sm text-gray-700">
                Monthly Rate <span class="text-red-400">*</span>
            </label>
            <select id="monthly_rate" wire:model.live="monthly_rate" step="0.01"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled selected>- - Select - -</option>

                @foreach ($salaries as $salary)
                    <option value="{{ $salary->monthly_rate }}">
                        {{ number_format($salary->monthly_rate, 2) }}
                    </option>
                @endforeach
            </select>
            @error('monthly_rate')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="gross" class="block text-sm text-gray-700">
                Gross<span class="text-red-400">*</span>
            </label>
            <input type="number" id="gross" wire:model="gross" step="0.01" readonly
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('gross')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="pt-4">
        <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer">
            Submit
        </button>
    </div>
</form>

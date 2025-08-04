<form wire:submit.prevent="save" class="space-y-4">
    <div class="flex items-center gap-2 mb-10">
        <a href="{{ route('employee') }}" class="text-red-400">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h2 class="text-xl text-gray-700 font-bold">
            New Employee
        </h2>
    </div>

    <div class="w-full grid grid-cols-3 gap-2">
        <div>
            <label for="last_name" class="block text-sm text-gray-700">
                Last Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="last_name" wire:model="last_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('last_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="first_name" class="block text-sm text-gray-700">
                First Name <span class="text-red-400">*</span>
            </label>
            <input type="text" id="first_name" wire:model="first_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('first_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="w-full grid grid-cols-2 gap-2">
            <div>
                <label for="middle_initial" class="block text-sm text-gray-700">Middle Initial</label>
                <input type="text" id="middle_initial" wire:model="middle_initial" maxlength="1"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                @error('middle_initial') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="suffix" class="block text-sm text-gray-700">Suffix</label>
                <input type="text" id="suffix" wire:model="suffix" maxlength="5" placeholder="e.g. Jr., Sr., III"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                @error('suffix') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="w-full grid grid-cols-3 gap-2">
        <div>
            <label for="designation" class="block text-sm text-gray-700">
                Designation <span class="text-red-400">*</span>
            </label>
            <select id="designation" wire:model.live="designation"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled>Select designation</option>
                @foreach ($designations as $designationOption)
                    <option value="{{ $designationOption }}">{{ $designationOption }}</option>
                @endforeach
            </select>
            @error('designation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="office_name" class="block text-sm text-gray-700">Office Name</label>
            <select id="office_name" wire:model.live="office_name"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" {{ !$designation ? 'disabled' : '' }}>
                <option value="" disabled>Select office</option>
                @if ($designation)
                    @foreach ($officeOptions[$designation] ?? [] as $office => $code)
                        <option value="{{ $office }}">{{ $office }}</option>
                    @endforeach
                @endif
            </select>
            @error('office_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="office_code" class="block text-sm text-gray-700">PAP</label>
            <input type="text" id="office_code" wire:model="office_code" readonly
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('office_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="w-full grid grid-cols-3 gap-2 mt-4">
        <div>
            <label for="employment_status" class="block text-sm text-gray-700">
                Employment Status <span class="text-red-400">*</span>
            </label>
            <select id="employment_status" wire:model="employment_status"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled>Select status</option>
                <option value="JO">JO</option>
                <option value="COS">COS</option>
            </select>
            @error('employment_status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="monthly_rate" class="block text-sm text-gray-700">
                Monthly Rate <span class="text-red-400">*</span>
            </label>
            <select id="monthly_rate" wire:model.live="monthly_rate" step="0.01"
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <option value="" disabled selected>- - Select - -</option>
                <option value="13378.00">13,378.00</option>
                <option value="15275.00">15,275.00</option>
                <option value="15368.00">15,368.00</option>
                <option value="15738.00">15,738.00</option>
                <option value="16458.00">16,458.00</option>
                <option value="16758.00">16,758.00</option>
                <option value="17179.00">17,179.00</option>
                <option value="17505.00">17,505.00</option>
                <option value="18251.00">18,251.00</option>
                <option value="18784.00">18,784.00</option>
                <option value="20572.00">20,572.00</option>
                <option value="21205.00">21,205.00</option>
                <option value="23877.00">23,877.00</option>
                <option value="25232.00">25,232.00</option>
                <option value="25439.00">25,439.00</option>
                <option value="35097.00">35,097.00</option>
                <option value="75359.00">75,359.00</option>
            </select>









            @error('monthly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="gross" class="block text-sm text-gray-700">
                Gross<span class="text-red-400">*</span>
            </label>
            <input type="number" id="gross" wire:model="gross" readonly
                class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
            @error('gross') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="pt-4">
        <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer">
            Submit
        </button>

    </div>

</form>
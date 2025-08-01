<div class="flex-1 flex gap-10 ">
    <div class="flex flex-col h-full w-1/2 bg-white p-6 rounded-xl">
        <form wire:submit.prevent="save" class="w-full flex flex-col">
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Add Signatory
            </h2>

            <div class="mt-2">
                <label for="name" class="block text-sm text-gray-700">
                    Name <span class="text-red-400">*</span>
                </label>
                <input type="text" id="name" wire:model.live="name"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="designation" class="block text-sm text-gray-700">
                    Designation <span class="text-red-400">*</span>
                </label>
                <input type="text" id="designation" wire:model.live="designation"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                @error('designation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                Save
            </button>
        </form>


        <h2 class="text-xl text-gray-700 font-bold mt-10 mb-6">
            Signatory Lists
        </h2>
        <div>

            {{-- @if(session()->has('message'))
            <div class="text-green-500 mb-4">{{ session('message') }}</div>
            @endif --}}

            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 text-nowrap">Name</th>
                        <th class="px-4 py-2 text-nowrap">Designation</th>
                        <th class="px-4 py-2 text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($signatories as $signatory)
                    <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer">
                        <td class="px-4 py-2 font-bold">{{ $signatory->name }}</td>
                        <td class="px-4 py-2">{{ $signatory->designation }}</td>
                        <td class="px-4 py-2">
                            <button class="bg-blue-500 text-white py-1 px-2 rounded">Edit</button>
                            <button class="bg-red-500 text-white py-1 px-2 rounded">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center text-gray-500">No signatories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $signatories->links() }}
            </div>
        </div>

    </div>
    <div class="flex flex-col h-full w-1/2 bg-white p-6 rounded-xl">
        <form wire:submit.prevent="updateRoles" class="w-full flex flex-col">
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Assign Signatory
            </h2>

            <!-- Prepared Signatory -->
            <div class="mt-2">
                <label for="prepared" class="block text-sm text-gray-700">
                    Prepared:
                </label>
                <select id="prepared" wire:model="prepared_id"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('prepared') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Noted by Signatory -->
            <div class="mt-2">
                <label for="noted_by" class="block text-sm text-gray-700">
                    Noted by:
                </label>
                <select id="noted_by" wire:model="noted_by_id"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('noted_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Funds Availability Signatory -->
            <div class="mt-2">
                <label for="funds_availability" class="block text-sm text-gray-700">
                    Funds Availability:
                </label>
                <select id="funds_availability" wire:model="funds_availability_id"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('funds_availability') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Approved Signatory -->
            <div class="mt-2">
                <label for="approved" class="block text-sm text-gray-700">
                    Approved:
                </label>
                <select id="approved" wire:model="approved_id"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('approved') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="button" wire:click="updateRoles"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                Confirm
            </button>
        </form>







        <div>
            <h2 class="text-xl text-gray-700 font-bold mt-10 mb-6">
                Current Signatory
            </h2>
            <div class="w-full flex items-center">
                <p class="w-48">Prepared:</p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">Name</p>
                    <p class="text-xs text-gray-600 flex-1">Designation</p>
                </div>
            </div>
            <div class="w-full flex items-center">
                <p class="w-48"> Noted by: </p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">Name</p>
                    <p class="text-xs text-gray-600 flex-1">Designation</p>
                </div>
            </div>
            <div class="w-full flex items-center">
                <p class="w-48"> Funds Availability</p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">Name</p>
                    <p class="text-xs text-gray-600 flex-1">Designation</p>
                </div>
            </div>
            <div class="w-full flex items-center">
                <p class="w-48"> Approved:
                </p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">Name</p>
                    <p class="text-xs text-gray-600 flex-1">Designation</p>
                </div>
            </div>
        </div>
    </div>
</div>
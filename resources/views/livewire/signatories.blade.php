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
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        @if ($editingId === $signatory->id)
                        <td class="px-4 py-2">
                            <input type="text" wire:model.defer="editName"
                                class="block w-full  py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                            @error('editName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" wire:model.defer="editDesignation"
                                class="block w-full  py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                            @error('editDesignation')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </td>
                        <td class="px-4 py-2 flex gap-2 items-center">
                            <button wire:click="cancelEdit"
                                class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer"
                                title="Cancel">
                                <i class="fas fa-times"></i>
                            </button>
                            <button wire:click="updateSignatory"
                                class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                title="Confirm">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>

                        @elseif ($deletingId === $signatory->id)
                        <td class="px-4 py-2 font-bold">{{ $signatory->name }}</td>
                        <td class="px-4 py-2">{{ $signatory->designation }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <button wire:click="cancelDelete"
                                class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer" title="Cancel Delete">
                                <i class="fas fa-times"></i>
                            </button>
                            <button wire:click="deleteSignatoryConfirmed"
                                class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                title="Confirm Delete">
                                <i class="fas fa-check"></i>
                            </button>
                        </td>

                        @else
                        <td class="px-4 py-2 font-bold">{{ $signatory->name }}</td>
                        <td class="px-4 py-2">{{ $signatory->designation }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <button wire:click="startEdit({{ $signatory->id }})"
                                class="bg-blue-500 text-white py-1 px-2 rounded  cursor-pointer"><i class="fas fa-edit"></i> Edit</button>
                            <button wire:click="confirmDelete({{ $signatory->id }})"
                                class="bg-red-500 text-white py-1 px-2 rounded  cursor-pointer"><i class="fas fa-trash-alt"></i> Delete</button>
                        </td>
                        
                        @endif
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
        <form wire:submit.prevent="saveSignatory" class="w-full flex flex-col">
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Assign Signatory
            </h2>

            <div class="mt-2">
                <label for="prapared_by" class="block text-sm text-gray-700">Prepared:</label>
                <select id="prapared_by" wire:model="prapared_by"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" selected disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('prapared_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="noted_by" class="block text-sm text-gray-700">Noted by:</label>
                <select id="noted_by" wire:model="noted_by"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" selected disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('noted_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="funds_availability" class="block text-sm text-gray-700">Funds Availability:</label>
                <select id="funds_availability" wire:model="funds_availability"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" selected disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('funds_availability') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="approved_by" class="block text-sm text-gray-700">Approved:</label>
                <select id="approved_by" wire:model="approved_by"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" selected disabled>- - Select - -</option>
                    @foreach($allSignatories as $signatory)
                    <option value="{{ $signatory->id }}">{{ $signatory->name }}</option>
                    @endforeach
                </select>
                @error('approved_by') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
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
                    <p class="font-bold flex-1">{{ $assigned->prepared->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 flex-1">{{ $assigned->prepared->designation ?? '-' }}</p>
                </div>
            </div>

            <div class="w-full flex items-center">
                <p class="w-48">Noted by:</p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">{{ $assigned->noted->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 flex-1">{{ $assigned->noted->designation ?? '-' }}</p>
                </div>
            </div>

            <div class="w-full flex items-center">
                <p class="w-48">Funds Availability:</p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">{{ $assigned->funds->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 flex-1">{{ $assigned->funds->designation ?? '-' }}</p>
                </div>
            </div>

            <div class="w-full flex items-center">
                <p class="w-48">Approved:</p>
                <div class="flex flex-col">
                    <p class="font-bold flex-1">{{ $assigned->approved->name ?? '-' }}</p>
                    <p class="text-xs text-gray-600 flex-1">{{ $assigned->approved->designation ?? '-' }}</p>
                </div>
            </div>

        </div>

    </div>
</div>
</div>
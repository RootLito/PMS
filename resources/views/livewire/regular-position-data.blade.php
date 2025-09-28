<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            POSITION
        </h2>
    </div>
    <div class="flex-1 flex flex-col gap-10">
        <div class="p-6 bg-white rounded-xl shadow">
            <form wire:submit.prevent="save" class="w-full flex flex-col">
                <h2 class="text-xl text-gray-700 font-bold mb-6">
                    Add Position
                </h2>

                <div class="w-full flex gap-2 flex-wrap">
                    <div class="flex-1">
                        <label for="name" class="block text-sm text-gray-700">
                            Position<span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" wire:model.defer="name"
                            class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                        @error('name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mt-auto" style="width:400px;">
                        <button
                            class="w-full bg-slate-700 text-white h-10 rounded-md hover:bg-slate-500 cursor-pointer">
                            Save
                        </button>
                        @if ($errors->any())
                            <span class="text-red-500 text-xs">Error occurred</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="flex-1 flex flex-col p-6 bg-white rounded-xl shadow">
            <div class="w-full flex justify-between">
                <h2 class="text-xl text-gray-700 font-bold mb-6">
                    Position List
                </h2>
                <input type="text" id="search" placeholder="Search Position" wire:model.live="search"
                    class="w-1/2 h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
            </div>


            <table class="min-w-full table-auto text-sm mt-2">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 text-nowrap" width="50%">Designation</th>
                        <th class="px-4 py-3 text-nowrap" width="35%">Date Added</th>
                        <th class="px-4 py-2 text-nowrap" width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $position)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            @if ($editingId === $position->id)
                                <td class="px-4 py-3" colspan="1">
                                    <input type="text" wire:model.defer="editName"
                                        class="block w-full py-1 border border-gray-200 bg-gray-50 rounded-md px-2" />
                                    @error('editName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="px-4 py-3">{{ $position->created_at->format('F j, Y') }}</td>
                                <td class="px-4 py-2 flex gap-2 items-center">
                                    <button wire:click="cancelEdit"
                                        class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer"
                                        title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button wire:click="updatePosition"
                                        class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                        title="Save">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                            @elseif ($deletingId === $position->id)
                                <td class="px-4 py-3">{{ $position->name }}</td>
                                <td class="px-4 py-3 font-bold">{{ $position->created_at->format('F j, Y') }}</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button wire:click="cancelDelete"
                                        class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer"
                                        title="Cancel Delete">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button wire:click="deletePositionConfirmed"
                                        class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                        title="Confirm Delete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                            @else
                                <td class="px-4 py-3">{{ $position->name }}</td>
                                <td class="px-4 py-3">{{ $position->created_at->format('F j, Y') }}</td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button wire:click="edit({{ $position->id }}, '{{ $position->name }}')"
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-400 cursor-pointer">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $position->id }})"
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-400 cursor-pointer">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


            @if ($positions->hasPages())
                <div class="w-full flex justify-between items-end mt-auto">
                    <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                        @php
                            $from = $positions->firstItem();
                            $to = $positions->lastItem();
                            $total = $positions->total();
                        @endphp
                        Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
                    </div>
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                        <ul class="inline-flex items-center space-x-1 select-none">
                            @if ($positions->onFirstPage())
                                <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                            @else
                                <li>
                                    <button wire:click="previousPage"
                                        class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                                </li>
                            @endif

                            @php
                                $current = $positions->currentPage();
                                $last = $positions->lastPage();

                                if ($current == 1) {
                                    $start = 1;
                                    $end = min(3, $last);
                                } elseif ($current == $last) {
                                    $start = max($last - 2, 1);
                                    $end = $last;
                                } else {
                                    $start = max($current - 1, 1);
                                    $end = min($current + 1, $last);
                                }
                            @endphp
                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $current)
                                    <li class="bg-slate-700 text-white px-4 py-2 rounded cursor-default">
                                        {{ $page }}
                                    </li>
                                @else
                                    <li>
                                        <button wire:click="gotoPage({{ $page }})"
                                            class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer">{{ $page }}</button>
                                    </li>
                                @endif
                            @endfor

                            @if ($positions->hasMorePages())
                                <li>
                                    <button wire:click="nextPage"
                                        class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&gt;</button>
                                </li>
                            @else
                                <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&gt;</li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

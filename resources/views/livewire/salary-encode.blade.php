<div class="h-full flex-1 flex gap-10 ">
    <div class="h-full flex flex-col w-100 p-6 rounded-xl bg-white">
        <h1 class="text-xl text-gray-700 font-bold mb-2">{{ $isUpdating ? 'Update Salary' : 'Create Salary' }}</h1>

        <form wire:submit.prevent="save" class="w-full flex flex-col">
            <div class="mt-2">
                <label for="monthly_rate" class="block text-sm text-gray-700">Monthly Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="monthly_rate" wire:model.live="monthly_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01">
                @error('monthly_rate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="daily_rate" class="block text-sm text-gray-700">Daily Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="daily_rate" wire:model.live="daily_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('daily_rate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="halfday_rate" class="block text-sm text-gray-700">Halfday Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="halfday_rate" wire:model.live="halfday_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('halfday_rate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="hourly_rate" class="block text-sm text-gray-700">Hourly Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="hourly_rate" wire:model.live="hourly_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('hourly_rate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-2">
                <label for="per_min_rate" class="block text-sm text-gray-700">Per Minute Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="per_min_rate" wire:model.live="per_min_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('per_min_rate')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                {{ $isUpdating ? 'Update' : 'Save' }}
            </button>
        </form>

    </div>

    <div class="flex-1 flex flex-col p-6 rounded-xl bg-white h-full overflow-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-700">Salary Records</h2>
        <div class="max-h-[400px] overflow-auto">
            <table class="min-w-full mt-4 table-auto text-sm">
                <thead class="bg-gray-100 text-left text-gray-600">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 text-nowrap">Monthly</th>
                        <th class="px-4 py-2 text-nowrap">Daily</th>
                        <th class="px-4 py-2 text-nowrap">Halfday</th>
                        <th class="px-4 py-2 text-nowrap">Hourly</th>
                        <th class="px-4 py-2 text-nowrap">Per Minute</th>
                        <th class="px-4 py-2 text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salaries as $salary)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="px-4 py-2">{{ number_format($salary->monthly_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->daily_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->halfday_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->hourly_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->per_min_rate, 2) }}</td>

                            @if ($deletingId === $salary->id)
                                <td class="px-4 py-2 flex gap-2">
                                    <button wire:click="cancelDelete"
                                        class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer"
                                        title="Cancel Delete">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button wire:click="deleteConfirmed"
                                        class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                        title="Confirm Delete">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </td>
                            @else
                                <td class="px-4 py-2 text-nowrap flex gap-2 items-center">
                                    <button wire:click="edit({{ $salary->id }})"
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 cursor-pointer"
                                        title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <button wire:click="confirmDelete({{ $salary->id }})"
                                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 cursor-pointer"
                                        title="Delete">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        @if ($salaries->hasPages())
            <div class="w-full flex justify-between items-end mt-auto">
                <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                    @php
                        $from = $salaries->firstItem();
                        $to = $salaries->lastItem();
                        $total = $salaries->total();
                    @endphp
                    Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
                </div>
                <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                    <ul class="inline-flex items-center space-x-1 select-none">
                        @if ($salaries->onFirstPage())
                            <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                        @else
                            <li>
                                <button wire:click="previousPage"
                                    class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                            </li>
                        @endif

                        @php
                            $current = $salaries->currentPage();
                            $last = $salaries->lastPage();

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

                        @if ($salaries->hasMorePages())
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

@if (session()->has('message'))
    <div class="mt-4 text-green-600">
        {{ session('message') }}
    </div>
@endif

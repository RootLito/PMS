<div class="flex-1 flex flex-col bg-white rounded-xl p-6 shadow">

    <div class="flex justify-between mb-4 gap-2 mt-4">
        <input type="text" placeholder="Search file"
            class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-full sm:w-1/2" wire:model.live="search">
        <div class="flex gap-2">
            <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-full" wire:model.live="designation">
                <option value="">Cutoff</option>
                <option value="">1-15</option>
                <option value="">16-31</option>
            </select>
            <select wire:model.live="sortOrder" class="shadow-sm border rounded border-gray-200 px-4 py-2 w-52">
                <option value="">Date Saved</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select>
        </div>
    </div>
    <div class="overflow-auto mt-6 mb-2">
        @php
            $files = [
                (object) [
                    'id' => 1,
                    'filename' => '',
                    'cutoff' => '2025-01-31',
                    'date_saved' => '2025-02-01 10:30:00',
                ],
                (object) [
                    'id' => 2,
                    'filename' => '',
                    'cutoff' => '2025-02-28',
                    'date_saved' => '2025-03-01 11:15:00',
                ],
                (object) [
                    'id' => 3,
                    'filename' => '',
                    'cutoff' => '2025-03-31',
                    'date_saved' => '2025-04-01 09:45:00',
                ],
            ];
        @endphp

        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap" width="45%">Filename</th>
                    <th class="px-4 py-3 text-nowrap" width="20%">Cut-off</th>
                    <th class="px-4 py-2 text-nowrap" width="20%">Date Saved</th>
                    <th class="px-4 py-2 text-nowrap" width="15%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2">1 COS Payroll 2025 - Region XI_February.xlsx</td>
                    <td class="px-4 py-2">1-15</td>
                    <td class="px-4 py-2">2025-02-01 10:30:00</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ url('/files/view', ['id' => 1]) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="View">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ url('/files/download', ['id' => 1]) }}"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="Download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2">1 COS Payroll 2025 - Region XI_March.xlsx</td>
                    <td class="px-4 py-2">1-15</td>
                    <td class="px-4 py-2">2025-03-01 11:15:00</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ url('/files/view', ['id' => 2]) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="View">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ url('/files/download', ['id' => 2]) }}"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="Download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </td>
                </tr>
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2">1 COS Payroll 2025 - Region XI_April.xlsx</td>
                    <td class="px-4 py-2">1-15</td>
                    <td class="px-4 py-2">2025-04-01 09:45:00</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ url('/files/view', ['id' => 3]) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="View">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ url('/files/download', ['id' => 3]) }}"
                            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1"
                            title="Download">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    {{-- @if ($employees->hasPages())
        <div class="w-full flex justify-between items-end">
            <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                @php
                    $from = $employees->firstItem();
                    $to = $employees->lastItem();
                    $total = $employees->total();
                @endphp
                Showing {{ $from }} to {{ $to }} of {{ number_format($total) }} results
            </div>
            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-4 text-xs">
                <ul class="inline-flex items-center space-x-1 select-none">
                    @if ($employees->onFirstPage())
                        <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                    @else
                        <li>
                            <button wire:click="previousPage"
                                class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                        </li>
                    @endif

                    @php
                        $current = $employees->currentPage();
                        $last = $employees->lastPage();

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
                            <li class="bg-slate-700 text-white px-4 py-2 rounded cursor-default">{{ $page }}
                            </li>
                        @else
                            <li>
                                <button wire:click="gotoPage({{ $page }})"
                                    class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer">{{ $page }}</button>
                            </li>
                        @endif
                    @endfor

                    @if ($employees->hasMorePages())
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
    @endif --}}
</div>

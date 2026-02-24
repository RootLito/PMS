<div class="flex-1 h-full flex flex-col gap-10 p-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            CONTRIBUTION
        </h2>
    </div>

    <div class="flex-1 flex flex-col p-6 bg-white rounded-xl shadow">
        <div class="w-full flex justify-between">
            <h2 class="text-xl text-gray-700 font-bold mb-6">
                Employees with more than minimum contribution
            </h2>
            <input type="text" id="search" placeholder="Search Employee" wire:model.live="search"
                class="w-1/2 h-10 border border-gray-200 bg-gray-50 rounded-md px-2 text-sm">
        </div>

        <table class="min-w-full table-auto text-sm mt-10">
            <thead class="bg-gray-100 text-left">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap" width="50%">Name</th>
                    <th class="px-4 py-3 text-nowrap" width="25%">Pag-ibig Excess</th>
                    <th class="px-4 py-3 text-nowrap" width="25%">SSS Excess</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-3">
                            {{ $employee->last_name }}, {{ $employee->first_name }}
                            @if (!empty($employee->suffix))
                                {{ $employee->suffix }}
                            @endif
                        </td>

                        {{-- Pag-ibig Excess: (ee_share - 400) --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $employee->display_hdmf > 0 ? number_format($employee->display_hdmf, 2) : '-' }}
                        </td>

                        {{-- SSS Excess: (amount + ec - 760) --}}
                        <td class="px-4 py-3 text-gray-700">
                            {{ $employee->display_sss > 0 ? number_format($employee->display_sss, 2) : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($employees->hasPages())
            <div class="w-full flex justify-between items-end mt-auto">
                <div class="text-gray-600 mt-2 text-xs select-none">
                    Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of
                    {{ number_format($employees->total()) }} results
                </div>
                <nav role="navigation" class="flex justify-center mt-4 text-xs">
                    <ul class="inline-flex items-center space-x-1 select-none">
                        @if ($employees->onFirstPage())
                            <li class="text-gray-400 px-4 py-2">&lt;</li>
                        @else
                            <li><button wire:click="previousPage"
                                    class="px-4 py-2 rounded bg-white shadow-sm border border-gray-200 hover:bg-gray-100">&lt;</button>
                            </li>
                        @endif

                        @foreach ($employees->getUrlRange(max(1, $employees->currentPage() - 1), min($employees->lastPage(), $employees->currentPage() + 1)) as $page => $url)
                            @if ($page == $employees->currentPage())
                                <li class="bg-slate-700 text-white px-4 py-2 rounded">{{ $page }}</li>
                            @else
                                <li><button wire:click="gotoPage({{ $page }})"
                                        class="px-4 py-2 rounded hover:bg-gray-200">{{ $page }}</button></li>
                            @endif
                        @endforeach

                        @if ($employees->hasMorePages())
                            <li><button wire:click="nextPage"
                                    class="px-4 py-2 rounded bg-white shadow-sm border border-gray-200 hover:bg-gray-100">&gt;</button>
                            </li>
                        @else
                            <li class="text-gray-400 px-4 py-2">&gt;</li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>

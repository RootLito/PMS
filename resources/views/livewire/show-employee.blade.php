<div class="flex-1 flex flex-col bg-white rounded-xl p-6 shadow">

    <div class="flex justify-between mb-4 gap-2 mt-4">
        <input type="text" placeholder="Search by name..."
            class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-full sm:w-1/2" wire:model.live="search">
        <div class="flex gap-2">
            <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-full" wire:model.live="designation">
                <option value="">All Designations</option>
                @foreach ($designations as $desig)
                    <option value="{{ $desig }}">{{ $desig }}</option>
                @endforeach
            </select>
            <select wire:model.live="sortOrder" class="shadow-sm border rounded border-gray-200 px-4 py-2 w-32">
                <option value="">Sort By</option>
                <option value="asc">A-Z</option>
                <option value="desc">Z-A</option>
            </select>
        </div>
    </div>
    <div class="overflow-auto mt-6 mb-2">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap">Status</th>
                    <th class="px-4 py-2 text-nowrap">Last Name</th>
                    <th class="px-4 py-2 text-nowrap">First Name</th>
                    <th class="px-4 py-2 text-nowrap">M.I.</th>
                    <th class="px-4 py-2 text-nowrap">Monthly Rate</th>
                    <th class="px-4 py-2 text-nowrap">Gross</th>
                    <th class="px-4 py-2 text-nowrap">Designation</th>
                    <th class="px-4 py-2 text-nowrap">Office</th>
                    <th class="px-4 py-2 text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer">
                        <td class="px-4 py-2 font-bold">{{ $employee->employment_status }}</td>
                        <td class="px-4 py-2">{{ $employee->last_name }}</td>
                        <td class="px-4 py-2">
                            {{ $employee->first_name }}{{ $employee->suffix ? ' ' . $employee->suffix . '.' : '' }}
                        </td>
                        <td class="px-4 py-2">
                            @if (!empty($employee->middle_initial))
                                {{ strtoupper(substr($employee->middle_initial, 0, 1)) }}.
                            @else
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ number_format($employee->monthly_rate, 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($employee->gross, 2) }}</td>
                        <td class="px-4 py-2">{{ $employee->designation }}</td>
                        <td class="px-4 py-2">{{ $employee->office_name }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            @if ($deletingId === $employee->id)
                                <button wire:click="cancelDelete"
                                    class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600 cursor-pointer"
                                    title="Cancel Delete">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button wire:click="deleteEmployeeConfirmed"
                                    class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600 cursor-pointer"
                                    title="Confirm Delete">
                                    <i class="fas fa-check"></i>
                                </button>
                            @else
                                <a href="{{ url('/employee/update', ['id' => $employee->id]) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer"
                                    title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('employee.payslip', $employee->id) }}"
                                    class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded cursor-pointer flex items-center gap-1"
                                    title="View Payslip">
                                    <i class="fas fa-file-invoice"></i> Payslip
                                </a>

                                <button wire:click="confirmDelete({{ $employee->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer"
                                    title="Delete">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-gray-500">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($employees->hasPages())
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
    @endif
</div>

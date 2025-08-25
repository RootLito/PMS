<div class="flex-1 grid grid-cols-2 gap-10">
    <div class="flex flex-col bg-white rounded-xl p-6">
        <div class="flex justify-between mb-4 gap-2 mt-2">
            <input type="text" placeholder="Search by name..."
                class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-1/2" wire:model.live="search">
            <div class="w-1/2 flex gap-2 justify-end">
                <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-48" wire:model.live="designation">
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

        <div class="overflow-auto mt-6">
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200 text-gray-700">
                        <th class="px-4 py-2">Last Name</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-4 py-2">M.I.</th>
                        <th class="px-4 py-2">Monthly Rate</th>
                        <th class="px-4 py-2">Gross</th>
                        <th class="px-4 py-2">Net Pay</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr
                            class="border-b border-gray-200 cursor-pointer {{ $selectedEmployee === $employee->id ? 'bg-gray-300' : '' }}">
                            <td class="px-4 py-2">{{ $employee->last_name }}</td>
                            <td class="px-4 py-2">
                                {{ $employee->first_name }}{{ $employee->suffix ? ' ' . $employee->suffix . '.' : '' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $employee->middle_initial }}
                                @if (!empty($employee->middle_initial))
                                    .
                                @endif
                            </td>

                            <td class="px-4 py-2">{{ number_format($employee->monthly_rate, 2) }}</td>
                            <td class="px-4 py-2 font-black text-gray-700">{{ number_format($employee->gross, 2) }}</td>
                            <td class="px-4 py-2 font-black text-gray-700">{{ number_format($employee->gross, 2) }}</td>
                            <td class="px-4 py-2">

                                <button wire:click="employeeSelected({{ $employee->id }})"
                                    class="bg-green-700 hover:bg-green-800 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer">
                                    {{ $selectedEmployee === $employee->id ? 'Selected' : 'Select' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="w-full flex justify-between items-end mt-auto">
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

    <div class="flex flex-col gap-10">
        <form class="flex-1 flex flex-col bg-white rounded-xl p-6" wire:submit.prevent="saveCalculation">
            @if ($employeeName)
                <div class="px-2 py-1 rounded bg-gray-500 ">
                    <p class="font-semibold text-white uppercase"> {{ $employeeName }} </p>
                </div>
            @endif
            <h2 class="text-2xl text-gray-700 font-bold">Deduction</h2>

            <p>
                @if ($selectedEmployee)
                    @if ($matchedRate)
                        <span class="mr-2 text-sm">Monthly Rate : {{ number_format($monthly_rate, 2) }}</span>
                        <span class="mr-2 text-sm">Daily: {{ number_format($matchedRate['daily'], 2) }}</span>
                        <span class="mr-2 text-sm">Halfday: {{ number_format($matchedRate['halfday'], 2) }}</span>
                        <span class="mr-2 text-sm">Hourly: {{ number_format($matchedRate['hourly'], 2) }}</span>
                        <span class="mr-2 text-sm">Per Minute: {{ number_format($matchedRate['per_min'], 2) }}</span>
                    @else
                        <span class="mr-2 text-sm">No rate matched.</span>
                    @endif
                @else
                    <span class="mr-2 text-sm">Please select an employee.</span>
                @endif
            </p>



            <div class="w-full grid grid-cols-4 gap-2 mt-2">
                <div class="flex flex-col">
                    <label for="daily" class="block text-sm text-gray-700">
                        Daily
                    </label>
                    <input id="daily" wire:model.live="daily" type="number" min="0" step="0.01"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>

                <div class="flex flex-col">
                    <label for="amount" class="block text-sm text-gray-700">
                        Amount
                    </label>
                    <input id="amount" wire:model.live="amount" type="number" disabled
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2">
                </div>

                <div class="flex flex-col">
                    <label for="minutes" class="block text-sm text-gray-700">
                        Minutes
                    </label>
                    <input id="minutes" wire:model.live="minutes" type="number" min="0"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>

                <div class="flex flex-col">
                    <label for="min_amount" class="block text-sm text-gray-700">
                        Amount
                    </label>
                    <input id="min_amount" wire:model.live="min_amount" type="number"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" disabled>
                </div>
            </div>

            <div class="w-full grid grid-cols-4 gap-2 mt-2">
                <div class="flex flex-col">
                    <label for="total" class="block text-sm text-gray-700">
                        TOTAL
                    </label>
                    <input id="total" wire:model="total" type="number" disabled
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2">
                </div>

                <div class="flex flex-col">
                    <label for="net_late_absences" class="block text-sm text-gray-700">
                        NET OF LATE/ABSENCES
                    </label>
                    <input id="net_late_absences" wire:model="net_late_absences" type="number" disabled
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2">
                </div>

                <div class="flex flex-col">
                    <label for="adjustment" class="block text-sm text-gray-700">
                        ADJUSTMENTS
                    </label>
                    <input id="adjustment" wire:model.live="adjustment" type="number"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
                <div class="flex flex-col">
                    <label for="tax" class="block text-sm text-gray-700">
                        TAX
                    </label>
                    <input id="tax" wire:model.live="tax" type="number"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>


            <div class="w-full flex gap-2">
                <div class="w-full flex flex-col mt-2">
                    <label for="remarks" class="block text-sm text-gray-700 mt-auto">
                        Remarks
                    </label>
                    <input id="remarks" wire:model.live="remarks" type="text"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <h2 class="text-2xl text-gray-700 font-bold">Contribution</h2>
                <select wire:model.live="cutoff" class="h-10 border border-gray-200 shadow-sm rounded-md px-2">
                    <option value="" disabled>Select Cutoff</option>
                    <option value="1-15">1st Cutoff (1-15)</option>
                    <option value="16-31">2nd Cutoff (16-31)</option>
                </select>
            </div>

            @if ($cutoff)
                <div class="">
                    @foreach ($fields as $field)
                        <div class="flex items-center">
                            <label for="{{ $field['model'] }}" class="text-sm text-gray-700 w-24">
                                {{ $field['label'] }}
                            </label>
                            <input type="number" step="0.01" id="{{ $field['model'] }}"
                                wire:model.live="{{ $field['model'] }}"
                                class="flex-1 mt-2 h-9 border border-gray-200 bg-gray-50 rounded-md px-2" disabled>
                        </div>
                    @endforeach
                </div>
            @endif


            <div class="flex flex-col w-full mt-auto gap-4 ">
                <div class="w-full px-6">
                    <div class="w-full flex justify-between mt-2">
                        <p class="text-sm text-gray-700 font-bold">NET PAY</p>
                        <p class="text-sm text-gray-700 font-bold">CUTOFF</p>
                    </div>

                    <div class="w-full flex justify-between items-end">
                        <h2 class="text-2xl">₱
                            <?php echo number_format((float) $this->net_pay, 2); ?>
                        </h2>

                        <h2>{{ $currentCutoffLabel }}</h2>
                    </div>
                </div>

                @php
                    $isDisabled = is_null($selectedEmployee);
                @endphp

                <button class="w-full h-10 bg-slate-700 rounded-md text-white {{ $isDisabled ? 'cursor-default opacity-50' : 'cursor-pointer' }}"
                    @disabled($isDisabled)>
                    CONFIRM
                </button>
            </div>
        </form>
    </div>
</div>

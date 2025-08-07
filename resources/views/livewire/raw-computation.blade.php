<div class="flex-1 grid grid-cols-2 gap-10">
    <div class="flex flex-col bg-white rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 mt-2">
            <input type="text" placeholder="Search by name..."
                class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-full sm:w-1/2" wire:model.live="search">
            <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-full sm:w-1/4"
                wire:model.live="designation">
                <option value="">All Designations</option>
                @foreach ($designations as $desig)
                    <option value="{{ $desig }}">{{ $desig }}</option>
                @endforeach
            </select>
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
                        <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer">
                            <td class="px-4 py-2">{{ $employee->last_name }}</td>
                            <td class="px-4 py-2">
                                {{ $employee->first_name }}{{ $employee->suffix ? ' ' . $employee->suffix . '.' : '' }}
                            </td>
                            <td class="px-4 py-2">{{ $employee->middle_initial }}</td>
                            <td class="px-4 py-2">{{ number_format($employee->monthly_rate, 2) }}</td>
                            <td class="px-4 py-2 font-black text-gray-700">{{ number_format($employee->gross, 2) }}</td>
                            <td class="px-4 py-2 font-black text-gray-700">{{ number_format($employee->gross, 2) }}</td>
                            <td class="px-4 py-2">
                                <button wire:click="employeeSelected({{ $employee->id }})"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer">
                                    Select
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-auto">
            {{-- <div class="text-sm text-gray-600 mb-2">
                Page {{ $employees->currentPage() }} of {{ $employees->lastPage() }}
            </div>
            {{ $employees->links('pagination::simple-tailwind') }} --}}
            {{ $employees->links() }}
        </div>
    </div>

    <div class="flex flex-col gap-10">
        <form class="flex-1 flex flex-col bg-white rounded-xl p-6" wire:submit.prevent="saveCalculation">
            <h2 class="text-2xl text-gray-700 font-bold">Deduction</h2>
            <p>
                @if ($selectedEmployee)
                    @if ($matchedRate)
                        <span class="mr-2">Monthly Rate : {{ number_format($monthly_rate, 2) }}</span>
                        <span class="mr-2">Daily: {{ number_format($matchedRate['daily'], 2) }}</span>
                        <span class="mr-2">Halfday: {{ number_format($matchedRate['halfday'], 2) }}</span>
                        <span class="mr-2">Hourly: {{ number_format($matchedRate['hourly'], 2) }}</span>
                        <span class="mr-2">Per Minute: {{ number_format($matchedRate['per_min'], 2) }}</span>
                    @else
                        No rate matched.
                    @endif
                @else
                    Please select an employee.
                @endif
            </p>



            <div class="w-full grid grid-cols-4 gap-2 mt-2">
                <div class="flex flex-col">
                    <label for="daily" class="block text-sm text-gray-700">
                        Daily
                    </label>
                    <input id="daily" wire:model.live="daily" type="number" min="0" step="0.01"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
    is_null($selectedEmployee) ? 'disabled' : '' }}>
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
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
    is_null($selectedEmployee) ? 'disabled' : '' }}>
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
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
    is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
                <div class="flex flex-col">
                    <label for="tax" class="block text-sm text-gray-700">
                        TAX
                    </label>
                    <input id="tax" wire:model.live="tax" type="number"
                        class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
    is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>


            <div class="w-full flex gap-2">
                <div class="w-full flex flex-col mt-2">
                    <label for="remarks" class="block text-sm text-gray-700 mt-auto">
                        Remarks
                    </label>
                    <input id="remarks" wire:model.live="remarks" type="text"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" {{
    is_null($selectedEmployee) ? 'disabled' : '' }}>
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
                                <input type="number" step="0.01" id="{{ $field['model'] }}" wire:model.live="{{ $field['model'] }}"
                                    class="flex-1 mt-2 h-9 border border-gray-200 bg-gray-50 rounded-md px-2" {{
                        is_null($selectedEmployee) ? 'disabled' : '' }}>
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

                <button
                    class="w-full h-10 bg-slate-700 rounded-md text-white {{ $isDisabled ? 'cursor-default opacity-50' : 'cursor-pointer' }}"
                    {{ $isDisabled ? 'disabled' : '' }}>
                    CONFIRM
                </button>

            </div>
    </div>
</div>
</div>
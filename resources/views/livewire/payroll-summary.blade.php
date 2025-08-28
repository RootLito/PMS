<div class="flex-1 flex-col relative gap-2">
    <div class="w-full flex justify-between items-center mb-6 p-6 bg-white rounded-xl">
        <h2 class="font-black text-gray-700">PAYROLL SUMMARY</h2>
        <div class="flex text-sm gap-2">
            <div class="w-150  relative rounded border border-gray-200 shadow-sm cursor-pointer">
                <div class="truncate h-10 flex items-center px-4 cursor-pointer" wire:click="toggleDesignations">
                    @if (count($designation))
                        {{ implode(', ', $designation) }}
                    @else
                        All Designations
                    @endif
                </div>
                @if ($showDesignations)
                    <div
                        class="w-full absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow p-2 z-10 max-h-60 overflow-y-auto">
                        <div class="w-full flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                            <p class="text-gray-600 text-xs">Select Designation(s)</p>
                            <button wire:click="proceed"
                                class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600 text-xs">Proceed</button>
                        </div>
                        @foreach ($designations as $desig)
                            <label class="block text-sm cursor-pointer">
                                <input type="checkbox" value="{{ $desig }}" wire:model="designation"
                                    class=" cursor-pointer mr-2">
                                {{ $desig }}
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
            <select wire:model.live="cutoff" class="px-2 py-1 rounded border border-gray-200 shadow-sm cursor-pointer">
                <option value="" disabled>Select Cutoff</option>
                <option value="1-15">1st Cutoff (1-15)</option>
                <option value="16-31">2nd Cutoff (16-31)</option>
            </select>

            <select wire:model.live="month" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white ">
                <option value="" disabled>Select Month</option>
                @foreach ($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="year" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                <option value="" disabled>Select Year</option>
                @foreach ($years as $yearOption)
                    <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>{{ $yearOption }}
                    </option>
                @endforeach
            </select>

            <button wire:click.prevent="saveArchive"
                class="bg-green-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-green-600"><i
                    class="fa-solid fa-floppy-disk mr-1"></i> Save to Archive</button>


            <button wire:click.prevent="exportPayroll"
                class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button>
        </div>
    </div>


    <div class="bg-white p-6 min-h-100 rounded-xl">
        @if (count($selectedEmployees) > 0)
            <div class="flex items-center gap-2 justify-end mb-10 ">
                <select id="newDesignation" wire:model.live="newDesignation"
                    class="mt-1 block w-1/2 h-9 border border-gray-200 bg-gray-50 rounded-md px-2">
                    <option value="" disabled>Select designation</option>
                    @foreach ($designations as $designationOption)
                        <option value="{{ $designationOption }}">{{ $designationOption }}</option>
                    @endforeach
                </select>
                <button wire:click="confirmSelected"
                    class="text-xs bg-blue-700 text-white font-semibold px-4 h-9 shadow rounded cursor-pointer hover:bg-blue-600 flex items-center space-x-2">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <span>Transfer</span>
                </button>

                <button wire:click="redirectToEdit"
                    class="text-xs bg-green-700 text-white font-semibold px-4 h-9 shadow rounded cursor-pointer hover:bg-green-600 flex items-center space-x-2">
                    <i class="fas fa-edit mr-2"></i>
                    <span>Update</span>
                </button>

                <button wire:click="deleteSelected"
                    class="text-xs bg-red-700 text-white font-semibold px-4 h-9 shadow rounded cursor-pointer hover:bg-red-600 flex items-center space-x-2">
                    <span><i class="fas fa-trash-alt mr-2"></i> Delete</span>
                </button>
            </div>
        @endif

        <div id="payrollContent">
            <div class="w-full flex justify-center items-center gap-6 mb-2">
                <div class="flex gap-2">
                    <div class="w-20 h-20">
                        <img src="{{ asset('images/bagong_pilipinas.png') }}" alt="Bagong Pilipinas"
                            class="object-contain w-full h-full">
                    </div>
                    <div class="w-20 h-20">
                        <img src="{{ asset('images/bfar.png') }}" alt="BFAR" class="object-contain w-full h-full">
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs">Republic of the Philippines</p>
                    <p class="text-xs">Department of Agriculture </p>
                    <h2 class="font-bold">BUREAU OF FISHERIES AND AQUATIC RESOURCES</h2>
                    <p class="text-xs">Region XI, R. Magsaysay Ave., Davao City</p>
                </div>
                <div class="w-20 h-20">
                    <img src="{{ asset('images/gad.png') }}" alt="GAD" class="object-contain w-full h-full">
                </div>
            </div>
            <p class="text-xs font-bold">CONTRACT OF SERVICES / JOB ORDER</p>

            <h2 class="font-bold">{{ $dateRange }}</h2>
            
            <table class="table-auto border-collapse w-full text-xs"
                style="font-size: 10px; font-family: 'Arial Narrow';">
                <thead>
                    <tr>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Select</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">No.</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">PAP</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Name of Employee</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Monthly Rate</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Gross</th>
                        <th colspan="2" class="border border-gray-300 px-2 py-1 text-center">Late/Absences</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Total</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Net of Late/Absences</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Tax</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Net of Tax</th>
                        <th colspan="5" class="border border-gray-300 px-2 py-1 text-center">Contributions</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Total Deductions</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Net Pay</th>
                    </tr>
                    <tr>
                        <th class="border border-gray-300 px-2 py-1">Absent</th>
                        <th class="border border-gray-300 px-2 py-1">Late/Undertime</th>
                        @foreach ($cutoffFields as $field)
                            <th class="border border-gray-300 px-2 py-1">{{ $field['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groupedEmployees as $designation => $group)
                        {{-- DESIGNATION  --}}
                        <tr class="bg-green-200 font-bold border border-gray-300">
                            <td></td>
                            <td></td>
                            <td style="font-size: 10px">
                                {{ $group['designation_pap'] ?? '' }}
                            </td>
                            <td class="px-2 py-1" style="font-size: 10px">{{ strtoupper($designation) }}</td>
                            <td></td>
                            <td class=" py-1 text-right px-2">
                                {{ number_format($totalPerVoucher[$designation]['totalGross'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalAbsent'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsent'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalLateUndertime'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalLateUndertime'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalAbsentLate'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsentLate'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetLateAbsences'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetLateAbsences'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTax'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetTax'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfPi'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfPi'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMpl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMpl'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMp2'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMp2'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfCl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfCl'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalDareco'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalDareco'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalTotalDeduction'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTotalDeduction'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetPay'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetPay'], 2) }}
                            </td>
                        </tr>


                        {{-- OFFICE  --}}
                        @foreach ($group['offices'] as $officeName => $officeGroup)
                            @foreach ($group['offices'] as $office)
                                <tr class="bg-gray-200 font-semibold border border-gray-300">
                                    <td></td>
                                    <td></td>
                                    <td class="px-2 py-2 font-bold" style="font-size: 10px">
                                        {{ $office['office_code'] }}
                                    </td>
                                    <td colspan="" class="px-2 py-2 font-bold" style="font-size: 10px">
                                        {{ $office['office_name'] }}
                                    </td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($office['totalGross'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalAbsent'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalLateUndertime'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalAbsentLate'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalNetLateAbsences'], 2) }}
                                    </td>
                                    <td class="text-right">{{ number_format($office['totalTax'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalNetTax'], 2) }}</td>

                                    @if ($cutoff === '1-15')
                                        <td class="text-right">{{ number_format($office['totalHdmfPi'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalHdmfMpl'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalHdmfMp2'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalHdmfCl'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalDareco'], 2) }}</td>
                                    @elseif($cutoff === '16-31')
                                        <td class="text-right">{{ number_format($office['totalSsCon'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalEcCon'], 2) }}</td>
                                        <td class="text-right">{{ number_format($office['totalWisp'], 2) }}</td>
                                        <td></td>
                                        <td></td>
                                    @endif

                                    <td class="text-right">{{ number_format($office['totalTotalDeduction'], 2) }}</td>
                                    <td class="text-right">{{ number_format($office['totalNetPay'], 2) }}</td>
                                </tr>
                            @endforeach



                            {{-- OFFICE DATA  --}}
                            @foreach ($officeGroup['employees'] as $index => $employee)
                                @php
                                    $rc = $employee->rawCalculation;
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 px-2 py-1 text-center"><input type="checkbox"
                                            wire:model.live="selectedEmployees" value="{{ $employee->id }}"></td>
                                    <td class="border border-gray-300 px-2 py-1">{{ $loop->iteration }}</td>
                                    <td class="border border-gray-300 px-2 py-1"></td>
                                    <td class="border border-gray-300 px-2 py-2" width="300">
                                        {{ $employee->first_name }}
                                        @if (!empty($employee->middle_initial))
                                            {{ strtoupper(substr($employee->middle_initial, 0, 1)) }}.
                                        @endif
                                        {{ $employee->last_name }}
                                        @if (!empty($employee->suffix))
                                            {{ $employee->suffix }}
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ number_format($employee->monthly_rate, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ number_format($employee->gross, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->absent == null || $rc->absent == 0 ? '-' : number_format($rc->absent, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->late_undertime == null || $rc->late_undertime == 0 ? '-' : number_format($rc->late_undertime, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->total_absent_late == null || $rc->total_absent_late == 0
                                            ? '-'
                                            : number_format($rc->total_absent_late, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->net_late_absences == null || $rc->net_late_absences == 0
                                            ? '-'
                                            : number_format($rc->net_late_absences, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->tax == null || $rc->tax == 0 ? '-' : number_format($rc->tax, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->net_tax == null || $rc->net_tax == 0 ? '-' : number_format($rc->net_tax, 2) }}
                                    </td>
                                    @if ($cutoff === '1-15')
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->hdmf_pi == null || $rc->hdmf_pi == 0 ? '-' : number_format($rc->hdmf_pi, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->hdmf_mpl == null || $rc->hdmf_mpl == 0 ? '-' : number_format($rc->hdmf_mpl, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->hdmf_mp2 == null || $rc->hdmf_mp2 == 0 ? '-' : number_format($rc->hdmf_mp2, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->hdmf_cl == null || $rc->hdmf_cl == 0 ? '-' : number_format($rc->hdmf_cl, 2) }}
                                        </td>

                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->dareco == null || $rc->dareco == 0 ? '-' : number_format($rc->dareco, 2) }}
                                        </td>
                                    @elseif($cutoff === '16-31')
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->ss_con == null || $rc->ss_con == 0 ? '-' : number_format($rc->ss_con, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->ec_con == null || $rc->ec_con == 0 ? '-' : number_format($rc->ec_con, 2) }}
                                        </td>

                                        <td class="border border-gray-300 px-2 py-1 text-right">
                                            {{ $rc->wisp == null || $rc->wisp == 0 ? '-' : number_format($rc->wisp, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-2 py-1 text-right"></td>
                                        <td class="border border-gray-300 px-2 py-1 text-right"></td>
                                    @endif
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->total_deduction == null || $rc->total_deduction == 0 ? '-' : number_format($rc->total_deduction, 2) }}
                                    </td>
                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                        {{ $rc->net_pay == null || $rc->net_pay == 0 ? '-' : number_format($rc->net_pay, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach





                        {{-- ENDLOOP   --}}
                        <tr class="bg-yellow-300 font-bold border border-gray-300">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2 py-1 text-center text-red-600">Total</td>
                            <td></td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ number_format($totalPerVoucher[$designation]['totalGross'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalAbsent'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsent'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalLateUndertime'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalLateUndertime'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalAbsentLate'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsentLate'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalNetLateAbsences'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetLateAbsences'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTax'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalNetTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetTax'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalHdmfPi'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfPi'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMpl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMpl'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMp2'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMp2'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalHdmfCl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfCl'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalDareco'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalDareco'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalTotalDeduction'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTotalDeduction'], 2) }}
                            </td>
                            <td class="px-2 py-1 text-right text-red-600">
                                {{ ($totalPerVoucher[$designation]['totalNetPay'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetPay'], 2) }}
                            </td>
                        </tr>
                        <tr class="border-x border-gray-300">
                            <td class="invisible">space</td>
                        </tr>
                        <tr class="bg-blue-400 font-bold border border-gray-300">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2 py-3 text-left ">GRAND</td>
                            <td></td>
                            <td class=" py-1 text-right px-2">
                                {{ number_format($totalPerVoucher[$designation]['totalGross'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalAbsent'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsent'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalLateUndertime'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalLateUndertime'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalAbsentLate'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalAbsentLate'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetLateAbsences'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetLateAbsences'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTax'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetTax'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetTax'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfPi'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfPi'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMpl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMpl'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfMp2'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfMp2'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalHdmfCl'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalHdmfCl'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalDareco'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalDareco'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalTotalDeduction'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalTotalDeduction'], 2) }}
                            </td>
                            <td class=" py-1 text-right px-2">
                                {{ ($totalPerVoucher[$designation]['totalNetPay'] ?? 0) == 0
                                    ? '-'
                                    : number_format($totalPerVoucher[$designation]['totalNetPay'], 2) }}
                            </td>
                        </tr>
                        <tr class="border-x border-t border-gray-300">
                            <td></td>
                            <td></td>
                            <td class="px-2 ">Prepared:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2" colspan="2">Checked and Noted by:</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2" colspan="3">Funds Availability:</td>
                            <td></td>
                            <td class="px-2" colspan="2">Approved:</td>
                        </tr>
                        <tr class="border-x border-gray-300">
                            <td class="invisible">space</td>
                        </tr>
                        <tr class="border-x border-gray-300">
                            <td></td>
                            <td></td>
                            <td class="px-2 pt-3 pb-2 font-bold" colspan="2">
                                <u>{{ $assigned->prepared->name ?? '-' }}</u>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2 pt-3 pb-2 font-bold" colspan="2">
                                <u>{{ $assigned->noted->name ?? '-' }}</u>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2 pt-3 pb-2 font-bold" colspan="4">
                                <u>{{ $assigned->funds->name ?? '-' }}</u>
                            </td>
                            <td class="px-2 pt-3 pb-2 font-bold" colspan="3">
                                <u>{{ $assigned->approved->name ?? '-' }}</u>
                            </td>
                        </tr>
                        <tr class="border-x border-gray-300">
                            <td></td>
                            <td></td>
                            <td class="px-2">{{ $assigned->prepared->designation ?? '-' }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2" colspan="2">{{ $assigned->noted->designation ?? '-' }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="px-2" colspan="4">{{ $assigned->funds->designation ?? '-' }}</td>
                            <td class="px-2" colspan="2">{{ $assigned->approved->designation ?? '-' }}</td>
                        </tr>
                        <tr class="border-x border-b border-gray-300">
                            <td class="invisible">space</td>
                        </tr>


                        {{-- TOTAL --}}
                    @empty
                        <tr class="border-x border-gray-300">
                            <td colspan="18" class="text-center py-4">No payroll data available.</td>
                        </tr>
                    @endforelse


                    <tr class="border-x border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td class="invisible">space</td>
                    </tr>



                    {{-- OVERALL --}}
                    <tr class="border-x border-gray-300 font-bold">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="px-2 bg-orange-300 py-2 text-center">Gross</td>
                        <td class="px-2 bg-orange-300 py-2 text-center"></td>
                        <td class="px-2 bg-orange-300 py-2 text-center"></td>
                        <td class="px-2 bg-orange-300 py-2 text-center">Late/Absences</td>
                        <td class="px-2 bg-orange-300 py-2 text-center"></td>
                        <td class="px-2 bg-orange-300 py-2 text-center">Tax</td>
                        @foreach ($cutoffFields as $field)
                            <td class="px-2 bg-orange-300 py-2 text-center text-nowrap">{{ $field['label'] }}</td>
                        @endforeach
                        <td class="px-2 bg-orange-300 py-2 text-center">Total Ded</td>
                        <td class="px-2 bg-orange-300 py-2 text-center">NET</td>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                    <tr class="border-x  border-gray-300">
                        <td></td>
                        <td></td>
                        <td class="text-right">COS/JO</td>
                        <td class="font-bold text-right">{{ number_format($overallTotal['totalGross'], 2) }}</td>
                        <td class="px-2  py-2 text-center"></td>
                        <td class="px-2 py-2 text-center"></td>

                        <td class="font-bold text-right">{{ number_format($overallTotal['totalAbsentLate'], 2) }}
                        </td>
                        <td></td>
                        <td class="font-bold text-right">{{ number_format($overallTotal['totalTax'], 2) }}</td>

                        @if ($cutoff == '1st')
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfPi'], 2) }}
                            </td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfMpl'], 2) }}
                            </td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfMp2'], 2) }}
                            </td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfCl'], 2) }}
                            </td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalDareco'], 2) }}
                            </td>
                        @endif
                        @if ($cutoff == '2nd')
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalSsCon'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalEcCon'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalWisp'], 2) }}</td>
                            <td></td>
                            <td></td>
                        @endif
                        <td class="font-bold text-right">{{ number_format($overallTotal['totalTotalDeduction'], 2) }}
                        </td>
                        <td class="font-bold text-right px-2">{{ number_format($overallTotal['totalNetPay'], 2) }}
                        </td>
                    </tr>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td></td>
                        <td></td>
                        <td class="text-right">IMEMS</td>
                        <td class="px-2 text-right"></td>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td></td>
                        <td></td>
                        <td class="text-right">NEW</td>
                        <td class="px-2 text-right"></td>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                    <tr class="border-x border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                    <tr class="border-x border-b border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('js/printPayroll.js') }}"></script>
@endpush

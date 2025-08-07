<div class="flex-1 flex-col relative gap-2">
    <div class="w-full flex justify-between items-center mb-6 p-6 bg-white rounded-xl">
        <h2 class="font-black text-gray-700">PAYROLL SUMMARY</h2>
        <div class="flex text-sm gap-2">
            <div class="w-150  relative rounded border border-gray-300 shadow-sm cursor-pointer">
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
            <select wire:model.live="cutoff" class="px-2 py-1 rounded border border-gray-300 shadow-sm cursor-pointer">
                <option value="" disabled>Select Cutoff</option>
                <option value="1st">1st Cutoff (1-15)</option>
                <option value="2nd">2nd Cutoff (16-31)</option>
            </select>
            <button class="bg-green-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-green-600"><i
                    class="fa-solid fa-floppy-disk mr-1"></i> Save to Archive</button>
            <button onclick="printPayroll()"
                class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600">
                <i class="fa-regular fa-file-excel mr-1"></i>Export to Excel</button>
        </div>
    </div>


    <div class="bg-white p-6 min-h-100 rounded-xl">
        @if (count($selectedEmployees) > 0)
            <div class="flex items-center gap-2 justify-end mb-10 ">
                <select wire:model="selectedDesignation" class="shadow-sm border rounded border-gray-200 px-4 py-2">
                    <option value="" disabled>Select Voucher</option>
                    @foreach ($designations as $desig)
                        <option value="{{ $desig }}">{{ $desig }}</option>
                    @endforeach
                </select>
                <button wire:click="confirmSelected"
                    class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600">
                    Confirm
                </button>
            </div>
        @endif

        @if (session()->has('success'))
            <div class="text-green-600">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="text-red-600">{{ session('error') }}</div>
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
            <table class="table-auto border-collapse w-full text-xs">
                <thead>
                    <tr>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Select</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">No.</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">PAP</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Name of Employee</th>
                        <th rowspan="2" class="border border-gray-300 px-2 py-1">Monthly Rate</th>
                        {{-- <th rowspan="2" class="border border-gray-300 px-2 py-1">No. of Working Days</th> --}}
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
                    @forelse($groupedEmployees as $designation => $offices)
                                    <tr class="bg-green-200 font-bold border border-gray-300">
                                        <td></td>
                                        <td></td>
                                        {{-- <td>{{ ($office_code) }}</td> --}}
                                        <td>{{ $offices[0]['office_code'] ?? '-' }}</td>
                                        <td colspan="18" class="px-2 py-1">{{ strtoupper($designation) }}</td>
                                    </tr>


                                    {{-- OFFICE  --}}
                                    @foreach($offices as $office => $employees)
                                        @if($employees['employees']->filter(fn($employee) => !empty($employee->office_code))->isNotEmpty())
                                            <tr class="bg-gray-200 font-semibold border border-gray-300">
                                                <td></td>
                                                <td></td>
                                                <td class="px-2 py-2 font-bold">{{ $employees['employees']->first()->office_code }}</td>
                                                <td colspan="18" class="px-2 py-2 font-bold">{{ $office }}</td>
                                            </tr>
                                        @endif
                                        @foreach($employees['employees'] as $index => $employee)
                                                @php
                                                    $rc = $employee->rawCalculation;
                                                @endphp
                                                <tr>
                                                    <td class="border border-gray-300 px-2 py-1 text-center"><input type="checkbox"
                                                            wire:model.live="selectedEmployees" value="{{ $employee->id }}"></td>
                                                    <td class="border border-gray-300 px-2 py-1">{{ $loop->iteration }}</td>
                                                    <td class="border border-gray-300 px-2 py-1"></td>
                                                    <td class="border border-gray-300 px-2 py-2" width="300">
                                                        {{ $employee->first_name }} {{ $employee->middle_initial }} {{ $employee->last_name }} {{
                                            $employee->suffix }}
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">{{
                                            number_format($employee->monthly_rate, 2)
                                                                                                                                    }}
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($employee->gross, 2) }}
                                                    </td>

                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->absent == null || $rc->absent == 0 ? '-' : number_format($rc->absent, 2) }}
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->late_undertime == null || $rc->late_undertime == 0 ? '-' :
                                            number_format($rc->late_undertime, 2) }}
                                                    </td>

                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->total_absent_late == null || $rc->total_absent_late == 0 ? '-' :
                                            number_format($rc->total_absent_late, 2) }}
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->net_late_absences == null || $rc->net_late_absences == 0 ? '-' :
                                            number_format($rc->net_late_absences, 2) }}
                                                    </td>

                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->tax == null || $rc->tax == 0 ? '-' : number_format($rc->tax, 2) }}
                                                    </td>
                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->net_tax == null || $rc->net_tax == 0 ? '-' : number_format($rc->net_tax, 2) }}
                                                    </td>

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
                                                    <td class="border border-gray-300 px-2 py-1 text-right">
                                                        {{ $rc->total_deduction == null || $rc->total_deduction == 0 ? '-' :
                                            number_format($rc->total_deduction, 2) }}
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
                                        <td class="px-2 py-1 text-right text-red-600">{{ number_format($employees['totalGross'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalAbsent'] == 0 || $employees['totalAbsent'] == null ? '-' :
                        number_format($employees['totalAbsent'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalLateUndertime'] == 0 || $employees['totalLateUndertime'] == null ? '-' :
                        number_format($employees['totalLateUndertime'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalAbsentLate'] == 0 || $employees['totalAbsentLate'] == null ? '-' :
                        number_format($employees['totalAbsentLate'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalNetLateAbsences'] == 0 || $employees['totalNetLateAbsences'] == null ?
                        '-' :
                        number_format($employees['totalNetLateAbsences'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalTax'] == 0 || $employees['totalTax'] == null ? '-' :
                        number_format($employees['totalTax'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalNetTax'] == 0 || $employees['totalNetTax'] == null ? '-' :
                        number_format($employees['totalNetTax'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalHdmfPi'] == 0 || $employees['totalHdmfPi'] == null ? '-' :
                        number_format($employees['totalHdmfPi'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalHdmfMpl'] == 0 || $employees['totalHdmfMpl'] == null ? '-' :
                        number_format($employees['totalHdmfMpl'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalHdmfMp2'] == 0 || $employees['totalHdmfMp2'] == null ? '-' :
                        number_format($employees['totalHdmfMp2'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalHdmfCl'] == 0 || $employees['totalHdmfCl'] == null ? '-' :
                        number_format($employees['totalHdmfCl'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalDareco'] == 0 || $employees['totalDareco'] == null ? '-' :
                        number_format($employees['totalDareco'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalTotalDeduction'] == 0 || $employees['totalTotalDeduction'] == null ? '-'
                        :
                        number_format($employees['totalTotalDeduction'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right text-red-600">
                                            {{ $employees['totalNetPay'] == 0 || $employees['totalNetPay'] == null ? '-' :
                        number_format($employees['totalNetPay'], 2) }}
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
                                        <td class="px-2 py-1 text-right">{{ number_format($employees['totalGross'], 2) }}</td>
                                        <td class="px-2 py-1 text-right">
                                            {{ $employees['totalAbsent'] == 0 || $employees['totalAbsent'] == null ? '-' :
                        number_format($employees['totalAbsent'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right">
                                            {{ $employees['totalLateUndertime'] == 0 || $employees['totalLateUndertime'] == null ? '-' :
                        number_format($employees['totalLateUndertime'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right">
                                            {{ $employees['totalAbsentLate'] == 0 || $employees['totalAbsentLate'] == null ? '-' :
                        number_format($employees['totalAbsentLate'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right">
                                            {{ $employees['totalNetLateAbsences'] == 0 || $employees['totalNetLateAbsences'] == null ?
                        '-' :
                        number_format($employees['totalNetLateAbsences'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalTax'] == 0 || $employees['totalTax'] == null ? '-' :
                        number_format($employees['totalTax'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right">
                                            {{ $employees['totalNetTax'] == 0 || $employees['totalNetTax'] == null ? '-' :
                        number_format($employees['totalNetTax'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalHdmfPi'] == 0 || $employees['totalHdmfPi'] == null ? '-' :
                        number_format($employees['totalHdmfPi'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalHdmfMpl'] == 0 || $employees['totalHdmfMpl'] == null ? '-' :
                        number_format($employees['totalHdmfMpl'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalHdmfMp2'] == 0 || $employees['totalHdmfMp2'] == null ? '-' :
                        number_format($employees['totalHdmfMp2'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalHdmfCl'] == 0 || $employees['totalHdmfCl'] == null ? '-' :
                        number_format($employees['totalHdmfCl'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalDareco'] == 0 || $employees['totalDareco'] == null ? '-' :
                        number_format($employees['totalDareco'], 2) }}
                                        </td>
                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalTotalDeduction'] == 0 || $employees['totalTotalDeduction'] == null ? '-'
                        :
                        number_format($employees['totalTotalDeduction'], 2) }}
                                        </td>

                                        <td class="px-2 py-1 text-right ">
                                            {{ $employees['totalNetPay'] == 0 || $employees['totalNetPay'] == null ? '-' :
                        number_format($employees['totalNetPay'], 2) }}
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
                                        <td class="px-2 pt-3 pb-2 font-bold" colspan="2"><u>{{ $assigned->prepared->name ?? '-' }}</u>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="px-2 pt-3 pb-2 font-bold" colspan="2"><u>{{ $assigned->noted->name ?? '-' }}</u></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="px-2 pt-3 pb-2 font-bold" colspan="4"><u>{{ $assigned->funds->name ?? '-' }}</u></td>
                                        <td class="px-2 pt-3 pb-2 font-bold" colspan="3"><u>{{ $assigned->approved->name ?? '-' }}</u>
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



                                    {{-- total --}}
                    @empty
                        <tr>
                            <td colspan="18" class="text-center py-4">No payroll data available.</td>
                        </tr>
                    @endforelse
                    <tr class="border-x border-b border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                    <tr class="border-x border-b border-gray-300">
                        <td class="invisible">space</td>
                    </tr>



                    {{-- OVERALL --}}
                    <tr class="border-x border-b border-gray-300 font-bold">
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
                    <tr class="border-x border-b border-gray-300">
                        <td class="invisible">space</td>
                    </tr>

                    <tr class="border-x border-b border-gray-300">
                        <td></td>
                        <td></td>
                        <td class="text-right">COS/JO</td>
                        <td class="font-bold text-right">{{ number_format($overallTotal['totalGross'], 2) }}</td>
                        <td class="px-2  py-2 text-center"></td>
                        <td class="px-2 py-2 text-center"></td>

                        <td class="font-bold text-right">{{ number_format($overallTotal['totalAbsentLate'], 2) }}</td>
                        <td></td>
                        <td class="font-bold text-right">{{ number_format($overallTotal['totalTax'], 2) }}</td>

                        @if($cutoff == '1st')
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfPi'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfMpl'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfMp2'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalHdmfCl'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalDareco'], 2) }}</td>
                        @endif

                        @if($cutoff == '2nd')
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalSsCon'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalEcCon'], 2) }}</td>
                            <td class="font-bold text-right">{{ number_format($overallTotal['totalWisp'], 2) }}</td>
                            <td></td>
                            <td></td>
                        @endif




                        <td class="font-bold text-right">{{ number_format($overallTotal['totalTotalDeduction'], 2) }}
                        </td>

                        <td class="font-bold text-right px-2">{{ number_format($overallTotal['totalNetPay'], 2) }}</td>

                    </tr>
                    </tr>
                    <tr class="border-x border-b border-gray-300">
                        <td></td>
                        <td></td>
                        <td class="text-right">IMEMS</td>
                        <td class="px-2 text-right"></td>
                    </tr>
                    <tr class="border-x border-b border-gray-300">
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


                    {{-- <tr class="border border-gray-300">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="font-bold" colspan="4">VOUCHER</td>
                        <td class="font-bold text-center">TOTAL</td>
                    </tr>


                    @foreach($voucherNetPays as $voucher => $netPay)
                    <tr class="border border-gray-300">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="4">{{ $voucher }}</td>
                        <td class="font-bold text-right">{{ number_format($netPay, 2) }}</td>
                    </tr>
                    @endforeach --}}



                    <tr class="border-x border-b border-gray-300">
                        <td class="invisible">space</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div> @push('scripts')
    <script src="{{ asset('js/printPayroll.js') }}">
    </script>
@endpush
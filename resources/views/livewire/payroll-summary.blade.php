<div class="flex-1 flex-col">
    <div class="w-full flex justify-between items-center mb-6">
        <h2 class="font-black text-gray-700">PAYROLL SUMMARY</h2>

        <div class="flex text-sm gap-2">
            <select class="shadow-sm border rounded border-gray-200 px-4 py-2" wire:model.live="designation">
                <option value="">All Designations</option>
                @foreach ($designations as $desig)
                <option value="{{ $desig }}">{{ $desig }}</option>
                @endforeach
            </select>
            <select name="" id="" class="px-2 py-1 rounded border border-gray-300 shadow-sm cursor-pointer">
                <option value="">Select Cutoff</option>
                <option value="">1st Cutoff (1-15)</option>
                <option value="">2nd Cutoff (16-31)</option>
            </select>
            <button class="bg-green-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-green-600"><i
                    class="fa-solid fa-floppy-disk mr-1"></i> Save to Archive</button>
            <button class="bg-blue-700 text-white font-semibold px-2 py-1 rounded cursor-pointer hover:bg-blue-600"><i
                    class="fa-solid fa-print mr-1"></i>Print Payroll</button>
        </div>
    </div>

    <div class="w-full flex justify-center items-center gap-6 mb-2">
        <div class="flex gap-2">
            <div class="w-20 h-20">
                <img src="{{ asset('images/bagong_pilipinas.png') }}" alt="Bagong Pilipinas"
                    class="object-cover w-full h-full">
            </div>
            <div class="w-20 h-20">
                <img src="{{ asset('images/bfar.png') }}" alt="BFAR" class="object-cover w-full h-full">
            </div>
        </div>
        <div class="text-center">
            <p class="text-xs">Republic of the Philippines</p>
            <p class="text-xs">Department of Agriculture </p>
            <h2 class="font-bold">BUREAU OF FISHERIES AND AQUATIC RESOURCES</h2>
            <p class="text-xs">Region XI, R. Magsaysay Ave., Davao City</p>
        </div>
        <div class="w-20 h-20">
            <img src="{{ asset('images/gad.png') }}" alt="GAD" class="object-cover w-full h-full">
        </div>
    </div>
    <p class="text-xs font-bold">CONTRACT OF SERVICES / JOB ORDER</p>
    <h2 class="font-bold">AUGUST 1-15, 2025</h2>




    <table class="table-auto border-collapse w-full text-xs">
        <thead>
            <tr>
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
                <th class="border border-gray-300 px-2 py-1">HDMF-PI</th>
                <th class="border border-gray-300 px-2 py-1">HDMF-MPL</th>
                <th class="border border-gray-300 px-2 py-1">HDMF-MP2</th>
                <th class="border border-gray-300 px-2 py-1">HDMF-CL</th>
                <th class="border border-gray-300 px-2 py-1">DARECO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedEmployees as $designation => $offices)
            <tr class="bg-green-200 font-bold border border-gray-300">
                <td></td>
                <td></td>
                <td colspan="18" class="px-2 py-1">{{ strtoupper($designation) }}</td>
            </tr>

            @foreach($offices as $office => $employees)
            @if($employees['employees']->filter(fn($employee) => !empty($employee->office_code))->isNotEmpty())
            <tr class="bg-gray-200 font-semibold border border-gray-300">
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
                <td class="border border-gray-300 px-2 py-1">{{ $loop->iteration }}</td>
                <td class="border border-gray-300 px-2 py-1"></td>
                <td class="border border-gray-300 px-2 py-2" width="300">
                    {{ $employee->first_name }} {{ $employee->middle_initial }} {{ $employee->last_name }} {{
                    $employee->suffix }}
                </td>
                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($employee->monthly_rate, 2) }}
                </td>
                {{-- <td class="border border-gray-300 px-2 py-1 text-center">8</td> --}}
                <td class="border border-gray-300 px-2 py-1 text-right">{{ number_format($employee->gross, 2) }}</td>

                {{-- Late / Absences --}}
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->absent == null || $rc->absent == 0 ? '-' : number_format($rc->absent, 2) }}
                </td>
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->late_undertime == null || $rc->late_undertime == 0 ? '-' :
                    number_format($rc->late_undertime, 2) }}
                </td>

                {{-- Total Late --}}
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->total_absent_late == null || $rc->total_absent_late == 0 ? '-' :
                    number_format($rc->total_absent_late, 2) }}
                </td>
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->net_late_absences == null || $rc->net_late_absences == 0 ? '-' :
                    number_format($rc->net_late_absences, 2) }}
                </td>

                {{-- Tax --}}
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->tax == null || $rc->tax == 0 ? '-' : number_format($rc->tax, 2) }}
                </td>
                <td class="border border-gray-300 px-2 py-1 text-right">
                    {{ $rc->net_tax == null || $rc->net_tax == 0 ? '-' : number_format($rc->net_tax, 2) }}
                </td>

                {{-- Contributions --}}
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

            <!-- Total Gross Row for Office -->
            <tr class="bg-yellow-300 font-bold border border-gray-300">
                <td></td>
                <td></td>
                <td class="px-2 py-1 text-center text-red-600">Total</td>
                <td></td>
                <td class="px-2 py-1 text-right text-red-600">{{ number_format($employees['totalGross'], 2) }}</td>
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
                    {{ $employees['totalNetLateAbsences'] == 0 || $employees['totalNetLateAbsences'] == null ? '-' :
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

                {{-- <td class="px-2 py-1 text-right text-red-600">
                    {{ $employees['totalSsCon'] == 0 || $employees['totalSsCon'] == null ? '-' :
                    number_format($employees['totalSsCon'], 2) }}
                </td>

                <td class="px-2 py-1 text-right text-red-600">
                    {{ $employees['totalEcCon'] == 0 || $employees['totalEcCon'] == null ? '-' :
                    number_format($employees['totalEcCon'], 2) }}
                </td>

                <td class="px-2 py-1 text-right text-red-600">
                    {{ $employees['totalWisp'] == 0 || $employees['totalWisp'] == null ? '-' :
                    number_format($employees['totalWisp'], 2) }}
                </td> --}}

                <td class="px-2 py-1 text-right text-red-600">
                    {{ $employees['totalTotalDeduction'] == 0 || $employees['totalTotalDeduction'] == null ? '-' :
                    number_format($employees['totalTotalDeduction'], 2) }}
                </td>

                <td class="px-2 py-1 text-right text-red-600">
                    {{ $employees['totalNetPay'] == 0 || $employees['totalNetPay'] == null ? '-' :
                    number_format($employees['totalNetPay'], 2) }}
                </td>


            </tr>
            <tr class="border border-gray-300">
                <td> SPACE</td>
            </tr>
            <tr class="bg-blue-400 font-bold border border-gray-300">
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
                    {{ $employees['totalNetLateAbsences'] == 0 || $employees['totalNetLateAbsences'] == null ? '-' :
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
                    {{ $employees['totalTotalDeduction'] == 0 || $employees['totalTotalDeduction'] == null ? '-' :
                    number_format($employees['totalTotalDeduction'], 2) }}
                </td>

                <td class="px-2 py-1 text-right ">
                    {{ $employees['totalNetPay'] == 0 || $employees['totalNetPay'] == null ? '-' :
                    number_format($employees['totalNetPay'], 2) }}
                </td>
            </tr>
            <tr>
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
                <td class="px-2" colspan="2">Funds Availability:</td>
                <td></td>
                <td></td>
                <td class="px-2" colspan="2">Approved:</td>
            </tr>
            <tr>
                <td> SPACE</td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td class="px-2 pt-3 pb-2 font-bold"><u>MARJORIE M. NUDALO</u></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="px-2 pt-3 pb-2 font-bold" colspan="2"><u>REISSA D. TARAZONA</u></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="px-2 pt-3 pb-2 font-bold" colspan="3"><u> EMMILOU J. UY, CPA, MBA</u></td>
                <td></td>
                <td class="px-2 pt-3 pb-2 font-bold" colspan="3"><u> ANGELI D. DELIGERO, CPA</u></td>
            </tr>

            <tr>
                <td></td>
                <td></td>
                <td class="px-2">Payroll Clerk</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="px-2" colspan="2">OIC, HRMU</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="px-2" colspan="2">OIC, Accounting Unit</td>
                <td></td>
                <td></td>
                <td class="px-2" colspan="2">OIC, FAS</td>
            </tr>
            <tr>
                <td> SPACE</td>
            </tr>


            @endforeach
            @empty
            <tr>
                <td colspan="18" class="text-center py-4">No payroll data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
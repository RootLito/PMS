@extends('layouts.app')

@section('title', 'Payslip')

@section('content')
    <div class="flex-1 grid place-items-center p-10 rel">
        <div class="print">
            <img src="{{ asset('images/top.png') }}" alt="header">
            <img src="{{ asset('images/bot.png') }}" alt="header">
            <h1 class="text-center font-bold mt-12 title">C E R T I F I C A T E &nbsp;&nbsp;O F&nbsp;&nbsp; N E T &nbsp;P A Y
            </h1>
            <div class="mt-[48px] text-[12pt] font-[Cambria] leading-none">
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">Name</span>
                    <span class="inline-block w-[8px] text-right">:</span>
                    <span class="ml-[40px]"><b><u>{{ $full_name }}</u></b></span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">Position</span>
                    <span class="inline-block w-[8px] text-right">:</span>
                    <span class="ml-[40px]"><u>{{ $position }}</u></span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">For the Month of</span>
                    <span class="inline-block w-[8px] text-right">:</span>
                    <span class="ml-[40px]"><u>{{ $ftm_coverage  }}</u></span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">Gross Monthly Income</span>
                    <span class="inline-block w-[8px] text-right">:</span>
                    <span class="ml-[40px]"><u>Php {{ number_format($gross_monthly_income, 2) }}</u></span>
                </div>
            </div>

            <div class="mt-[40px] ml-[40px]">
                <p>Less: DEDUCTIONS</p>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">HDMF-MC</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span
                        class="ml-[40px]">{{ $contributions['hdmf_pi'] && $contributions['hdmf_pi'] != 0 ? number_format($contributions['hdmf_pi'], 2) : '' }}</span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">HDMF-MPL</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span
                        class="ml-[40px]">{{ $contributions['hdmf_mpl'] && $contributions['hdmf_mpl'] != 0 ? number_format($contributions['hdmf_mpl'], 2) : '' }}</span>
                </div>


                @if (!is_null($contributions['hdmf_mp2']) && $contributions['hdmf_mp2'] != 0)
                    <div class="flex items-center h-[20px]">
                        <span class="inline-block w-[192px]">HDMF-MP2</span>
                        <span class="inline-block w-[8px] text-right">-</span>
                        <span class="ml-[40px]">
                            {{ number_format($contributions['hdmf_mp2'], 2) }}
                        </span>
                    </div>
                @endif
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">HDMF-Cal</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span
                        class="ml-[40px]">{{ $contributions['hdmf_cl'] && $contributions['hdmf_cl'] != 0 ? number_format($contributions['hdmf_cl'], 2) : '' }}</span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">SSS Contribution</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span
                        class="ml-[40px]">{{ $contributions['sss'] && $contributions['sss'] != 0 ? number_format($contributions['sss'], 2) : '' }}</span>
                </div>
                <div class="flex items-center h-[20px]0">
                    <span class="inline-block w-[192px]">EC Contribution</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span
                        class="ml-[40px]">{{ $contributions['ec'] && $contributions['ec'] != 0 ? number_format($contributions['ec'], 2) : '' }}</span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">Tax</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span class="ml-[40px]">{{ $tax && $tax != 0 ? number_format($tax, 2) : '' }}</span>
                </div>
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[192px]">Absences/Late</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span class="ml-[40px]">
                        <u>{!! $total_absent_late && $total_absent_late != 0
                            ? number_format($total_absent_late, 2)
                            : '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' !!}</u>
                    </span>

                </div>
            </div>
            <div class="mt-4 ml-[80px]">
                <div class="flex items-center h-[20px]">
                    <span class="inline-block w-[152px]">Total Deductions</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span class="ml-[40px]">{{ number_format($total_deductions, 2) }}</span>
                </div>
                <div class="flex items-center h-[20px] mt-4">
                    <span class="inline-block w-[152px]">Net Monthly Income</span>
                    <span class="inline-block w-[8px] text-right">-</span>
                    <span class="ml-[40px]"><b>Php {{ number_format($net_pay, 2) }}</b></span>
                </div>
            </div>
            <div class="cert mt-[28px]">
                <P>I certify that the abovementioned information is true and correct.</P>
                <P class="mt-2">Issued this {{ $issued_date }}.</P>
            </div>
            <div class="oic mt-[40px] leading-tight">
                <p class="font-bold mb-0">{{ $noted_by_name }}</p>
                <p class="italic">{{ $noted_by_designation }}</p>
            </div>

            <p class="cn">CN: {{ $controlNumber }}</p>
        </div>
        <div class="w-[320px] bg-white absolute top-26 right-10 flex flex-col p-6 rounded-xl date">
            <h2 class="font-bold text-gray-700">Modify Payslip</h2>
            <form method="GET" action="{{ route('employee.payslip', ['employeeId' => $employeeId]) }}">
                <label for="date" class="text-xs mt-4 text-gray-600">Date Issued</label>
                <input type="date" name="date" value="{{ $raw_date ?? '' }}"
                    class="text-sm  w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 ">
                <label for="ftm" class="text-xs mt-4 text-gray-600">For the Month of</label>
                <input type="date" name="ftm" value="{{ $ftm_raw ?? '' }}"
                    class="text-sm  w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">

                <label for="date" class="text-xs mt-4 text-gray-600">Control Number</label>
                <input type="text" name="control_number" value="{{ request('control_number') }}"
                    placeholder="Enter Control Number"
                    class="text-sm w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                <button type="submit"
                    class="text-sm  bg-green-700 hover:bg-green-800 h-10 mt-4 rounded-lg cursor-pointer text-white w-full"><i
                        class="fas fa-check mr-2"></i>Apply</button>
            </form>
            <button type="button" onclick="window.print()"
                class="text-sm bg-slate-700 hover:bg-slate-800 h-10 mt-4 rounded-lg cursor-pointer text-white w-full"><i
                    class="fas fa-print mr-2"></i>Print</button>

            <a href="/employee"
                class="text-sm bg-red-400 hover:bg-red-500 text-white h-10 mt-4 rounded-lg flex items-center justify-center w-full">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
@endsection

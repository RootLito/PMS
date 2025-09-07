<div class="flex-1 flex flex-col gap-10">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            ATTENDANCE
        </h2>
    </div>
    <div class="flex-1 flex flex-col bg-white rounded-xl p-6">
        <div class="flex justify-between mb-4 gap-6 mt-4">
            <input type="text" placeholder="Search Employee" class="border flex-1 border-gray-300 bg-gray-50 rounded px-4 py-2"
                wire:model.live="search">
            <div class="flex gap-2">
                <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-full" wire:model.live="office">
                    <option value="">Select Office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office }}">{{ $office }}</option>
                    @endforeach
                </select>

                <select wire:model.live="month" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white ">
                    <option value="">Select Month</option>
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="year" class="py-1 border border-gray-200 shadow-sm rounded-md px-2 bg-white">
                    <option value="">Select Year</option>
                    @foreach ($years as $yearOption)
                        <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                            {{ $yearOption }}
                        </option>
                    @endforeach
                </select>

                <button wire:click="exportPayroll"
                    class="px-6 py-1 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500">
                    Export
                </button>


            </div>
        </div>
        <table class="min-w-full table-auto text-sm mt-6 mb-6">
            <thead class="bg-gray-100 text-left">
                <tr class="border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap" rowspan="2" width="15%">Last Name</th>
                    <th class="px-4 py-3 text-nowrap" rowspan="2" width="15%">First Name</th>
                    <th class="px-4 py-3 text-nowrap" rowspan="2" width="5%">M.I.</th>
                    <th class="px-4 py-3 text-nowrap" rowspan="2" width="10%">Monthly Rate</th>
                    <th class="px-4 py-2 text-nowrap text-center" colspan="3" width="20%">Total Instances
                        (Absences)</th>
                    <th class="px-4 py-2 text-nowrap text-center" colspan="3" width="20%">Total Instances (Lates)
                    </th>
                    <th class="px-4 py-2 text-nowrap" rowspan="2" width="20%">Remarks</th>
                </tr>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-2 text-nowrap text-center">1st</th>
                    <th class="px-4 py-2 text-nowrap text-center">2nd</th>
                    <th class="px-4 py-2 text-nowrap text-center">Total</th>
                    <th class="px-4 py-2 text-nowrap text-center">1st</th>
                    <th class="px-4 py-2 text-nowrap text-center">2nd</th>
                    <th class="px-4 py-2 text-nowrap text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedEmployees as $designation => $offices)
                    {{-- <tr>
                        <td colspan="10" class="font-bold bg-gray-200">{{ $designation }}</td>
                    </tr> --}}
                    @foreach ($offices as $office => $employees)
                        <tr>
                            <td colspan="11" class="font-semibold px-4 py-2 bg-green-100 border-b border-gray-200">
                                {{ $office }}</td>
                        </tr>
                        @foreach ($employees as $emp)
                            <tr class="border-b border-gray-200 hover:bg-gray-200 cursor-pointer">
                                <td class="px-4 py-2">{{ $emp['last_name'] }}</td>
                                <td class="px-4 py-2">{{ $emp['first_name'] }}</td>
                                <td class="px-4 py-2">
                                    {{ $emp['middle_initial'] ? strtoupper(substr($emp['middle_initial'], 0, 1)) . '.' : '' }}
                                </td>
                                <td class="px-4 py-2">{{ number_format($emp['monthly_rate'], 2) }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['absent_1'] ?: '' }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['absent_2'] ?: '' }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['absent_total'] ?: '-' }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['late_1'] ?: '' }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['late_2'] ?: '' }}</td>
                                <td class="px-4 py-2 text-center">{{ $emp['late_total'] ?: '-' }}</td>
                                <td class="px-4 py-2">{{ $emp['remarks'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

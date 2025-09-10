<div class="flex-1 flex flex-col gap-10">
    <div class="w-full h-full flex gap-10">

        <div class="w-[40%] h-full flex flex-col gap-10">
            <div class="w-full h-[60%] rounded-xl flex flex-col gap-10">
                <div class="w-full flex-1 bg-white rounded-xl">

                </div>
                <div class="w-full flex-1 rounded-xl flex gap-10">

                    <div class="flex-1 bg-white rounded-xl flex gap-10">

                    </div>

                    <div class="flex-1 bg-white rounded-xl flex gap-10">

                    </div>

                </div>
                <div class="w-full flex-1 flex gap-10">

                    <div class="flex-1 bg-white rounded-xl flex gap-10">

                    </div>

                    <div class="flex-1 bg-white rounded-xl flex gap-10">

                    </div>

                </div>
            </div>

            <div class="w-full h-[40%] bg-white rounded-xl">

            </div>
        </div>


        <div class="w-[60%] h-full flex flex-col gap-10">
            <div class="w-full h-[40%] bg-white rounded-xl">

            </div>

            <div class="w-full h-[60%] bg-white rounded-xl">

            </div>
        </div>

    </div>






    {{-- <div class="w-full min-h-screen grid grid-cols-3 grid-rows-3 gap-10">

        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="flex gap-2 p-4 border-b border-gray-200">
                <i class="fa-solid fa-briefcase text-gray-600 text-xl"></i>
                <h2 class="font-bold text-gray-600">Employee Status Total</h2>
            </div>

            <div class="flex-1 py-6 flex ">
                <div class="flex-1 flex flex-col items-center justify-end py-6">
                    <div class="text-5xl text-gray-700">
                        {{ $joCount }}
                    </div>
                    <h2 class="text-2xl font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">JO</h2>
                </div>
                <div class="flex-1 flex flex-col items-center justify-end py-6">
                    <p class="text-5xl text-gray-700">{{ $cosCount }}</p>
                    <h2 class="text-2xl font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">COS</h2>
                </div>
                <div class="flex-1 flex flex-col items-center justify-end py-6">
                    <div class="text-5xl text-gray-700">
                        {{ $totalCount }}
                    </div>
                    <h2 class="text-2xl font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">TOTAL</h2>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="flex gap-2 p-4 border-b border-gray-200">
                <i class="fa-solid fa-venus-mars text-gray-600 text-xl"></i>
                <h2 class="font-bold text-gray-600">Gender Total</h2>
            </div>

            <div class="flex-1 py-6 flex ">
                <div class="flex-1 flex flex-col items-center justify-end py-6">
                    <div class="text-5xl text-gray-700">
                        {{ $maleCount }}
                    </div>
                    <h2 class="text-2xl font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">MALE</h2>
                </div>
                <div class="flex-1 flex flex-col items-center justify-end py-6">
                    <div class="text-5xl text-gray-700">
                        {{ $femaleCount }}
                    </div>
                    <h2 class="text-2xl font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">FEMALE</h2>
                </div>
            </div>
        </div>


        <div class="bg-white row-span-3 col-start-3 rounded-xl shadow-sm overflow-hidden">
            <div class="flex gap-2 p-4 border-b border-gray-200">
                <i class="fa-solid fa-users text-gray-600 text-xl"></i>
                <h2 class="font-bold text-gray-600">Employee Count per Office</h2>
            </div>

            <div class="p-4 pb-10 space-y-2 max-h-screen overflow-y-auto">
                @forelse ($officeCounts as $office => $count)
                    <div class="flex justify-between text-sm text-gray-700 border-b border-gray-200 pb-1">
                        <span class="font-medium">{{ $office }}</span>
                        <span class="text-gray-500">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No data available.</p>
                @endforelse
            </div>
        </div>



        <div class="bg-white col-span-2 row-start-2 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="flex gap-2 p-4 border-b border-gray-200">
                <i class="fa-solid fa-money-bill-wave text-gray-600 text-lg"></i>
                <h2 class="font-bold text-gray-600">Contribution Total</h2>
            </div>

            <div class="flex-1 grid grid-cols-6">
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalPi, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">HDMF-PI</h2>
                </div>
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalMp2, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">HDMF-MP2</h2>
                </div>
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalMpl, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">HDMF-MPL</h2>
                </div>
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalCl, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">HDMF-CL</h2>
                </div>
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalDareco, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">DARECO</h2>
                </div>
                <div class="flex flex-col justify-center items-center gap-2 p-4">
                    <div class="text-xl text-gray-700 font-semibold">₱{{ number_format($totalSssEcWisp, 2) }}</div>
                    <h2 class="text-lg font-bold mt-6 text-gray-500 p-2 bg-gray-100 rounded-lg">SSS, EC, WISP</h2>
                </div>
            </div>
        </div>

        <div class="bg-white col-span-2 row-start-3 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="flex gap-2 p-4 border-b border-gray-200">
                <i class="fa-solid fa-calendar-check text-gray-600 text-xl"></i>
                <h2 class="font-bold text-gray-600">Employee Attendance Status for the Month of September</h2>
            </div>

            <div class="flex-1 p-6 overflow-auto">
                @if ($employeesData->isEmpty())
                    <div class="text-center text-gray-500">No Record Yet</div>
                @else
                    <table class="w-full text-left table-auto border-collapse border border-gray-200 text-xs">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 px-4 py-2">Name</th>
                                <th class="border border-gray-300 px-4 py-2">Late</th>
                                <th class="border border-gray-300 px-4 py-2">Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employeesData as $employee)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $employee['full_name'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="flex items-center justify-between">
                                            <div>{{ $employee['total_late_ins'] }}</div>
                                            <div class="flex items-center space-x-2">
                                                @if ($employee['late_status'] === 'memo')
                                                    <span class="w-3 h-3 bg-red-600 rounded-full blink"></span>
                                                @endif
                                                <span>{{ $employee['late_status'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="flex items-center justify-between">
                                            <div>{{ $employee['total_absent_ins'] }}</div>
                                            <div class="flex items-center space-x-2">
                                                @if ($employee['absent_status'] === 'memo')
                                                    <span class="w-3 h-3 bg-red-600 rounded-full blink"></span>
                                                @endif
                                                <span>{{ $employee['absent_status'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div> --}}
    @if ($showModal)
        <div
            style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 40; display: flex; justify-content: center; align-items: center;">
            <div
                style="background: white; width: 24rem; border-radius: .5rem; overflow: hidden; font-family: Arial, sans-serif;">
                <div
                    style="background-color: #f56565; padding: 2rem; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="color: white; font-size: 2.5rem;"></i>
                </div>
                <div style="padding: 1.5rem 2rem; color: #2d3748; text-align: center;">
                    <h2 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 1.5rem; margin-top: 1rem;">REMINDER
                    </h2>

                    <p style="font-size: 0.9rem; color: #718096; margin-bottom: 1.5rem;">
                        The Pag-IBIG loan term of the following employee(s) will end tomorrow. Kindly ensure all
                        necessary payments are completed:
                    </p>

                    <ul
                        style="color: #4a5568; font-weight: 600; margin-bottom: 1.5rem; text-align: left; max-height: 200px; overflow-y: auto; padding-left: 1rem;">
                        @foreach ($reminderData as $reminder)
                            <div class="w-full flex justify-between">
                                <li>• {{ $reminder['full_name'] }}</li>
                                {{ $reminder['end_term_date'] }}
                            </div>
                        @endforeach
                    </ul>

                    <button wire:click="closeModal" class="bg-red-400"
                        style="color: white; border-radius: 0.5rem; padding: 0.5rem 1rem; cursor: pointer; width: 100%; font-weight: 600;">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

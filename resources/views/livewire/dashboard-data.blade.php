<div class="flex-1 flex flex-col gap-10">
    <div class="w-full h-full flex gap-10">

        <div class="w-[40%] h-full flex flex-col gap-10">
            <div class="w-full h-[60%] rounded-xl flex flex-col gap-10">
                <div class="w-full flex-1 flex gap-10">
                    
                    <div class="flex-1 flex items-center justify-between p-6 gap-10 bg-white rounded-xl">
                        <div class="w-[50px] h-[50px] bg-green-100 rounded-lg grid place-items-center">
                            <i class="fas fa-users text-green-400 text-3xl"></i>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">Total Employee</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $totalCount }}</p>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center justify-between p-6 gap-10 bg-white rounded-xl">
                        <div class="w-[50px] h-[50px] bg-violet-100 rounded-lg grid place-items-center">
                            <p class="text-violet-400  font-bold">NSAP</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">NSAP</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $nsap }}</p>
                        </div>
                    </div>
                </div>
                <div class="w-full flex-1 rounded-xl flex gap-10">
                    <div class="flex-1 bg-white rounded-xl flex items-center justify-between p-6 gap-10">
                        <div class="w-[50px] h-[50px] bg-blue-100 rounded-lg grid place-items-center">
                            <i class="fas fa-mars text-blue-400 text-3xl"></i>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">Male</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $maleCount }}</p>
                        </div>
                    </div>

                    <div class="flex-1 bg-white rounded-xl flex items-center justify-between p-6 gap-10">
                        <div class="w-[50px] h-[50px] bg-red-100 rounded-lg grid place-items-center">
                            <i class="fas fa-venus text-red-400 text-3xl"></i>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">Female</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $femaleCount }}</p>
                        </div>
                    </div>

                </div>
                <div class="w-full flex-1 flex gap-10">
                    <div class="flex-1 bg-white rounded-xl flex items-center justify-between p-6 gap-10">
                        <div class="w-[50px] h-[50px] bg-yellow-100 rounded-lg grid place-items-center">
                            <p class="text-yellow-400 text-2xl font-bold">JO</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">Job Order</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $joCount }}</p>
                        </div>
                    </div>
                    <div class="flex-1 bg-white rounded-xl flex items-center justify-between p-6 gap-10">
                        <div class="w-[50px] h-[50px] bg-violet-100 rounded-lg grid place-items-center">
                            <p class="text-violet-400 text-xl font-bold">COS</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <p class="text-gray-600">Contract of Service</p>
                            <p class="text-4xl font-bold text-gray-700">{{ $cosCount }}</p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="w-full h-[40%] bg-white rounded-xl p-6 flex flex-col gap-6">
                <div class="w-full flex gap-2 items-center">
                    <div class="w-[50px] h-[50px] bg-blue-100 rounded-lg grid place-items-center">
                        <i class="fas fa-calendar-alt text-blue-400 text-3xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-gray-600">Attendance Status</p>
                        <p class="text-gray-600 text-sm font-bold">For the month of {{ $attMonth }}</p>
                    </div>

                    <div class="flex flex-col ml-auto text-sm">
                        <div class="flex gap-2 items-center">
                            <span class="w-3 h-3 bg-yellow-400 rounded-full inline-block"></span>
                            <span>1-9 Warning</span>
                        </div>
                        <div class="flex gap-2 items-center">
                            <span class="w-3 h-3 bg-red-400 rounded-full inline-block"></span>
                            <span>10 Memo</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 ">
                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-300">
                                <th class="font-semibold py-3 px-3" width="10%">Status</th>
                                <th class="font-semibold py-3 px-3">Name</th>
                                <th class="font-semibold py-3 px-3 text-center" width="20%">Late</th>
                                <th class="font-semibold py-3 px-3 text-center" width="20%">Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employeesData as $employee)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-2 px-3 flex items-center justify-center">
                                        @if ($employee['late_status'] === 'memo' || $employee['absent_status'] === 'memo')
                                            <span class="w-3 h-3 bg-red-600 rounded-full inline-block blink mt-1"
                                                title="Memo"></span>
                                        @elseif ($employee['late_status'] === 'warning' || $employee['absent_status'] === 'warning')
                                            <span class="w-3 h-3 bg-yellow-400 rounded-full inline-block blink"
                                                title="Warning"></span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        {{ $employee['full_name'] }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        {{ $employee['total_late_ins'] }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        {{ $employee['total_absent_ins'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="w-[60%] h-full flex flex-col gap-10">
            <div class="w-full h-[40%] bg-white rounded-xl p-6 gap-6 flex flex-col">
                <div class="w-full flex gap-2 items-center">
                    <div class="w-[50px] h-[50px] bg-yellow-100 rounded-lg grid place-items-center">
                        <i class="fas fa-coins text-yellow-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-600">Contribution</p>
                </div>


                <div class="flex-1 grid grid-cols-6 gap-6">
                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalPi, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            HDMF-PI
                        </div>
                    </div>

                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalMp2, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            HDMF-MP2
                        </div>
                    </div>

                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalMpl, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            HDMF-MPL
                        </div>
                    </div>

                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalCl, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            HDMF-CL
                        </div>
                    </div>

                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalDareco, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            Dareco
                        </div>
                    </div>

                    <div class="flex flex-col justify-end items-center">
                        <p class="text-2xl mb-8 font-bold text-gray-700">₱{{ number_format($totalSssEcWisp, 2) }}</p>
                        <div class="w-full p-2 bg-gray-100 text-center rounded-xl text-sm font-semibold text-gray-600">
                            SSS, EC, WISP
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full h-[60%] bg-white rounded-xl p-6 flex flex-col gap-6">
                <div class="w-full flex gap-2 items-center">
                    <div class="w-[50px] h-[50px] bg-green-100 rounded-lg grid place-items-center">
                        <i class="fas fa-building text-green-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-600">Employee Per Office</p>
                    <div class="flex gap-2 ml-auto">
                        {{-- <input type="text" placeholder="Search Employee"
                            class="border flex-1 border-gray-300 bg-gray-50 rounded px-4 py-1 text-sm"
                            wire:model.live="search"> --}}
                        <select class="shadow-sm border rounded border-gray-200 px-4 py-1 text-sm w-full"
                            wire:model.live="office">
                            <option value="">Select Office</option>
                            @foreach ($offices as $office)
                                <option value="{{ $office }}">{{ $office }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="flex-1 flex flex-col">
                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-300">
                                <th class="font-semibold py-3 px-3">Office Name</th>
                                <th class="font-semibold py-3 px-3 text-center">Employee Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($officeCounts as $officeCount)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-2 px-3 font-medium">{{ $officeCount->office }}</td>
                                    <td class="py-2 px-3 text-gray-500 text-center" width='20%'>
                                        {{ $officeCount->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-gray-400 py-2 px-3 text-center">No data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($officeCounts->hasPages())
                        <div class="w-full flex justify-between items-end mt-auto">
                            <div class="flex justify-center text-gray-600 mt-2 text-xs select-none">
                                @php
                                    $from = $officeCounts->firstItem();
                                    $to = $officeCounts->lastItem();
                                    $total = $officeCounts->total();
                                @endphp
                                Showing {{ $from }} to {{ $to }} of {{ number_format($total) }}
                                results
                            </div>
                            <nav role="navigation" aria-label="Pagination Navigation"
                                class="flex justify-center mt-4 text-xs">
                                <ul class="inline-flex items-center space-x-1 select-none">
                                    @if ($officeCounts->onFirstPage())
                                        <li class="text-gray-400 cursor-not-allowed px-4 py-2 rounded ">&lt;</li>
                                    @else
                                        <li>
                                            <button wire:click="previousPage"
                                                class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer bg-white shadow-sm">&lt;</button>
                                        </li>
                                    @endif

                                    @php
                                        $current = $officeCounts->currentPage();
                                        $last = $officeCounts->lastPage();

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
                                                {{ $page }}</li>
                                        @else
                                            <li>
                                                <button wire:click="gotoPage({{ $page }})"
                                                    class="px-4 py-2 rounded hover:bg-gray-200 cursor-pointer">{{ $page }}</button>
                                            </li>
                                        @endif
                                    @endfor

                                    @if ($officeCounts->hasMorePages())
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
            </div>
        </div>
    </div>

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
                    <h2 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 1.5rem; margin-top: 1rem;">
                        REMINDER
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

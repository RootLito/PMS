<div class="flex gap-10">
    <div class="w-1/2 flex flex-col bg-white rounded-xl p-6 h-96 overflow-auto">
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
            <table class="min-w-full table-auto text-xs">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200 text-gray-700">
                        <th class="px-4 py-2">Last Name</th>
                        <th class="px-4 py-2">First Name</th>
                        <th class="px-4 py-2">M.I.</th>
                        <th class="px-4 py-2">Designation</th>
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
                            <td class="px-4 py-2">
                                {{ $employee->middle_initial }}
                                @if (!empty($employee->middle_initial))
                                    .
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $employee->designation }}</td>
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
                                <li class="bg-blue-600 text-white px-4 py-2 rounded cursor-default">{{ $page }}
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

    <div class="w-1/2 flex flex-col gap-10 bg-white rounded-xl p-6">
        <form wire:submit.prevent="saveContributions">

            <div class="w-full flex justify-between">
                <h2 class="text-xl font-bold text-gray-700">Contributions</h2>

                <div class="flex item-center gap-2">
                    <select wire:model="selectedContribution"
                        class="block h-9 border border-gray-200 bg-gray-50 rounded-md px-2 text-xs  cursor-pointer">
                        <option value="">-- Select Contribution --</option>
                        <option value="hdmf_pi">HDMF - PI</option>
                        <option value="hdmf_mp2">HDMF - MP2</option>
                        <option value="hdmf_mpl">HDMF - MPL</option>
                        <option value="hdmf_cl">HDMF - CL</option>
                        <option value="dareco">DARECO</option>
                        <option value="sss_ec_wisp">SSS, EC, WISP</option>
                    </select>
                    <button wire:click.prevent="exportContribution"
                        class="text-xs px-3 h-9 bg-slate-700 rounded-md text-white cursor-pointer">
                        <i class="fas fa-file-export mr-1"></i>
                        Export
                    </button>
                </div>
            </div>


            {{-- PAG-IBIG ID/RTN ------------------------------------------------------ --}}

            <div class="w-full flex justify-between">
                <div class="flex flex-col mt-6">
                    <label class="block text-xs text-gray-700">Pag-IBIG ID/RTN</label>
                    <div class="w-full flex justify-between items-center">
                        <input type="text" wire:model.live="pag_ibig_id_rtn"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
                <div class="relative mt-6" style="width: 200px;"> <!-- fixed width -->
                    <label class="block text-xs text-gray-700 mb-1">Add Contribution</label>

                    <div class="h-9 flex items-center px-4 rounded border border-gray-300 shadow-sm cursor-pointer text-sm truncate whitespace-nowrap overflow-hidden"
                        wire:click="toggleContributions" style="width: 200px;">
                        @if (count($selectedContributions))
                            {{ implode(', ', array_map(fn($val) => $contributionLabels[$val] ?? $val, $selectedContributions)) }}
                        @else
                            Select Contributions
                        @endif
                    </div>

                    @if ($showContributions)
                        <div class="w-full absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow p-2 z-10 max-h-60 overflow-y-auto"
                            style="width: 200px;">
                            @foreach ($contributionLabels as $value => $label)
                                <label class="block text-sm cursor-pointer mb-1">
                                    <input type="checkbox" value="{{ $value }}"
                                        wire:model="selectedContributions" class="cursor-pointer mr-2">
                                    {{ $label }}
                                </label>
                            @endforeach

                            <button wire:click.prevent="confirmContributions"
                                class="mt-2 w-full bg-blue-700 text-white font-semibold py-1 rounded hover:bg-blue-600 text-sm">
                                Confirm
                            </button>
                        </div>
                    @endif
                </div>




            </div>


            {{-- PI/MC ------------------------------------------------------ --}}
            @if (in_array('hdmf_pi', $selectedContributions))
                <div class="mt-6">
                    <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">HMDF-PI/MC</h1>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Account Number</label>
                            <input type="text" wire:model="account_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Membership Program</label>
                            <input type="text" wire:model="mem_program"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">PERCOV</label>
                            <input type="text" min="0" step="0.01" wire:model="pi_mc_percov"
                                class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                disabled>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">EE SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="pi_mc_ee_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">ER SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="pi_mc_er_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-xs text-gray-700">Remarks</label>
                        <input type="text" wire:model="pi_mc_remarks"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
            @endif


            {{-- MPL ------------------------------------------------------ --}}
            @if (in_array('hdmf_mpl', $selectedContributions))
                <div class="mt-6">
                    <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">HMDF-MPL</h1>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Application Number</label>
                            <input type="text" wire:model="application_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Loan Type</label>
                            <input type="text" wire:model="loan_type"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Amount</label>
                            <input type="number" min="0" step="0.01" wire:model="mpl_amount"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Status</label>
                            <select wire:model="status"
                                class="text-sm block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                <option value="" selected disabled> - - Select Status- - </option>
                                <option value="existing loan">Existing Loan</option>
                                <option value="new loan">New Loan</option>
                                <option value="reloan">Reloan</option>
                            </select>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Start Term</label>
                            <input type="date" wire:model="start_te"
                                class="text-sm block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">End Term</label>
                            <input type="date" wire:model="end_te"
                                class="text-sm block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="w-full flex gap-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Remarks</label>
                            <input type="text" wire:model="mpl_remarks"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Note</label>
                            <input type="text" wire:model="notes"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>
            @endif


            {{-- MP2 ------------------------------------------------------ --}}
            @if (in_array('hdmf_mp2', $selectedContributions))
                <div class="mt-6">
                    <div class="w-full bg-gray-300 flex justify-between items-center">
                        <h1 class="py-1 px-2 text-gray-700 font-bold ">HMDF-MP2</h1>
                        <button
                            class="bg-green-700 text-white py-1 px-4 rounded flex items-center space-x-2 cursor-pointer text-xs mr-1"
                            wire:click.prevent="addMp2Entry" type="button">
                            <i class="fas fa-plus"></i>
                            <span>Add</span>
                        </button>
                    </div>

                    @foreach ($mp2Entries as $index => $entry)
                        <div class="w-full mt-4 ">
                            <div class="w-full flex gap-2 mb-2">
                                <div class="flex flex-col flex-1">
                                    <label class="block text-xs text-gray-700">Account Number</label>
                                    <input type="text"
                                        wire:model.defer="mp2Entries.{{ $index }}.account_number"
                                        class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                </div>
                                <div class="flex flex-col flex-1">
                                    <label class="block text-xs text-gray-700">Membership Program</label>
                                    <input type="text"
                                        wire:model.defer="mp2Entries.{{ $index }}.mem_program"
                                        class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                </div>
                            </div>

                            <div class="w-full flex gap-2 mb-2">
                                <div class="flex flex-col flex-1">
                                    <label class="block text-xs text-gray-700">PERCOV</label>
                                    <input type="text" wire:model.defer="mp2Entries.{{ $index }}.percov"
                                        class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                        disabled>
                                </div>
                                <div class="flex flex-col flex-1">
                                    <label class="block text-xs text-gray-700">EE SHARE</label>
                                    <input type="number" min="0" step="0.01"
                                        wire:model.defer="mp2Entries.{{ $index }}.ee_share"
                                        class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                </div>
                                <div class="flex flex-col flex-1">
                                    <label class="block text-xs text-gray-700">ER SHARE</label>
                                    <input type="number" min="0" step="0.01"
                                        wire:model.defer="mp2Entries.{{ $index }}.er_share"
                                        class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <label class="block text-xs text-gray-700">Remarks</label>
                                <input type="text" wire:model.defer="mp2Entries.{{ $index }}.remarks"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif


            {{-- CL ------------------------------------------------------ --}}
            @if (in_array('hdmf_cl', $selectedContributions))
                <div class="mt-6">
                    <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">HMDF-CL</h1>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Account Number</label>
                            <input type="text" wire:model="cl_account_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Membership Program</label>
                            <input type="text" wire:model="cl_mem_program"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">PERCOV</label>
                            <input type="text" min="0" step="0.01" wire:model="cl_percov"
                                class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                disabled>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">EE SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="cl_ee_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">ER SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="cl_er_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-xs text-gray-700">Remarks</label>
                        <input type="text" wire:model="cl_remarks"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
            @endif


            {{-- DARECO ------------------------------------------------------ --}}
            @if (in_array('dareco', $selectedContributions))
                <div class="mt-6">
                    <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">DARECO</h1>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Account Number</label>
                            <input type="text" wire:model="dareco_account_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">Membership Program</label>
                            <input type="text" wire:model="dareco_mem_program"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="w-full flex gap-2 mb-2">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">PERCOV</label>
                            <input type="text" min="0" step="0.01" wire:model="dareco_percov"
                                class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                disabled>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">EE SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="dareco_ee_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">ER SHARE</label>
                            <input type="number" min="0" step="0.01" wire:model="dareco_er_share"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-xs text-gray-700">Remarks</label>
                        <input type="text" wire:model="dareco_remarks"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
            @endif


            {{-- SSS | EC | WISP ------------------------------------------------------ --}}
            @if (in_array('sss_ec_wisp', $selectedContributions))
                <div class="w-full mt-6">
                    <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">SSS | EC | WISP</h1>
                    <div class="flex gap-4">
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">SSS Number</label>
                            <input type="text" wire:model="sss_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">EC Number</label>
                            <input type="text" wire:model="ec_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block text-xs text-gray-700">WISP Number</label>
                            <input type="text" wire:model="wisp_number"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>
            @endif


            @if (is_null($selectedEmployee))
            @elseif (empty($selectedContributions))
                <p
                    class="text-gray-600 text-sm mt-4 mb-4 w-full h-10 flex items-center justify-center bg-gray-300 rounded-md font-semibold">
                    Please select contribution(s)
                </p>
            @else
                <button type="submit"
                    class="mt-4 mb-4 w-full h-10 bg-slate-700 rounded-md text-white cursor-pointer">
                    CONFIRM
                </button>
            @endif

        </form>
    </div>
</div>

<div class="flex-1 flex flex-col p-10 gap-10 relative">
    <div class="w-full flex justify-between">
        <h2 class="text-5xl font-bold text-gray-700">
            EMPLOYEE CONTRIBUTION
        </h2>
        <div class="flex gap-2">
            <select wire:model.live="month" class="h-10 border border-gray-200 shadow-sm rounded-md px-2 pr-8 bg-white">
                <option value="" disabled>Select Month</option>
                @foreach ($months as $num => $name)
                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}
                    </option>
                @endforeach
            </select>

            <select wire:model.live="year" class="h-10 border border-gray-200 shadow-sm rounded-md px-2 pr-8 bg-white">
                <option value="" disabled>Select Year</option>
                @foreach ($years as $yearOption)
                    <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>{{ $yearOption }}
                    </option>
                @endforeach
            </select>

            <button wire:click.prevent="updatePercov" wire:loading.attr="disabled" wire:target="updatePercov"
                class="w-48 h-10 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-900 mr-4 text-sm">
                <span wire:loading.remove wire:target="updatePercov">Update PerCov</span>
                <span wire:loading wire:target="updatePercov">Updating...</span>
            </button>

            <select wire:model.live="selectedContribution"
                class="block h-10 border border-gray-200 bg-white text-sm rounded-md px-2 cursor-pointer shadow-sm">
                <option value="">-- Select Contribution --</option>
                <option value="hdmf_pi">HDMF - PI</option>
                <option value="hdmf_mp2">HDMF - MP2</option>
                <option value="hdmf_mpl">HDMF - MPL</option>
                <option value="hdmf_cl">HDMF - CL</option>
                <option value="dareco">DARECO</option>
                <option value="sss_ec_wisp">SSS, EC, WISP</option>
                {{-- <option value="tax">TAX</option> --}}
            </select>
            <button wire:click.prevent="exportContribution"
                class="text-sm px-10 h-10 bg-slate-700 rounded-md text-white cursor-pointer">
                <i class="fas fa-file-export mr-1"></i>
                Export
            </button>
        </div>
    </div>


    <div class="flex gap-10">
        <div class="w-1/2 flex flex-col bg-white rounded-xl p-6 overflow-auto">
            <div class="flex justify-between mb-4 gap-2 mt-2">
                <input type="text" placeholder="Search by name..."
                    class="border border-gray-300 bg-gray-50 rounded px-4 py-2 w-1/2" wire:model.live="search">
                <div class="w-1/2 flex gap-2 justify-end">
                    <select class="shadow-sm border rounded border-gray-200 px-4 py-2 w-48"
                        wire:model.live="designation">
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
                        <tr class="border-b border-t border-gray-200 text-gray-700 ">
                            <th class="px-4 py-2 ">Last Name</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">M.I.</th>
                            <th class="px-4 py-2">Designation</th>
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
                                    @if (!empty($employee->middle_initial))
                                        {{ strtoupper(substr($employee->middle_initial, 0, 1)) }}.
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $employee->designation }}</td>




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

        <div class="w-1/2 flex flex-col gap-10 text-xs">
            <form class="flex-1 flex flex-col bg-white rounded-xl p-6" wire:submit.prevent="saveContributions">
                @if ($employeeName)
                    <div class="px-2 py-1 rounded bg-gray-500 mb-2">
                        <p class="font-semibold text-white uppercase"> {{ $employeeName }} </p>
                    </div>
                @endif

                <div class="w-full flex justify-between ">
                    <h2 class="text-xl font-bold text-gray-700">Contributions</h2>

                    <div class="relative" style="width: 200px;">
                        <div class="h-9 flex items-center px-4 rounded border border-gray-300 shadow-sm cursor-pointer text-sm truncate whitespace-nowrap overflow-hidden"
                            wire:click="toggleContributions" style="width: 200px;">
                            @if (count($selectedContributions))
                                {{ implode(', ', array_map(fn($val) => $contributionLabels[$val] ?? $val, $selectedContributions)) }}
                            @else
                                Add Contributions
                            @endif
                        </div>

                        @if ($showContributions)
                            <div class="w-full absolute top-full left-0 mt-1 bg-white border border-gray-300 rounded shadow p-2 z-10 max-h-60 overflow-y-auto"
                                style="width: 200px;">
                                @foreach ($contributionLabels as $value => $label)
                                    <label class="block text-sm cursor-pointer mb-1">
                                        <input type="checkbox" value="{{ $value }}"
                                            wire:model.live="selectedContributions" class="cursor-pointer mr-2">
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


                {{-- PAG-IBIG ID/RTN ------------------------------------------------------ --}}

                <div class="w-full flex justify-between">
                    <div class="flex flex-col mt-6">
                        @if (count(array_intersect($selectedContributions, ['hdmf_pi', 'hdmf_mp2', 'hdmf_mpl', 'hdmf_cl'])) > 0)
                            <label class="block text-xs text-gray-700">Pag-IBIG ID/RTN</label>
                            <div class="w-full flex justify-between items-center">
                                <input type="text" wire:model.live="pag_ibig_id_rtn"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        @endif
                    </div>
                </div>


                {{-- PI/MC ------------------------------------------------------ --}}
                @if (in_array('hdmf_pi', $selectedContributions))
                    <div class="mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">HMDF-PI/MC</h1>
                            <button wire:click.prevent="deleteContribution('hdmf_pi')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Account Number</label>
                                <input type="text" wire:model="account_number"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Membership Program</label>
                                <input type="text" wire:model="mem_program"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">PERCOV</label>
                                <input type="text" min="0" step="0.01" wire:model="pi_mc_percov"
                                    class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    disabled>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">EE SHARE</label>
                                <input type="number" min="0" step="0.01" wire:model="pi_mc_ee_share"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">ER SHARE</label>
                                <input type="number" min="0" step="0.01" wire:model="pi_mc_er_share"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="block  text-gray-700">Remarks</label>
                            <input type="text" wire:model="pi_mc_remarks"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                @endif


                {{-- MPL ------------------------------------------------------ --}}
                @if (in_array('hdmf_mpl', $selectedContributions))
                    <div class="mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">HMDF-MPL</h1>
                            <button wire:click.prevent="deleteContribution('hdmf_mpl')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Application Number</label>
                                <input type="text" wire:model="application_number"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Loan Type</label>
                                <input type="text" wire:model="loan_type"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Amount</label>
                                <input type="number" min="0" step="0.01" wire:model="mpl_amount"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Status</label>
                                <select wire:model="status"
                                    class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                    <option value="" selected disabled> - - Select Status- - </option>
                                    <option value="existing loan">Existing Loan</option>
                                    <option value="new loan">New Loan</option>
                                    <option value="reloan">Reloan</option>
                                </select>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Start Term</label>
                                <input type="date" wire:model="start_te"
                                    class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">End Term</label>
                                <input type="date" wire:model="end_te"
                                    class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="w-full flex gap-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Remarks</label>
                                <input type="text" wire:model="mpl_remarks"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Note</label>
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
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">HMDF-MP2</h1>
                            <div class="flex gap-2 items-center py-1">
                                <button
                                    class="bg-green-700 text-white py-1 px-4 rounded flex items-center cursor-pointer "
                                    wire:click.prevent="addMp2Entry" type="button">
                                    <i class="fas fa-plus"></i>
                                    <span>Add</span>
                                </button>
                                <button wire:click.prevent="deleteContribution('hdmf_mp2')"
                                    class="text-red-500 cursor-pointer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-6">
                            @if (count($mp2Entries) > 0)
                                @foreach ($mp2Entries as $index => $entry)
                                    <div class="w-full mt-4">
                                        <div class="w-full flex justify-end px-2">
                                            <button
                                                wire:click.prevent="deleteAccount({{ $selectedEmployee }}, '{{ $entry['account_number'] }}')"
                                                class="text-red-500 cursor-pointer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <div class="w-full flex gap-2 mb-2">
                                            <div class="flex flex-col flex-1">
                                                <label class="block  text-gray-700">Account Number</label>
                                                <input type="text"
                                                    wire:model.defer="mp2Entries.{{ $index }}.account_number"
                                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                            </div>
                                            <div class="flex flex-col flex-1">
                                                <label class="block  text-gray-700">Membership Program</label>
                                                <input type="text"
                                                    wire:model.defer="mp2Entries.{{ $index }}.mem_program"
                                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                            </div>
                                        </div>

                                        <div class="w-full flex gap-2 mb-2">
                                            <div class="flex flex-col flex-1">
                                                <label class="block  text-gray-700">PERCOV</label>
                                                <input type="text"
                                                    wire:model.defer="mp2Entries.{{ $index }}.percov"
                                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                    disabled>
                                            </div>
                                            <div class="flex flex-col flex-1">
                                                <label class="block  text-gray-700">EE SHARE</label>
                                                <input type="number" min="0" step="0.01"
                                                    wire:model.defer="mp2Entries.{{ $index }}.ee_share"
                                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                            </div>
                                            <div class="flex flex-col flex-1">
                                                <label class="block  text-gray-700">ER SHARE</label>
                                                <input type="number" min="0" step="0.01"
                                                    wire:model.defer="mp2Entries.{{ $index }}.er_share"
                                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                            </div>
                                        </div>

                                        <div class="flex flex-col">
                                            <label class="block  text-gray-700">Remarks</label>
                                            <input type="text"
                                                wire:model.defer="mp2Entries.{{ $index }}.remarks"
                                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="w-full mt-4">
                                    <div class="w-full flex gap-2 mb-2">
                                        <div class="flex flex-col flex-1">
                                            <label class="block  text-gray-700">Account Number</label>
                                            <input type="text" wire:model.defer="mp2Entries.0.account_number"
                                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                        </div>
                                        <div class="flex flex-col flex-1">
                                            <label class="block  text-gray-700">Membership Program</label>
                                            <input type="text" wire:model.defer="mp2Entries.0.mem_program"
                                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                        </div>
                                    </div>

                                    <div class="w-full flex gap-2 mb-2">
                                        <div class="flex flex-col flex-1">
                                            <label class="block  text-gray-700">PERCOV</label>
                                            <input type="text" wire:model.defer="mp2Entries.0.percov"
                                                class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                disabled>
                                        </div>
                                        <div class="flex flex-col flex-1">
                                            <label class="block  text-gray-700">EE SHARE</label>
                                            <input type="number" min="0" step="0.01"
                                                wire:model.defer="mp2Entries.0.ee_share"
                                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                        </div>
                                        <div class="flex flex-col flex-1">
                                            <label class="block  text-gray-700">ER SHARE</label>
                                            <input type="number" min="0" step="0.01"
                                                wire:model.defer="mp2Entries.0.er_share"
                                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                        </div>
                                    </div>

                                    <div class="flex flex-col">
                                        <label class="block  text-gray-700">Remarks</label>
                                        <input type="text" wire:model.defer="mp2Entries.0.remarks"
                                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                @endif


                {{-- CL ------------------------------------------------------ --}}
                @if (in_array('hdmf_cl', $selectedContributions))
                    <div class="mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">HMDF-CL</h1>
                            <button wire:click.prevent="deleteContribution('hdmf_cl')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Application Number</label>
                                <input type="text" wire:model="cl_app_no"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2  "
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Loan Type</label>
                                <input type="text" wire:model="cl_loan_type"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2  "
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="w-full flex gap-2 mb-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Amount</label>
                                <input type="number" min="0" step="0.01" wire:model="cl_amount"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2  "
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Start Term</label>
                                <input type="date" wire:model="cl_start_term"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2  "
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">End Term</label>
                                <input type="date" wire:model="cl_end_term"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2 "
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <label class="block  text-gray-700">Remarks</label>
                            <input type="text" wire:model="cl_remarks"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2  "
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>

                    </div>
                @endif


                {{-- DARECO ------------------------------------------------------ --}}
                @if (in_array('dareco', $selectedContributions))
                    <div class="mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">DARECO</h1>
                            <button wire:click.prevent="deleteContribution('dareco')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block  text-gray-700">Amount</label>
                            <input type="number" wire:model="dareco_amount"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2 mb-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>

                        <div class="flex flex-col">
                            <label class="block  text-gray-700">Remarks</label>
                            <input type="text" wire:model="dareco_remarks"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                @endif


                {{-- SSS | EC | WISP ------------------------------------------------------ --}}
                @if (in_array('sss_ec_wisp', $selectedContributions))
                    <div class="w-full mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">SSS | EC | WISP</h1>
                            <button wire:click.prevent="deleteContribution('sss_ec_wisp')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">SSS</label>
                                <input type="text" wire:model="sss_number"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">EC</label>
                                <input type="text" wire:model="ec_number"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">WISP</label>
                                <input type="text" wire:model="wisp_number"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-2">
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Difference</label>
                                <input type="text" wire:model="difference"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                            <div class="flex flex-col flex-1">
                                <label class="block  text-gray-700">Remarks</label>
                                <input type="text" wire:model="remarks"
                                    class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                @endif



                 {{-- TAX ------------------------------------------------------ --}}
                @if (in_array('tax', $selectedContributions))
                    <div class="mt-6">
                        <div class="w-full flex justify-between bg-gray-300 px-2 item-center mb-2">
                            <h1 class="py-1 text-gray-700 font-bold">TAX</h1>
                            <button wire:click.prevent="deleteContribution('tax')"
                                class="text-red-500 cursor-pointer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="flex flex-col flex-1">
                            <label class="block  text-gray-700">Amount</label>
                            <input type="number" wire:model.live="tax"
                                class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2 mb-2"  step="0.01"
                                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                        </div>
                    </div>
                @endif


                <button 
    type="submit" 
    class="mt-4 mb-4 w-full h-10 bg-slate-700 rounded-md text-white cursor-pointer" 
    @if(is_null($selectedEmployee)) disabled @endif
>
    CONFIRM
</button>

            </form>
        </div>
    </div>


    @if ($showModal)
        <div
            style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 40; display: flex; justify-content: center; align-items: center;">
            <div
                style="background: white; width: 24rem; border-radius: .5rem; overflow: hidden;  font-family: Arial, sans-serif;">
                <div
                    style="background-color: #f56565; padding: 2rem; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="color: white; font-size: 2.5rem;"></i>
                </div>
                <div style="padding: 1.5rem 2rem; color: #2d3748; text-align: center;">
                    <h2 class="text-lg text-gray-700 font-bold mb-6 mt-4">REMINDER</h2>
                    <p style="font-size: 0.9rem; color: #718096; margin-bottom: 1.5rem;">
                        The Pag-IBIG loan term of <b class="text-gray-600 font-black">{{ $nameMpl }}</b> will end
                        tomorrow. Kindly ensure all necessary payments are completed.
                    </p>
                    <button wire:click="closeModal"
                        class="bg-red-500 text-white rounded-lg py-2 cursor-pointer w-full">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

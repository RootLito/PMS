<div class="flex gap-10">
    <div class="w-1/2 flex flex-col bg-white rounded-xl p-6 h-96 overflow-auto">
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
                            <td class="px-4 py-2">{{ $employee->middle_initial }}</td>
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
        <div class="mt-auto">
            {{ $employees->links() }}
        </div>
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

            <div class="flex flex-col mt-6">
                <label class="block text-xs text-gray-700">Pag-IBIG ID/RTN</label>
                <div class="w-full flex justify-between items-center">
                    <input type="text" wire:model.live="pag_ibig_id_rtn"
                        class="block w-1/2 h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>


            {{-- PI/MC ------------------------------------------------------ --}}
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
                            class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2" disabled>
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


            {{-- MPL ------------------------------------------------------ --}}

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
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
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
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Note</label>
                        <input type="text" wire:model="notes"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>



            {{-- MP2 ------------------------------------------------------ --}}
            <div class="mt-6">
                <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">HMDF-MP2</h1>
                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Account Number</label>
                        <input type="text" wire:model="mp2_account_number"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Membership Program</label>
                        <input type="text" wire:model="mp2_mem_program"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">PERCOV</label>
                        <input type="text" min="0" step="0.01" wire:model="mp2_percov"
                            class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            disabled>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">EE SHARE</label>
                        <input type="number" min="0" step="0.01" wire:model="ee_share"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">ER SHARE</label>
                        <input type="number" min="0" step="0.01" wire:model="er_share"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>
                <div class="flex flex-col">
                    <label class="block text-xs text-gray-700">Remarks</label>
                    <input type="text" wire:model="mp2_remarks"
                        class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>




            {{-- CL ------------------------------------------------------ --}}
            <div class="mt-6">
                <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">HMDF-CL</h1>
                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Application No./Agreement No.</label>
                        <input type="text" wire:model="cl_app_no"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Loan Type</label>
                        <input type="text" wire:model="cl_loan_type"
                            class="block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Amount</label>
                        <input type="text" min="0" step="0.01" wire:model="cl_amount"
                            class="text-xs block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2">
                    </div>
                </div>

                <div class="w-full flex gap-2 mb-2">


                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Start Term</label>
                        <input type="date" wire:model="cl_start_term"
                            class="text-sm block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">End Term</label>
                        <input type="date" wire:model="cl_end_term"
                            class="text-sm block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
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

            {{-- DARECO ------------------------------------------------------ --}}
            <div class="mt-6">
                <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">DARECO</h1>

                <div class="w-full flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Amount</label>
                        <input type="number" min="0" step="0.01" wire:model="dareco_amount"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>

                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">Remarks</label>
                        <input type="text" wire:model="dareco_remarks"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

            </div>


            {{-- SSS | EC | WISP ------------------------------------------------------ --}}
            <div class="w-full mt-6">
                <h1 class="py-1 px-2 bg-gray-300 text-gray-700 font-bold mb-2">SSS | EC | WISP</h1>
                <div class="flex gap-2 mb-2">
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">SSS</label>
                        <input type="number" min="0" step="0.01" wire:model="contributions.sss.amount"
                            class="my-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">EC</label>
                        <input type="number" min="0" step="0.01" wire:model="contributions.ec.amount"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>


                </div>

                <div class="flex gap-2 mb-2">



                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700">WISP</label>
                        <input type="number" min="0" step="0.01" wire:model="contributions.wisp.amount"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                    <div class="flex flex-col flex-1">
                        <label class="block text-xs text-gray-700"> Difference </label>
                        <input type="number" min="0" step="0.01" wire:model="difference"
                            class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                            {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label class="block text-xs text-gray-700">Remarks</label>
                    <input type="text" wire:model="remarks"
                        class=" block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                        {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                </div>
            </div>

            <button type="submit"
                class="mt-4 mb-4 w-full h-10 bg-slate-700 rounded-md text-white {{ is_null($selectedEmployee) ? 'cursor-default opacity-50' : 'cursor-pointer' }}"
                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                CONFIRM
            </button>
        </form>
    </div>
</div>

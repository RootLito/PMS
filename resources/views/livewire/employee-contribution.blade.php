<div class="flex-1 grid grid-cols-2 gap-10">
    <div class="flex flex-col bg-white rounded-xl p-6">
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

    <div class="flex flex-col gap-10 bg-white rounded-xl p-6">
        <form wire:submit.prevent="saveContributions">

            <h1>EMPLOYEE ID: {{ $selectedEmployee }}</h1>
            <h1 class="mt-6">HDMF-PI</h1>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Amount</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_pi.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="contributions.hdmf_pi.remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>



            <h1 class="mt-6">MPL</h1>


            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Pag-IBIG ID/RTN</label>
                <input type="text" wire:model="contributions.hdmf_mpl.pag_ibig_id_rtn"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Application Number</label>
                <input type="text" wire:model="contributions.hdmf_mpl.app_no"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Loan Type</label>
                <input type="text" wire:model="contributions.hdmf_mpl.loan_type"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Amount</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_mpl.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="contributions.hdmf_mpl.remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Note</label>
                <input type="text" wire:model="contributions.hdmf_mpl.note"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Start Term</label>
                <input type="date" wire:model="contributions.hdmf_mpl.start_te"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">End Term</label>
                <input type="date" wire:model="contributions.hdmf_mpl.end_te"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <h1 class="mt-6">MP2</h1>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">PERCOV</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_mp2.percov"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">EE SHARE</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_mp2.ee_share"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">ER SHARE</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_mp2.er_share"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="mp2_remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>


            <h1 class="mt-6">MC</h1>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Pag-IBIG ID/RTN</label>
                <input type="text" wire:model="contributions.hdmf_cl.pag_ibig_id_rtn"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Application Number</label>
                <input type="text" wire:model="contributions.hdmf_cl.app_no"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Membership Program</label>
                <input type="text" wire:model="contributions.hdmf_cl.mem_program"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Amount</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_cl.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">PERCOV</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_cl.percov"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">EE SHARE</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_cl.ee_share"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">ER SHARE</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.hdmf_cl.er_share"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="contributions.hdmf_cl.remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <h1 class="mt-6">DARECO</h1>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Amount</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.dareco.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="contributions.dareco.remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">SSS</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.sss.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">EC</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.ec.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">WISP</label>
                <input type="number" min="0" step="0.01" wire:model="contributions.wisp.amount"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <div class="flex flex-col">
                <label class="block text-sm text-gray-700">Remarks</label>
                <input type="text" wire:model="remarks"
                    class="mt-1 block w-full h-9 border border-gray-200 bg-gray-50 rounded-md px-2"
                    {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
            </div>

            <button type="submit"
                class="mt-4 w-full h-10 bg-slate-700 rounded-md text-white {{ is_null($selectedEmployee) ? 'cursor-default opacity-50' : 'cursor-pointer' }}"
                {{ is_null($selectedEmployee) ? 'disabled' : '' }}>
                CONFIRM
            </button>

        </form>


    </div>
</div>

<div class="flex-1 flex flex-col bg-white rounded-xl p-6 shadow">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2 mt-4">
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

    <div class="overflow-auto mt-6 mb-2">
        <table class="min-w-full table-auto text-sm">
            <thead class="bg-gray-100 text-left">
                <tr class="border-b border-t border-gray-200">
                    <th class="px-4 py-3 text-nowrap">Status</th>
                    <th class="px-4 py-2 text-nowrap">Last Name</th>
                    <th class="px-4 py-2 text-nowrap">First Name</th>
                    <th class="px-4 py-2 text-nowrap">M.I.</th>
                    <th class="px-4 py-2 text-nowrap">Monthly Rate</th>
                    <th class="px-4 py-2 text-nowrap">Gross</th>
                    <th class="px-4 py-2 text-nowrap">Designation</th>
                    <th class="px-4 py-2 text-nowrap">Office</th>
                    <th class="px-4 py-2 text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer">
                    <td class="px-4 py-2 font-bold">{{ $employee->employment_status }}</td>
                    <td class="px-4 py-2">{{ $employee->last_name }}</td>
                    <td class="px-4 py-2">
                        {{ $employee->first_name }}{{ $employee->suffix ? ' ' . $employee->suffix . '.' : '' }}
                    </td>

                    <td class="px-4 py-2">{{ $employee->middle_initial }}</td>
                    <td class="px-4 py-2">{{ number_format($employee->monthly_rate, 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($employee->gross, 2) }}</td>
                    <td class="px-4 py-2">{{ $employee->designation }}</td>
                    <td class="px-4 py-2">{{ $employee->office_name }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ url('/employee/update', ['id' => $employee->id]) }}"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer"
                            title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>


                        <button
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded flex items-center gap-1 cursor-pointer"
                            title="Delete">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-2 text-center text-gray-500">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>




    </div>
    <div class="mt-auto">
        {{ $employees->links() }}
    </div>
</div>
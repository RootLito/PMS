@extends('layouts.app')

@section('title', 'Employee')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                EMPLOYEE
            </h2>


            <div class="flex">
                <a href="{{ route('employee.new') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        Add Employee
                    </button>
                </a>


            </div>
        </div>


        <div class="flex-1 bg-white rounded-xl p-6 shadow">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <input type="text" placeholder="Search by name..." class="border rounded px-4 py-2 w-full sm:w-1/2"
                    id="searchInput" onkeyup="filterTable()">

                <select class="border rounded px-4 py-2 w-full sm:w-1/4" id="designationFilter" onchange="filterTable()">
                    <option value="">All Designations</option>
                    <option value="A.">A.</option>
                    <option value="B.">B.</option>
                    <option value="L.">L.</option>
                    <option value="G.">G.</option>
                </select>
            </div>

            <div class="overflow-auto mt-10">
                <table class="min-w-full table-auto text-sm" id="employeeTable">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2">Last Name</th>
                            <th class="px-4 py-2">First Name</th>
                            <th class="px-4 py-2">M.I.</th>
                            <th class="px-4 py-2">Monthly Rate</th>
                            <th class="px-4 py-2">Gross</th>
                            <th class="px-4 py-2">Designation</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 text-red-600 font-semibold">Mata</td>
                            <td class="px-4 py-2 text-red-600">Rupert (transferred to FIQU)</td>
                            <td class="px-4 py-2">G.</td>
                            <td class="px-4 py-2 text-red-600">17,505.00</td>
                            <td class="px-4 py-2">-</td>
                            <td class="px-4 py-2">G.</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Pleños</td>
                            <td class="px-4 py-2">Loveliza</td>
                            <td class="px-4 py-2">A.</td>
                            <td class="px-4 py-2">17,505.00</td>
                            <td class="px-4 py-2">8,752.50</td>
                            <td class="px-4 py-2">A.</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Domogho</td>
                            <td class="px-4 py-2">April Karl</td>
                            <td class="px-4 py-2"></td>
                            <td class="px-4 py-2">17,505.00</td>
                            <td class="px-4 py-2">8,752.50</td>
                            <td class="px-4 py-2"></td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Señerez</td>
                            <td class="px-4 py-2">Cendie Jeen</td>
                            <td class="px-4 py-2">B.</td>
                            <td class="px-4 py-2">17,505.00</td>
                            <td class="px-4 py-2">8,752.50</td>
                            <td class="px-4 py-2">B.</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">Alivio</td>
                            <td class="px-4 py-2">Leo</td>
                            <td class="px-4 py-2">L.</td>
                            <td class="px-4 py-2">16,458.00</td>
                            <td class="px-4 py-2">8,229.00</td>
                            <td class="px-4 py-2">L.</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-red-600 font-semibold">Milallos Jr.</td>
                            <td class="px-4 py-2 text-red-600">Carlito (RESIGNED)</td>
                            <td class="px-4 py-2">A.</td>
                            <td class="px-4 py-2 text-red-600">15,275.00</td>
                            <td class="px-4 py-2">-</td>
                            <td class="px-4 py-2">A.</td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection
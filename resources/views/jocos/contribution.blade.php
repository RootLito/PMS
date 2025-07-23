@extends('layouts.app')

@section('title', 'Contribution')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                CONTRIBUTION
            </h2>


            <div class="flex">
                <a href="{{ route('contribution.new') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        Add Contribution
                    </button>
                </a>


            </div>
        </div>


        <div class="flex-1 bg-white rounded-xl p-6 shadow">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <input type="text" placeholder="Search by name..." class="border rounded px-4 py-2 w-full sm:w-1/2"
                    id="searchInput" onkeyup="filterTable()">

                <select class="border rounded px-4 py-2 w-full sm:w-1/4" id="designationFilter" onchange="filterTable()">
                    <option value="">Type</option>
                    <option value="A.">1-15</option>
                    <option value="B.">16-31</option>
                </select>
            </div>


            <div class="overflow-auto mt-10">
                <table class="min-w-full table-auto text-sm " id="contributionTable">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-2 ">Abbreviation</th>
                            <th class="px-4 py-2 ">Name</th>
                            <th class="px-4 py-2 ">Type</th>
                            <th class="px-4 py-2 ">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-2 ">HDMF-PI</td>
                            <td class="px-4 py-2 ">Pag-IBIG Personal Contribution</td>
                            <td class="px-4 py-2 ">1-15</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">HDMF-MPL</td>
                            <td class="px-4 py-2 ">Pag-IBIG Multi-Purpose Loan</td>
                            <td class="px-4 py-2 ">1-15</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">HDMF-MP2</td>
                            <td class="px-4 py-2 ">Pag-IBIG MP2 Savings</td>
                            <td class="px-4 py-2 ">1-15</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">HDMF-CL</td>
                            <td class="px-4 py-2 ">Pag-IBIG Calamity Loan</td>
                            <td class="px-4 py-2 ">1-15</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">DARECO</td>
                            <td class="px-4 py-2 ">Davao del Sur Electric Cooperative</td>
                            <td class="px-4 py-2 ">1-15</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">SS CON</td>
                            <td class="px-4 py-2 ">SSS Contribution</td>
                            <td class="px-4 py-2 ">16-31</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">EC CON</td>
                            <td class="px-4 py-2 ">Employees Compensation</td>
                            <td class="px-4 py-2 ">16-31</td>
                            <td class="px-4 py-2  space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></a>
                                <a href="#" class="text-red-600 hover:text-red-800"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 ">WISP</td>
                            <td class="px-4 py-2 ">SSS WISP (Workers' Investment & Savings Program)</td>
                            <td class="px-4 py-2 ">16-31</td>
                            <td class="px-4 py-2  space-x-2">
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
@extends('layouts.app')

@section('title', 'adjustment')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                PAYROLL SUMMARY
            </h2>


            {{-- <div class="flex">
                <a href="{{ route('contribution.new') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        Add Contribution
                    </button>
                </a>


            </div> --}}
        </div>


        <div class="flex-1 bg-white rounded-xl p-6">

            {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                <input type="text" placeholder="Search by name..." class="border rounded px-4 py-2 w-full sm:w-1/2"
                    id="searchInput" onkeyup="filterTable()">

                <select class="border rounded px-4 py-2 w-full sm:w-1/4" id="designationFilter" onchange="filterTable()">
                    <option value="">Type</option>
                    <option value="A.">1-15</option>
                    <option value="B.">16-31</option>
                </select>
            </div> --}}


            <div class="overflow-auto mt-10">
               
            </div>

        </div>
    </div>
@endsection
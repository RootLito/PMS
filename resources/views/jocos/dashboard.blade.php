@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                PAYROLL OVERVIEW
            </h2>


            <div class="flex">
                <button class="w-12 h-12 bg-slate-700 rounded-md text-white cursor-pointer">
                    <i class="fa-solid fa-sliders"></i>
                </button>
            </div>
        </div>


        <div class="flex-1 flex gap-10">
            <div class="flex-1 flex flex-col gap-10">
                <div class="flex gap-10">
                    <div class="flex-1 h-48 flex bg-white rounded-xl p-6 flex-col justify-between">
                        <h2 class="text-xl">Gross</h2>
                        <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                    </div>
                    <div class="flex-1 h-48 flex bg-white rounded-xl p-6 flex-col justify-between">
                        <h2 class="text-xl">Late/Absences</h2>
                        <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                    </div>
                    <div class="flex-1 h-48 flex bg-white rounded-xl p-6 flex-col justify-between">
                        <h2 class="text-xl">Tax</h2>
                        <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                    </div>
                </div>


                <div class="flex-1 bg-white rounded-xl p-6">
                    <h2 class="text-xl">Contributions</h2>
                    <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                </div>
            </div>


            <div class="w-100 h-full bg-white rounded-xl flex flex-col p-6">
                <div class="flex-1 flex-col ">
                    <h2 class="text-xl">Total Deduction</h2>
                    <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                </div>
                <div class="flex-1 flex-col">
                    <h2 class="text-xl">NET</h2>
                    <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
                </div>
            </div>
        </div>
    </div>
@endsection
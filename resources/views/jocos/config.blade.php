@extends('layouts.app')

@section('title', 'Contribution')

@section('content')
    <div class="flex-1 p-10 ">

        <div class="flex-1 h-full bg-white rounded-xl shadow-sm  flex flex-col justify-center items-center gap-10">
            <div class="flex gap-10 flex-wrap">

                <div class="w-[300px] h-[300px] bg-gray-100 rounded-xl flex flex-col items-center justify-center">
                    <div class="flex flex-col items-center gap-4 text-gray-700">
                        <i class="fa-solid fa-coins text-6xl"></i>
                        <span class="text-lg font-semibold">Monthly Rate</span>
                        <a href="/salary"
                            class="mt-4 px-6 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-semibold transition inline-block">
                            Go to Salary
                        </a>
                    </div>
                </div>

                <div class="w-[300px] h-[300px] bg-gray-100 rounded-xl flex flex-col items-center justify-center">
                    <div class="flex flex-col items-center gap-4 text-gray-700">
                        <i class="fa-solid fa-address-card text-6xl"></i>
                        <span class="text-lg font-semibold">Designation</span>
                        <a href="/designation"
                            class="mt-4 px-6 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-semibold transition inline-block">
                            Go to Designation
                        </a>
                    </div>
                </div>

                <div class="w-[300px] h-[300px] bg-gray-100 rounded-xl flex flex-col items-center justify-center">
                    <div class="flex flex-col items-center gap-4 text-gray-700">
                        <i class="fa-solid fa-user-tie text-6xl"></i>
                        <span class="text-lg font-semibold">Position</span>
                        <a href="/position"
                            class="mt-4 px-6 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-semibold transition inline-block">
                            Go to Position
                        </a>
                    </div>
                </div>

                <div class="w-[300px] h-[300px] bg-gray-100 rounded-xl flex flex-col items-center justify-center">
                    <div class="flex flex-col items-center gap-4 text-gray-700">
                        <i class="fa-solid fa-pen-nib text-6xl"></i>
                        <span class="text-lg font-semibold">Signatory</span>
                        <a href="/signatory"
                            class="mt-4 px-6 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-sm font-semibold transition inline-block">
                            Go to Signatory
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

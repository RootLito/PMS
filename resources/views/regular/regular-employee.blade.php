@extends('layouts.app')

@section('title', 'Employee')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                REGULAR EMPLOYEE
            </h2>
            <div class="flex">
                <a href="{{ route('regular-employee.new-regular') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        Add Employee <i class="fa-solid fa-plus"></i>
                    </button>
                </a>
            </div>
        </div>
        @livewire('show-regular-employee')
    </div>
@endsection
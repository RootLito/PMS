@extends('layouts.app')

@section('title', 'Archive')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                PAYROLL RECORDS
            </h2>
            {{-- <div class="flex">
                <a href="{{ route('employee.new') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        Add Employee <i class="fa-solid fa-plus"></i>
                    </button>
                </a>
            </div> --}}
        </div>
        @livewire('archive')
    </div>
@endsection

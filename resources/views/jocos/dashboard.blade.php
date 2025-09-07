@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                DASHBOARD OVERVIEW
            </h2>
            {{-- <div class="flex">
                <button class="w-12 h-12 bg-slate-700 rounded-md text-white cursor-pointer">
                    <i class="fa-solid fa-sliders"></i>
                </button>
            </div> --}}
        </div>
        @livewire('dashboard-data')
    </div>
@endsection
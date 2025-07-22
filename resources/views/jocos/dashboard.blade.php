@extends('layouts.app')

@section('title', 'dashboard')

@section('content')
    <div class="flex-1 flex p-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold">
                Payroll Overview
            </h2>


            <div class="flex">
                <button class="w-53 h-12 bg-red-400 rounded-md text-white">
                    Filter
                </button>
            </div>
        </div>

    </div>
@endsection
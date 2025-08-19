@extends('layouts.app')

@section('title', 'Contribution')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 relative">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                EMPLOYEE CONTRIBUTION
            </h2>


        </div>
        @livewire('employee-contribution')
    </div>
@endsection
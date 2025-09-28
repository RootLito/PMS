@extends('layouts.app')

@section('title', 'Designation')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 relative">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                DESIGNATION
            </h2>


        </div>
        @livewire('employee-designation')
    </div>
@endsection

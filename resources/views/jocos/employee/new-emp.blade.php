@extends('layouts.app')

@section('title', 'New Employee')

@section('content')
    <div class="flex-1 p-10 grid place-items-center">
        <div class="w-200 bg-white p-6 rounded-xl">
            @livewire('employee-form')
        </div>
    </div>
@endsection
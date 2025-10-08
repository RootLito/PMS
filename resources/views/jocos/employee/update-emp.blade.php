@extends('layouts.app')

@section('title', 'Update Employee')

@section('content')
    <div class="flex-1 p-10 grid place-items-center">
        <div class="w-200 bg-white p-6 rounded-xl">
            @livewire('update-employee', ['id' => $id])
        </div>
    </div>
@endsection
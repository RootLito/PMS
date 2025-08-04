@extends('layouts.app')

@section('title', 'Payroll')

@section('content')
<div class="flex-1 flex flex-col p-10 gap-10">
    <div class="flex-1 ">
        @livewire('payroll-summary')
    </div>
</div>
@endsection
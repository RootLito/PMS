@extends('layouts.app')

@section('title', 'Payroll')

@section('content')
<div class="flex-1 flex flex-col p-10 gap-10">
    <div class="flex-1 bg-white">
        <div class="min-h-[calc(100vh-4rem)] relative overflow-auto py-6">
            <div class="min-h-screen sticky top-0 left-0 mb-6 px-4">
                @livewire('payroll-summary')
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Salary')

@section('content')
<div class="flex-1 flex flex-col p-10 gap-10">
    <div class="flex-1 ">
        @livewire('salary-encode')
    </div>
</div>
@endsection
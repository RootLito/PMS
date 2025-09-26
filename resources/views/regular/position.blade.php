@extends('layouts.app')

@section('title', 'Position')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 relative">
        @livewire('position-data')
    </div>
@endsection

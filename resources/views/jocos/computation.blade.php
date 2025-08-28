@extends('layouts.app')

@section('title', 'Contribution')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 relative">
        
        @livewire('raw-computation')
    </div>
@endsection

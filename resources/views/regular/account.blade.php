@extends('layouts.app')

@section('title', 'Account Management')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10 relative">
        @livewire('accounts')
    </div>
@endsection

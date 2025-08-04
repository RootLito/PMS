@extends('layouts.app')

@section('title', 'Contribution')

@section('content')
    <div class="flex-1 flex flex-col p-10 gap-10">
        <div class="w-full flex justify-between">
            <h2 class="text-5xl font-bold text-gray-700">
                RAW COMPUTATION
            </h2>


            <div class="flex">
                <a href="{{ route('computation.voucher') }}">
                    <button class="w-53 h-12 bg-slate-700 rounded-md text-white cursor-pointer hover:bg-slate-500 ">
                        <i class="fa-solid fa-ticket mr-2"></i>View Vouchers 
                    </button>
                </a>
            </div>
        </div>
        @livewire('raw-computation')
    </div>
@endsection
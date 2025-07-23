@extends('layouts.app')

@section('title', 'New Contribution')

@section('content')
    <div class="flex-1 p-10 grid place-items-center">
        <div class="w-150 bg-white p-6 rounded-xl">
            <form action="" class="space-y-4">
                <div class="flex items-center gap-2 mb-10">
                    <a href="{{ route('contribution') }}" class="text-red-400">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl text-gray-700 font-bold">
                        New Contribution
                    </h2>
                </div>

                <div>
                    <label for="contribution" class="block text-sm text-gray-700">Abbreviation <span
                            class="text-red-400">*</span></label>
                    <input type="text" id="contribution" name="contribution"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 " required>
                </div>

                <div>
                    <label for="contribution" class="block text-sm text-gray-700">Contribution Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" id="contribution" name="contribution"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 " required>
                </div>

                <div>
                    <label for="type" class="block text-sm text-gray-700">Type <span class="text-red-400">*</span></label>
                    <select id="type" name="type"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" required>
                        <option value="" disabled selected>Select type</option>
                        <option value="1-15">1-15</option>
                        <option value="16-30">16-31</option>
                    </select>
                </div>




                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
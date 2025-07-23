@extends('layouts.app')

@section('title', 'New Employee')

@section('content')
    <div class="flex-1 p-10 grid place-items-center">
        <div class="w-150 bg-white p-6 rounded-xl">
            <form action="" class="space-y-4">
                <div class="flex items-center gap-2 mb-10">
                    <a href="{{ route('employee') }}" class="text-red-400">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl text-gray-700 font-bold">
                        New Employee
                    </h2>
                </div>

                <div>
                    <label for="lastname" class="block text-sm text-gray-700">Last Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" id="lastname" name="lastname"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" required>
                </div>

                <div>
                    <label for="firstname" class="block text-sm text-gray-700">First Name <span
                            class="text-red-400">*</span></label>
                    <input type="text" id="firstname" name="firstname"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" required>
                </div>

                <div>
                    <label for="middle_initial" class="block text-sm text-gray-700">Middle Initial</label>
                    <input type="text" id="middle_initial" name="middle_initial" maxlength="1"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                </div>

                <div>
                    <label for="monthly_rate" class="block text-sm text-gray-700">Monthly Rate <span
                            class="text-red-400">*</span></label>
                    <input type="number" id="monthly_rate" name="monthly_rate" step="0.01"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" required>
                </div>

                <div>
                    <label for="gross" class="block text-sm text-gray-700">Gross</label>
                    <input type="number" id="gross" name="gross" step="0.01"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2">
                </div>

                <div>
                    <label for="designation" class="block text-sm text-gray-700">Designation <span
                            class="text-red-400">*</span></label>
                    <input type="text" id="designation" name="designation"
                        class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" required>
                </div>


                <div class="pt-4">
                    <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
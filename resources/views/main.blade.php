<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <title>@yield('title', 'pms')</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="w-full h-screen flex flex-col justify-center items-center bg-gray-200">
        <form action="{{ route('login') }}" method="POST"
            class="w-[360px] bg-white rounded-xl shadow-sm flex flex-col px-6 py-10">
            @csrf

            <img src="{{ asset('images/bfar.png') }}" alt="bfar logo" width="200px" class="self-center">
            <h1 class="text-xl text-center text-gray-700 font-bold">PAYROLL MANAGEMENT SYSTEM</h1>
            <p class="text-xl mb-4 mt-6 text-gray-700 font-semibold">Login</p>
            @error('username')
                <p class="text-red-600 text-xs mb-4 block w-full py-4 bg-red-200 px-6 text-center rounded-xl">{{ $message }}</p>
            @enderror

            <label for="username" class="block text-xs text-gray-700">Username</label>
            <input type="text" id="username" name="username"
                class="text-sm mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2"
                placeholder="Username" value="{{ old('username') }}" required autofocus>

            <label for="password" class="block text-xs text-gray-700 mt-4">Password</label>
            <div x-data="{ show: false }" class="relative mt-1">
                <input :type="show ? 'text' : 'password'" id="password" name="password"
                    class="text-sm block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2 pr-10"
                    placeholder="Password" required>
                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-2 flex items-center px-2 text-gray-600 hover:text-gray-900"
                    tabindex="-1">
                    <template x-if="show">
                        <i class="fa-solid fa-eye"></i>
                    </template>
                    <template x-if="!show">
                        <i class="fa-solid fa-eye-slash"></i>
                    </template>
                </button>
            </div>
            <button type="submit"
                class="text-xs mt-6 w-full h-10 bg-green-700 hover:bg-green-900 text-white font-semibold rounded-md transition duration-150 ease-in-out cursor-pointer">
                Login
            </button>
            <div class="text-center text-[8px] mt-8">
                © 2025 Bureau of Fisheries and Aquatic Resources. All rights reserved.
            </div>
        </form>
    </div>
</body>
</html>

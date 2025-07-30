<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>@yield('title', 'pms')</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body>
    <div class="w-full min-h-screen bg-red-100 flex relative">
        @if ($errors->any())
        <div
            class="absolute top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 max-w-sm">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session()->has('message'))
        <div
            class="absolute top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50 max-w-sm">
            {{ session('message') }}
        </div>
        @endif


        <div class="w-56 h-screen sticky top-0 bg-white">
            @include('partials.sidebar')
        </div>


        <div class="flex-1 min-h-screen bg-gray-200 flex flex-col">
            @include('partials.navbar')
            @yield('content')
        </div>
    </div>

    @livewireScripts
    @stack('scripts') 
</body>
</html>
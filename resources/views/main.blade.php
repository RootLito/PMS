<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'pms')</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="w-full h-screen flex flex-col justify-center items-center">
        <H2 class="text-2xl font-bold mb-5">Payroll Management System</H2>
        <p class="text-center mb-5">
            Manage employee salaries, benefits, tax filings, and reporting—seamlessly and accurately.
        </p>
        <small class="mb-2">
            Designed exclusively for BFAR
        </small>
        <a href="/dashboard"
            class="inline-block bg-blue-600 text-white text-sm font-medium px-6 py-3 rounded-lg shadow hover:bg-blue-700">
            Proceed Testing
        </a>
    </div>
</body>

</html>
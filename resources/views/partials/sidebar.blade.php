<div class="h-screen flex flex-col p-6 border-r border-gray-200">
    <div class="w-full flex flex-col gap-4">
        <img src="{{ asset('images/bfar.png') }}" alt="BFAR Logo" class="mx-auto w-auto h-32">

        <h2 class="text-5xl text-center font-black text-slate-700">P.M.S.</h2>
    </div>
    <div class="mt-16 flex flex-col gap-2">
        <a href="/dashboard"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('dashboard*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-house ml-5 text-lg"></i>
            Dashboard
        </a>
        <a href="/employee"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('employee*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fas fa-user-group ml-5 text-lg"></i>
            Employee
        </a>
        <a href="/contribution"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('contribution*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-money-bill-wave ml-5 text-lg"></i>
            Contribution
        </a>
        <a href="/computation"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('computation*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-calculator ml-5 text-lg"></i>
            Computation
        </a>
        <a href="/payroll"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('payroll*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-money-check ml-5 text-lg"></i>
            Payroll
        </a>
        <a href="/attendance"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('attendance*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-calendar-check ml-5 text-lg"></i> Attendance
        </a>
        <a href="/archive"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
                {{ request()->is('archive*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-box-archive ml-5 text-lg"></i>
            Archive
        </a>
        <div x-data="{ open: {{ request()->is('configuration*') ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open"
                class="w-full flex items-center gap-2 h-10 px-4 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all cursor-pointer">
                <i class="fa-solid fa-cog text-lg"></i>
                <span class="flex-1 text-left">Configuration</span>
                <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-1 space-y-1 bg-gray-100 rounded-lg">
                <a href="/configuration/salary"
                    class="flex items-center gap-2 font-semibold px-2 h-10 text-sm rounded hover:bg-gray-200 transition-all
            {{ request()->is('configuration/salary') ? 'bg-gray-300 font-medium text-gray-800' : 'text-gray-600' }}">
                    <i class="fa-solid fa-coins ml-2 text-lg"></i>
                    Monthly Rate
                </a>
                <a href="/configuration/designation"
                    class="flex items-center gap-2 font-semibold px-2 h-10 text-sm rounded hover:bg-gray-200 transition-all
            {{ request()->is('configuration/designation') ? 'bg-gray-300 font-medium text-gray-800' : 'text-gray-600' }}">
                    <i class="fa-solid fa-address-card ml-2 text-lg"></i>
                    Designation
                </a>
                <a href="/configuration/position"
                    class="flex items-center gap-2 font-semibold px-2 h-10 text-sm rounded hover:bg-gray-200 transition-all
            {{ request()->is('configuration/position') ? 'bg-gray-300 font-medium text-gray-800' : 'text-gray-600' }}">
                    <i class="fa-solid fa-user-tie ml-2 text-lg"></i>
                    Position
                </a>
                <a href="/configuration/signatory"
                    class="flex items-center gap-2 font-semibold px-2 h-10 text-sm rounded hover:bg-gray-200 transition-all
            {{ request()->is('configuration/signatory') ? 'bg-gray-300 font-medium text-gray-800' : 'text-gray-600' }}">
                    <i class="fa-solid fa-pen-nib ml-2 text-lg"></i>
                    Signatory
                </a>
            </div>
        </div>
    </div>
    <form action="" class="mt-auto">
        <button class="w-full h-10 text-sm font-semibold bg-red-400  text-white rounded-lg">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</div>

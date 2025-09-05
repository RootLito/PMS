<div class="h-screen flex flex-col p-6 border-r border-gray-200">
    <div class="w-full flex flex-col gap-4">
        <img src="{{ asset('images/bfar.png') }}" alt="BFAR Logo" class="mx-auto w-auto h-32">

        <h2 class="text-5xl text-center font-black text-slate-700">P.M.S.</h2>
    </div>



    <div class="mt-18 flex flex-col gap-2">
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


        <a href="/salary"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('salary*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-coins ml-5 text-lg"></i>
            Monthly Rate
        </a>

        <a href="/designation"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('designation*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-address-card ml-5 text-lg"></i>
            Designation
        </a>

        <a href="/signatory"
            class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('signatory*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-pen-nib ml-5 text-lg"></i>
            Signatory
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


    </div>

    <form action="" class="mt-auto">
        <button class="w-full h-10 text-sm font-semibold bg-red-400  text-white rounded-lg">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</div>

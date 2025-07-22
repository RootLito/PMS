<div class="h-full flex flex-col p-6">
    <div class="w-full flex flex-col gap-4">
        <div class="mx-auto w-32 h-32 rounded-full bg-slate-700">
        </div>
        <h2 class="text-3xl text-center font-black text-slate-700">PMS</h2>
    </div>



    <div class="mt-18 flex flex-col gap-2">
        <a href="/dashboard" class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('dashboard*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fas fa-home-alt ml-5"></i>
            Dashboard
        </a>

        <a href="/employee" class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('employee*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fas fa-user ml-5"></i>
            Employee
        </a>

        <a href="/adjustment" class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('adjustment*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fa-solid fa-file-pen  ml-5"></i>
            Adjustment
        </a>

        <a href="/contribution" class="flex bg-gray-100 items-center gap-2 h-10 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-200 hover:text-gray-600 transition-all
               {{ request()->is('contribution*') ? 'bg-gray-300 text-gray-700' : '' }}">
            <i class="fas fa-hand-holding-usd ml-5"></i>
            Contribution
        </a>
    </div>





    <form action="" class="mt-auto">
        <button class="w-full h-10 text-sm font-semibold bg-red-400  text-white rounded-lg">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</div>
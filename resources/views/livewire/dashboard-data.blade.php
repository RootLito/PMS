<div class="flex-1 flex flex-col gap-10">
    <div class="w-full h-96 flex gap-10">
        <div class="flex-1 flex bg-white rounded-xl p-6 flex-col justify-between gap-4">
            <h2 class="text-xl">Total Employee</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
        <div class="flex-1 flex bg-white rounded-xl p-6 flex-col justify-between gap-4">
            <h2 class="text-xl">Gross</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
        <div class="flex-1 flex bg-white rounded-xl p-6 flex-col justify-between gap-4">
            <h2 class="text-xl">Gross</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
        <div class="flex-1 flex bg-white rounded-xl p-6 flex-col justify-between gap-4">
            <h2 class="text-xl">Late/Absences</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
        <div class="flex-1 flex bg-white rounded-xl p-6 flex-col justify-between gap-4">
            <h2 class="text-xl">Tax</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
    </div>





    <div class="flex-1 bg-white rounded-xl p-6">
        <div class="flex-1 ">
            <h2 class="text-xl">Contributions</h2>
            <h1 class="text-5xl text-gray-700 font-bold">1,326,921</h1>
        </div>
    </div>




    @if ($showModal)
        <div
            style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 40; display: flex; justify-content: center; align-items: center;">
            <div
                style="background: white; width: 24rem; border-radius: .5rem; overflow: hidden; font-family: Arial, sans-serif;">
                <div
                    style="background-color: #f56565; padding: 2rem; display: flex; justify-content: center; align-items: center;">
                    <i class="fas fa-exclamation-triangle" style="color: white; font-size: 2.5rem;"></i>
                </div>
                <div style="padding: 1.5rem 2rem; color: #2d3748; text-align: center;">
                    <h2 style="font-weight: 700; font-size: 1.125rem; margin-bottom: 1.5rem; margin-top: 1rem;">REMINDER
                    </h2>

                    <p style="font-size: 0.9rem; color: #718096; margin-bottom: 1.5rem;">
                        The Pag-IBIG loan term of the following employee(s) will end tomorrow. Kindly ensure all
                        necessary payments are completed:
                    </p>

                    <ul
                        style="color: #4a5568; font-weight: 600; margin-bottom: 1.5rem; text-align: left; max-height: 200px; overflow-y: auto; padding-left: 1rem;">
                        @foreach ($reminderData as $reminder)
                            <div class="w-full flex justify-between">
                                <li>• {{ $reminder['full_name'] }}</li>
                                {{ $reminder['end_term_date'] }}
                            </div>
                        @endforeach
                    </ul>

                    <button wire:click="closeModal" class="bg-red-400"
                        style="color: white; border-radius: 0.5rem; padding: 0.5rem 1rem; cursor: pointer; width: 100%; font-weight: 600;">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

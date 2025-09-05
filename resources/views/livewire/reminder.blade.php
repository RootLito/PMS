<div class="">
    <div class="relative inline-block">
        <button wire:click.prevent='reminderToggle' class="cursor-pointer relative">
            <i class="fa-solid fa-bell text-3xl text-slate-700"></i>

            @if (count($reminderData) > 0)
                <span
                    style="
                        position: absolute;
                        top: -4px;
                        right: -10px;
                        background-color: #dc2626;
                        color: white;
                        font-size: 0.75rem;
                        font-weight: 700;
                        border-radius: 9999px;
                        padding: 0 8px;
                        min-width: 20px;
                        height: 20px;
                        line-height: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">
                    {{ count($reminderData) }}
                </span>
            @endif
        </button>
    </div>
    @if ($reminder)
        <div class="absolute bg-white shadow-sm rounded-md" style="top: 55px; right: 0; width: 500px;">
            <div class="w-full py-2 px-4 border-b border-gray-200 text-sm font-bold text-gray-700">
                Reminder
            </div>

            <div class="w-full py-2 px-4 text-sm">
                @if (count($reminderData) > 0)
                    <ul>
                        @foreach ($reminderData as $reminder)
                            <li class="py-2">
                                The Pag-IBIG loan term of
                                <u><a href="{{ route('contribution', ['employee_id' => $reminder['employee_id']]) }}"
                                        class="text-blue-600 underline font-semibold hover:text-blue-800">
                                        {{ $reminder['full_name'] }}
                                    </a></u>
                                will end <b>{{ $reminder['end_term_date'] }}</b>.
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-center py-4">No reminders yet.</p>
                @endif
            </div>

        </div>
    @endif
</div>

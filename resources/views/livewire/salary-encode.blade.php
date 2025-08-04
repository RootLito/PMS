<div class="h-full flex-1 flex gap-10 ">
    <div class="h-full flex flex-col w-100 p-6 rounded-xl bg-white">
        <h1 class="text-2xl font-bold mb-2">{{ $isUpdating ? 'Update Salary' : 'Create Salary' }}</h1>

        <form wire:submit.prevent="save" class="w-full flex flex-col">
            <div class="mt-2">
                <label for="monthly_rate" class="block text-sm text-gray-700">Monthly Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="monthly_rate" wire:model.live="monthly_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01">
                @error('monthly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="daily_rate" class="block text-sm text-gray-700">Daily Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="daily_rate" wire:model.live="daily_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('daily_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="halfday_rate" class="block text-sm text-gray-700">Halfday Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="halfday_rate" wire:model.live="halfday_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('halfday_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="hourly_rate" class="block text-sm text-gray-700">Hourly Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="hourly_rate" wire:model.live="hourly_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('hourly_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="mt-2">
                <label for="per_min_rate" class="block text-sm text-gray-700">Per Minute Rate <span
                        class="text-red-400">*</span></label>
                <input type="number" id="per_min_rate" wire:model.live="per_min_rate"
                    class="mt-1 block w-full h-10 border border-gray-200 bg-gray-50 rounded-md px-2" step="0.01"
                    disabled>
                @error('per_min_rate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button type="submit"
                class="w-full bg-slate-700 text-white py-2 rounded-md hover:bg-slate-500 cursor-pointer mt-4">
                {{ $isUpdating ? 'Update' : 'Save' }}
            </button>
        </form>

        <div class="h-24 bg-gray-100 p-6  mt-10 rounded-lg">
            <h2 class="text-gray-400 font-semibold">Reminder</h2>
        </div>
    </div>

    <div class="flex-1 flex flex-col p-6 rounded-xl bg-white h-full overflow-auto">
        <h2 class="text-xl font-bold mb-4">Salary Records</h2>
        <div class="max-h-[400px] overflow-auto">
            <table class="min-w-full mt-4 table-auto text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr class="border-b border-t border-gray-200">
                        <th class="px-4 py-3 text-nowrap">Monthly</th>
                        <th class="px-4 py-2 text-nowrap">Daily</th>
                        <th class="px-4 py-2 text-nowrap">Halfday</th>
                        <th class="px-4 py-2 text-nowrap">Hourly</th>
                        <th class="px-4 py-2 text-nowrap">Per Minute</th>
                        <th class="px-4 py-2 text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salaries as $salary)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="px-4 py-2">{{ number_format($salary->monthly_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->daily_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->halfday_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->hourly_rate, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($salary->per_min_rate, 2) }}</td>
                            <td class="px-4 py-2 text-nowrap">
                                <button wire:click="edit({{ $salary->id }})"
                                    class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-400">Edit</button>
                                <button wire:click="delete({{ $salary->id }})"
                                    class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-400">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-auto">
            {{ $salaries->links() }}
        </div>
    </div>
</div>

@if (session()->has('message'))
    <div class="mt-4 text-green-600">
        {{ session('message') }}
    </div>
@endif
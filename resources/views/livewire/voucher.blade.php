<div class="flex-1 bg-white p-6 rounded-xl">
    <div class="w-full flex justify-between items-center mb-10">
        <div class="flex items-center gap-2">
            <a href="{{ route('computation') }}" class="text-red-400">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-xl text-gray-700 font-bold">
                Voucher List
            </h2>
        </div>

        <div class="flex gap-2">
            <select wire:model="month" class="px-2 py-1 rounded border border-gray-300 shadow-sm cursor-pointer">
                <option value="" disabled>Select Month</option>
                @foreach($months as $key => $name)
                <option value="{{ $key }}">{{ $name }}</option>
                @endforeach
            </select>

            <select wire:model="year" class="px-2 py-1 rounded border border-gray-300 shadow-sm cursor-pointer">
                <option value="" disabled>Select Year</option>
                @foreach($years as $year)
                <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>

            <select wire:model="cutoff" class="px-4 py-1 rounded border border-gray-300 shadow-sm cursor-pointer">
                <option value="" disabled>Select Cutoff</option>
                <option value="1st">1st Cutoff (1-15)</option>
                <option value="2nd">2nd Cutoff (16-31)</option>
            </select>

            <button wire:click="proceed"
                class="bg-blue-700 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-blue-600 text-xs">
                <i class="fa-solid fa-sliders mr-1"></i> Filter
            </button>

            <button wire:click="resetFilters"
                class="bg-gray-500 text-white font-semibold px-4 py-1 rounded cursor-pointer hover:bg-gray-400 text-xs">
                <i class="fa-solid fa-rotate-left mr-1"></i> Reset
            </button>
        </div>


    </div>

    <table class="min-w-full table-auto text-sm mt-4">
        <thead class="bg-gray-100 text-left">
            <tr class="border-b border-t border-gray-200">
                <th class="px-4 py-3 text-nowrap">Designation</th>
                <th class="px-4 py-3 text-nowrap">Total Net Pay</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($netPayTotals as $designation => $totalNetPay)
            <tr class="border-b border-gray-200 hover:bg-gray-100 cursor-pointer">
                <td class="px-4 py-2">{{ $designation }}</td>
                <td class="px-4 py-2">{{ number_format($totalNetPay, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="px-4 py-2">No data found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
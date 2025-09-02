<div class="mb-4 flex items-center">
    <form method="GET" action="" class="flex items-center w-full">
        <label for="purok" class="mr-2 text-green-700 font-semibold">
            <svg class="inline w-5 h-5 mr-1 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
        </label>
        <select name="purok" id="purok" onchange="this.form.submit()" class="border border-green-300 rounded px-3 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-400 bg-green-50 text-green-700 font-semibold">
            <option value="">All Puroks</option>
            @foreach($puroks as $purok)
                <option value="{{ $purok->id }}" {{ request('purok') == $purok->id ? 'selected' : '' }}>
                    {{ $purok->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

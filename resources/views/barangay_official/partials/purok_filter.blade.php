<div class="mb-4">
    <form method="GET" action="" class="w-64">
        <select name="purok" id="purok" onchange="this.form.submit()" class="w-full border border-green-300 rounded px-3 py-2 focus:ring-2 focus:ring-green-200 focus:border-green-400 bg-green-50 text-green-700 font-semibold">
            <option value="">All Puroks</option>
            @foreach($puroks as $purok)
                <option value="{{ $purok->id }}" {{ request('purok') == $purok->id ? 'selected' : '' }}>
                    {{ $purok->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

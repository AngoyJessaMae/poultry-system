<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Configure {{ $station->station_code ?? $station->name }} Feeding</h2></x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg p-6">
        @if(session('success'))<div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('manager.stations.feeding-config.update', $station) }}">@csrf @method('PUT')
            <label class="block font-medium text-sm text-gray-700" for="method">Feeding method</label><select id="method" name="method" class="block mt-1 w-full border-gray-300 rounded-md"><option value="unlimited" @selected(old('method', $config?->method) === 'unlimited')>Unlimited</option><option value="four_times_daily" @selected(old('method', $config?->method) === 'four_times_daily')>Four times daily</option><option value="twice_daily" @selected(old('method', $config?->method) === 'twice_daily')>Twice daily</option></select>
            <p class="mt-4 text-sm text-gray-600">Enter exactly four times for four-times daily, two times for twice daily, and no times for unlimited. Use 24-hour HH:MM format.</p>
            @for($i = 0; $i < 4; $i++)<div class="mt-3"><label class="block text-sm text-gray-700">Session {{ $i + 1 }}</label><input type="time" name="schedule_times[]" value="{{ old('schedule_times.' . $i, (($config?->schedule_times ?? [])[$i] ?? '')) }}" class="border-gray-300 rounded-md"></div>@endfor
            <x-input-error :messages="$errors->get('schedule_times')" class="mt-2" /><x-primary-button class="mt-5">Save Configuration</x-primary-button>
        </form>
    </div></div></div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Station') }}
            </h2>
            <a href="{{ route('worker.stations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Stations') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm text-orange-900">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 text-lg" aria-hidden="true">ⓘ</span>
                            <div>
                                <h3 class="font-semibold">Before you add this station</h3>
                                <p class="mt-1">The station identifier is the station's short code. It helps the system connect this station to batches, feeding records, feed prices, expenses, and reports.</p>
                                <p class="mt-1">Select one of the available codes: <strong>S1</strong>, <strong>S2</strong>, or <strong>S3</strong>. Do not reuse a code for another station.</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('worker.stations.store') }}">
                        @csrf

                        <!-- Station Identifier -->
                        <div>
                            <x-input-label for="station_code" :value="__('Station Identifier')" />
                            <select id="station_code" name="station_code" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required autofocus @disabled(count($stationCodes) === 0)>
                                <option value="">Select a station identifier</option>
                                @foreach($stationCodes as $stationCode)
                                    <option value="{{ $stationCode }}" @selected(old('station_code') === $stationCode)>{{ $stationCode }}</option>
                                @endforeach
                            </select>
                            @if(count($stationCodes) > 0)
                                <p class="mt-1 text-sm text-gray-600">Only S1, S2, and S3 are available because the system uses these codes to manage feeding.</p>
                            @else
                                <p class="mt-1 text-sm text-red-600">All station identifiers are already in use. Rename or update an existing station instead of creating S4 or another code.</p>
                            @endif
                            <x-input-error :messages="$errors->get('station_code')" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Station Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Capacity -->
                        <div class="mt-4">
                            <x-input-label for="capacity" :value="__('Capacity')" />
                            <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="old('capacity')" required />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <!-- Min Age -->
                        <div class="mt-4">
                            <x-input-label for="min_age_days" :value="__('Minimum Age (Days)')" />
                            <x-text-input id="min_age_days" class="block mt-1 w-full" type="number" name="min_age_days" :value="old('min_age_days')" required />
                            <x-input-error :messages="$errors->get('min_age_days')" class="mt-2" />
                        </div>

                        <!-- Max Age -->
                        <div class="mt-4">
                            <x-input-label for="max_age_days" :value="__('Maximum Age (Days)')" />
                            <x-text-input id="max_age_days" class="block mt-1 w-full" type="number" name="max_age_days" :value="old('max_age_days')" required />
                            <x-input-error :messages="$errors->get('max_age_days')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4" :disabled="count($stationCodes) === 0">
                                {{ __('Submit for Manager Approval') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

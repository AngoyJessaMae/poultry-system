<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Batch') }}
            </h2>
            <a href="{{ route('worker.batches.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Batches') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.batches.store') }}">
                        @csrf

                        <!-- Batch Code -->
                        <div>
                            <x-input-label for="batch_code" :value="__('Batch Code')" />
                            <x-text-input id="batch_code" class="block mt-1 w-full" type="text" name="batch_code" :value="old('batch_code')" required autofocus />
                            <x-input-error :messages="$errors->get('batch_code')" class="mt-2" />
                        </div>

                        <!-- Station -->
                        <div class="mt-4">
                            <x-input-label for="station_id" :value="__('Station')" />
                            <select id="station_id" name="station_id" class="block mt-1 w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-md shadow-sm">
                                @foreach(\App\Models\Station::all() as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('station_id')" class="mt-2" />
                        </div>

                        <!-- Arrival Date -->
                        <div class="mt-4">
                            <x-input-label for="arrival_date" :value="__('Arrival Date')" />
                            <x-text-input id="arrival_date" class="block mt-1 w-full" type="date" name="arrival_date" :value="old('arrival_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('arrival_date')" class="mt-2" />
                        </div>

                        <!-- Initial Quantity -->
                        <div class="mt-4">
                            <x-input-label for="initial_quantity" :value="__('Initial Quantity')" />
                            <x-text-input id="initial_quantity" class="block mt-1 w-full" type="number" name="initial_quantity" :value="old('initial_quantity')" required min="1" />
                            <x-input-error :messages="$errors->get('initial_quantity')" class="mt-2" />
                        </div>

                        <!-- Initial Weight (grams) -->
                        <div class="mt-4">
                            <x-input-label for="initial_weight_grams" :value="__('Initial Weight (grams)')" />
                            <x-text-input id="initial_weight_grams" class="block mt-1 w-full" type="number" step="0.01" name="initial_weight_grams" :value="old('initial_weight_grams', 45.00)" required min="0" />
                            <x-input-error :messages="$errors->get('initial_weight_grams')" class="mt-2" />
                        </div>

                        <!-- Feeding Method -->
                        <div class="mt-4">
                            <x-input-label for="feeding_method" :value="__('Feeding Method')" />
                            <select id="feeding_method" name="feeding_method" class="block mt-1 w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-md shadow-sm">
                                <option value="twice_daily" {{ old('feeding_method') == 'twice_daily' ? 'selected' : '' }}>Twice Daily</option>
                                <option value="four_times_daily" {{ old('feeding_method') == 'four_times_daily' ? 'selected' : '' }}>Four Times Daily</option>
                                <option value="unlimited" {{ old('feeding_method') == 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                            </select>
                            <x-input-error :messages="$errors->get('feeding_method')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mt-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-brand-orange focus:ring-brand-orange rounded-md shadow-sm">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="sold_out" {{ old('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Batch') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
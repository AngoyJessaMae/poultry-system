<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Growth Record') }}
            </h2>
            <a href="{{ route('worker.growth-records.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Growth Records') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.growth-records.store') }}">
                        @csrf

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Recorded Date -->
                        <div class="mt-4">
                            <x-input-label for="recorded_date" :value="__('Recorded Date')" />
                            <x-text-input id="recorded_date" class="block mt-1 w-full" type="date" name="recorded_date" :value="old('recorded_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('recorded_date')" class="mt-2" />
                        </div>

                        <!-- Age (Days) -->
                        <div class="mt-4">
                            <x-input-label for="age_days" :value="__('Age (Days)')" />
                            <x-text-input id="age_days" class="block mt-1 w-full" type="number" name="age_days" :value="old('age_days')" required />
                            <x-input-error :messages="$errors->get('age_days')" class="mt-2" />
                        </div>

                        <!-- Is Below Expected? -->
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_below_expected" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" @if(old('is_below_expected')) checked @endif>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Growth is below expected') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_below_expected')" class="mt-2" />
                        </div>

                        <!-- Average Weight -->
                        <div class="mt-4">
                            <x-input-label for="average_weight_grams" :value="__('Average Weight (g)')" />
                            <x-text-input id="average_weight_grams" class="block mt-1 w-full" type="number" step="0.01" name="average_weight_grams" :value="old('average_weight_grams')" required />
                            <x-input-error :messages="$errors->get('average_weight_grams')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="notes">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
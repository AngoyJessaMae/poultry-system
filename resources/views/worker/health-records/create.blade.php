<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Health Record') }}
            </h2>
            <a href="{{ route('worker.health-records.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Health Records') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.health-records.store') }}">
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

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 mt-4">
                            <div>
                                <x-input-label for="affected_count" :value="__('Affected Chickens')" />
                                <x-text-input id="affected_count" class="block mt-1 w-full" type="number" name="affected_count" min="1" value="{{ old('affected_count', 1) }}" required />
                                <x-input-error :messages="$errors->get('affected_count')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="dead_count" :value="__('Dead Chickens')" />
                                <x-text-input id="dead_count" class="block mt-1 w-full" type="number" name="dead_count" min="0" value="{{ old('dead_count', 0) }}" required />
                                <x-input-error :messages="$errors->get('dead_count')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Recovery Status')" />
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="under_treatment" @selected(old('status', 'under_treatment') === 'under_treatment')>Under treatment</option>
                                    <option value="recovering" @selected(old('status') === 'recovering')>Recovering</option>
                                    <option value="recovered" @selected(old('status') === 'recovered')>Recovered</option>
                                    <option value="dead" @selected(old('status') === 'dead')>Dead</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Observation -->
                        <div class="mt-4">
                            <x-input-label for="observation" :value="__('Observation')" />
                            <textarea id="observation" name="observation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('observation') }}</textarea>
                            <x-input-error :messages="$errors->get('observation')" class="mt-2" />
                        </div>

                        <!-- Medication Name (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="medication_name" :value="__('Medication Name (Optional)')" />
                            <x-text-input id="medication_name" class="block mt-1 w-full" type="text" name="medication_name" :value="old('medication_name')" />
                            <x-input-error :messages="$errors->get('medication_name')" class="mt-2" />
                        </div>

                        <!-- Dosage Amount (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="dosage_amount" :value="__('Dosage Amount (Optional)')" />
                            <x-text-input id="dosage_amount" class="block mt-1 w-full" type="text" name="dosage_amount" :value="old('dosage_amount')" />
                            <x-input-error :messages="$errors->get('dosage_amount')" class="mt-2" />
                        </div>

                        <!-- Dosage Unit (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="dosage_unit" :value="__('Dosage Unit (Optional)')" />
                            <x-text-input id="dosage_unit" class="block mt-1 w-full" type="text" name="dosage_unit" :value="old('dosage_unit')" />
                            <x-input-error :messages="$errors->get('dosage_unit')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="remarks" :value="__('Remarks (Optional)')" />
                            <textarea id="remarks" name="remarks" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('remarks') }}</textarea>
                            <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="remedy" :value="__('Remedy or Treatment Plan (Optional)')" />
                            <textarea id="remedy" name="remedy" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('remedy') }}</textarea>
                            <x-input-error :messages="$errors->get('remedy')" class="mt-2" />
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
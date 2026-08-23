<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Health Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.health-records.update', $healthRecord) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a batch</option>
                                @foreach(\App\Models\Batch::where('current_quantity', '>', 0)->get() as $batch)
                                    <option value="{{ $batch->id }}" @if(old('batch_id', $healthRecord->batch_id) == $batch->id) selected @endif>{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Record Type -->
                        <div class="mt-4">
                            <x-input-label for="record_type" :value="__('Record Type')" />
                            <select id="record_type" name="record_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a type</option>
                                <option value="symptom" @if(old('record_type', $healthRecord->record_type) == 'symptom') selected @endif>Symptom</option>
                                <option value="diagnosis" @if(old('record_type', $healthRecord->record_type) == 'diagnosis') selected @endif>Diagnosis</option>
                                <option value="medication" @if(old('record_type', $healthRecord->record_type) == 'medication') selected @endif>Medication</option>
                                <option value="recommendation" @if(old('record_type', $healthRecord->record_type) == 'recommendation') selected @endif>Recommendation</option>
                            </select>
                            <x-input-error :messages="$errors->get('record_type')" class="mt-2" />
                        </div>

                        <!-- Recorded At -->
                        <div class="mt-4">
                            <x-input-label for="recorded_at" :value="__('Recorded Date & Time')" />
                            <x-text-input id="recorded_at" class="block mt-1 w-full" type="datetime-local" name="recorded_at" :value="old('recorded_at', $healthRecord->recorded_at?->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('recorded_at')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description / Observation')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description', $healthRecord->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Medication Given (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="medication_given" :value="__('Medication Given (Optional)')" />
                            <x-text-input id="medication_given" class="block mt-1 w-full" type="text" name="medication_given" :value="old('medication_given', $healthRecord->medication_given)" />
                            <x-input-error :messages="$errors->get('medication_given')" class="mt-2" />
                        </div>

                        <!-- Dosage (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="dosage" :value="__('Dosage (Optional)')" />
                            <x-text-input id="dosage" class="block mt-1 w-full" type="text" name="dosage" :value="old('dosage', $healthRecord->dosage)" />
                            <x-input-error :messages="$errors->get('dosage')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('worker.health-records.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Update Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
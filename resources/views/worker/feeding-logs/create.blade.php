<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Feeding') }}
            </h2>
            <a href="{{ route('worker.feeding-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Feeding Logs') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.feeding-logs.store') }}">
                        @csrf

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ (old('batch_id', $selectedBatchId ?? '') == $batch->id) ? 'selected' : '' }}>
                                        {{ $batch->batch_code }} ({{ \Illuminate\Support\Str::title(str_replace('_', ' ', $batch->feeding_method)) }}) (Current Qty: {{ $batch->current_quantity }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                            <div id="unlimited-feeding-message" class="hidden mt-2 text-sm text-blue-600">
                                This batch has an unlimited feeding method.
                            </div>
                        </div>

                        <!-- Feed Type -->
                        <div class="mt-4">
                            <x-input-label for="feed_type" :value="__('Feed Type')" />
                            <x-text-input id="feed_type" class="block mt-1 w-full" type="text" name="feed_type" :value="old('feed_type')" required />
                            <x-input-error :messages="$errors->get('feed_type')" class="mt-2" />
                        </div>



                        <!-- Quantity Fed (kg) -->
                        <div class="mt-4">
                            <x-input-label for="quantity_kg" :value="__('Quantity Fed (kg)')" />
                            <x-text-input id="quantity_kg" class="block mt-1 w-full" type="number" step="0.01" name="quantity_kg" :value="old('quantity_kg')" required />
                            <x-input-error :messages="$errors->get('quantity_kg')" class="mt-2" />
                        </div>

                        <!-- Fed At -->
                        <div class="mt-4">
                            <x-input-label for="fed_at" :value="__('Feeding Date & Time')" />
                            <x-text-input id="fed_at" class="block mt-1 w-full" type="datetime-local" name="fed_at" :value="old('fed_at', now()->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('fed_at')" class="mt-2" />
                        </div>

                        <!-- Feeding Time Slot (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="feeding_time_slot" :value="__('Time Slot (Optional)')" />
                            <x-text-input id="feeding_time_slot" class="block mt-1 w-full" type="text" name="feeding_time_slot" :value="old('feeding_time_slot')" />
                            <x-input-error :messages="$errors->get('feeding_time_slot')" class="mt-2" />
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mt-4">
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="notes">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Log') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const batchSelect = document.getElementById('batch_id');
            const unlimitedFeedingMessage = document.getElementById('unlimited-feeding-message');
            const batches = @json($batches->keyBy('id'));

            batchSelect.addEventListener('change', function () {
                const selectedBatchId = this.value;
                if (selectedBatchId && batches[selectedBatchId] && batches[selectedBatchId].feeding_method === 'unlimited') {
                    unlimitedFeedingMessage.classList.remove('hidden');
                } else {
                    unlimitedFeedingMessage.classList.add('hidden');
                }
            });

            // Trigger change event on page load if a batch is already selected
            if (batchSelect.value) {
                batchSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</x-app-layout>
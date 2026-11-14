<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quantity Correction</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
                @endif
                <p class="mb-4 text-sm text-gray-600">Batch: <strong>{{ $batch->batch_code }}</strong></p>
                <p class="mb-4 text-sm text-gray-600">Current quantity: <strong>{{ $batch->current_quantity }}</strong></p>
                <form method="POST" action="{{ route('manager.quantity-adjustments.store', $batch) }}">
                    @csrf
                    <input type="hidden" name="previous_quantity" value="{{ $batch->current_quantity }}">
                    <div>
                        <x-input-label for="corrected_quantity" value="Corrected Quantity" />
                        <x-text-input id="corrected_quantity" name="corrected_quantity" type="number" min="0" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('corrected_quantity')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="reason" value="Reason for Correction" />
                        <textarea id="reason" name="reason" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                    <x-primary-button class="mt-4">Record Correction</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Sale') }}
            </h2>
            <a href="{{ route('manager.sales.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Sales') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('manager.sales.store') }}">
                        @csrf

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Select a batch</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Sale Date -->
                        <div class="mt-4">
                            <x-input-label for="sale_date" :value="__('Sale Date')" />
                            <x-text-input id="sale_date" class="block mt-1 w-full" type="date" name="sale_date" :value="old('sale_date', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('sale_date')" class="mt-2" />
                        </div>

                        <!-- Heads Sold -->
                        <div class="mt-4">
                            <x-input-label for="heads_sold" :value="__('Number of Birds Sold')" />
                            <x-text-input id="heads_sold" class="block mt-1 w-full" type="number" name="heads_sold" :value="old('heads_sold')" min="1" required />
                            <x-input-error :messages="$errors->get('heads_sold')" class="mt-2" />
                        </div>

                        <!-- Total Weight -->
                        <div class="mt-4">
                            <x-input-label for="total_weight_kg" :value="__('Total Weight (kg)')" />
                            <x-text-input id="total_weight_kg" class="block mt-1 w-full" type="number" step="0.01" name="total_weight_kg" :value="old('total_weight_kg')" min="0" required />
                            <x-input-error :messages="$errors->get('total_weight_kg')" class="mt-2" />
                        </div>

                        <!-- Price per kg -->
                        <div class="mt-4">
                            <x-input-label for="price_per_kg" :value="__('Price per kg (PHP)')" />
                            <x-text-input id="price_per_kg" class="block mt-1 w-full" type="number" step="0.01" name="price_per_kg" :value="old('price_per_kg')" min="0" required />
                            <x-input-error :messages="$errors->get('price_per_kg')" class="mt-2" />
                        </div>

                        <!-- Total Amount -->
                        <div class="mt-4">
                            <x-input-label for="total_amount" :value="__('Total Amount (PHP)')" />
                            <x-text-input id="total_amount" class="block mt-1 w-full bg-gray-100" type="number" step="0.01" name="total_amount" :value="old('total_amount')" min="0" required readonly />
                            <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
                        </div>

                        <!-- Buyer Name -->
                        <div class="mt-4">
                            <x-input-label for="buyer_name" :value="__('Buyer Name (Optional)')" />
                            <x-text-input id="buyer_name" class="block mt-1 w-full" type="text" name="buyer_name" :value="old('buyer_name')" />
                            <x-input-error :messages="$errors->get('buyer_name')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Sale') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const totalWeightInput = document.getElementById('total_weight_kg');
            const pricePerKgInput = document.getElementById('price_per_kg');
            const totalAmountInput = document.getElementById('total_amount');

            function calculateTotalAmount() {
                const totalWeight = parseFloat(totalWeightInput.value) || 0;
                const pricePerKg = parseFloat(pricePerKgInput.value) || 0;
                const totalAmount = totalWeight * pricePerKg;
                totalAmountInput.value = totalAmount.toFixed(2);
            }

            totalWeightInput.addEventListener('input', calculateTotalAmount);
            pricePerKgInput.addEventListener('input', calculateTotalAmount);
        });
    </script>
</x-app-layout>
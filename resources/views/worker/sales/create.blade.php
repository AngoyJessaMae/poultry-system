<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.sales.store') }}">
                        @csrf

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(\App\Models\Batch::where('current_quantity', '>', 0)->get() as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Heads Sold -->
                        <div class="mt-4">
                            <x-input-label for="heads_sold" :value="__('Heads Sold')" />
                            <x-text-input id="heads_sold" class="block mt-1 w-full" type="number" name="heads_sold" :value="old('heads_sold')" required />
                            <x-input-error :messages="$errors->get('heads_sold')" class="mt-2" />
                        </div>

                        <!-- Total Amount -->
                        <div class="mt-4">
                            <x-input-label for="total_amount" :value="__('Total Amount')" />
                            <x-text-input id="total_amount" class="block mt-1 w-full" type="number" step="0.01" name="total_amount" :value="old('total_amount')" required />
                            <x-input-error :messages="$errors->get('total_amount')" class="mt-2" />
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
</x-app-layout>
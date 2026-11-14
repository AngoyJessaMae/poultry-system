<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit {{ $sale->transaction_number }}</h2></x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('manager.sales.update', $sale) }}">@csrf @method('PUT')
            <input type="hidden" name="batch_id" value="{{ $sale->batch_id }}"><p>Batch: <strong>{{ $sale->batch->batch_code }}</strong> (S3)</p>
            <label class="block mt-3">Sale date<input type="date" name="sale_date" required value="{{ old('sale_date', $sale->sale_date->toDateString()) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Quantity sold<input type="number" name="heads_sold" min="1" required value="{{ old('heads_sold', $sale->heads_sold) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Total weight (kg)<input type="number" name="total_weight_kg" min="0.01" step="0.01" required value="{{ old('total_weight_kg', $sale->total_weight_kg) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Unit selling price<input type="number" name="price_per_kg" min="0.01" step="0.01" required value="{{ old('price_per_kg', $sale->price_per_kg) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Buyer name<input type="text" name="buyer_name" value="{{ old('buyer_name', $sale->buyer_name) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <x-input-error :messages="$errors->all()" class="mt-3" /><button class="mt-5 rounded bg-gray-800 px-4 py-2 text-white">Save Changes</button>
        </form>
    </div></div></div>
</x-app-layout>

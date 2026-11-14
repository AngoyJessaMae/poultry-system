<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Record S3 Sale</h2></x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('manager.sales.store') }}">@csrf
            <label class="block mt-3">S3 Batch<select name="batch_id" required class="mt-1 block w-full rounded border-gray-300"><option value="">Select an eligible S3 batch</option>@foreach($batches as $batch)<option value="{{ $batch->id }}" @selected(old('batch_id') == $batch->id)>{{ $batch->batch_code }} — {{ $batch->current_quantity }} available</option>@endforeach</select></label>
            <label class="block mt-3">Sale date<input type="date" name="sale_date" required value="{{ old('sale_date', now()->toDateString()) }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Quantity sold<input type="number" name="heads_sold" min="1" required value="{{ old('heads_sold') }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Total weight (kg)<input type="number" name="total_weight_kg" min="0.01" step="0.01" required value="{{ old('total_weight_kg') }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <label class="block mt-3">Unit selling price<input id="price_per_kg" type="number" name="price_per_kg" min="0.01" step="0.01" required value="{{ old('price_per_kg') }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <p class="mt-3 text-sm text-gray-600">Total sales amount is calculated as quantity sold × unit selling price.</p>
            <label class="block mt-3">Buyer name<input type="text" name="buyer_name" value="{{ old('buyer_name') }}" class="mt-1 block w-full rounded border-gray-300"></label>
            <x-input-error :messages="$errors->all()" class="mt-3" />
            <button class="mt-5 rounded bg-gray-800 px-4 py-2 text-white">Save Sale</button>
        </form>
    </div></div></div>
</x-app-layout>

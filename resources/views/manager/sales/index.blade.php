<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between gap-4"><h2 class="font-semibold text-xl text-gray-800">Sales Management</h2><a class="inline-flex min-h-10 items-center justify-center rounded-lg bg-brand-orange-alt px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-orange-300" href="{{ route('manager.sales.create') }}">Record S3 Sale</a></div></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))<div class="mb-4 rounded bg-green-50 p-3 text-green-700">{{ session('success') }}</div>@endif
        <form method="GET" class="mb-6 grid grid-cols-1 gap-3 bg-white p-6 shadow-sm sm:grid-cols-4">
            <input type="date" name="date" value="{{ request('date') }}" class="rounded border-gray-300" placeholder="Date">
            <select name="batch_id" class="rounded border-gray-300"><option value="">All batches</option>@foreach($batches as $batch)<option value="{{ $batch->id }}" @selected(request('batch_id') == $batch->id)>{{ $batch->batch_code }}</option>@endforeach</select>
            <select name="status" class="rounded border-gray-300"><option value="">All statuses</option><option value="completed" @selected(request('status') === 'completed')>Completed</option><option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option></select>
            <button class="rounded bg-gray-800 px-4 py-2 text-white">Filter</button>
        </form>
        <div class="mb-4 rounded bg-white p-4 shadow-sm">Valid sales revenue: <strong>₱{{ number_format($revenue, 2) }}</strong></div>
        <div class="overflow-x-auto bg-white p-6 shadow-sm"><table class="min-w-full divide-y"><thead><tr><th class="px-3 py-2 text-left">Transaction</th><th class="px-3 py-2 text-left">Date</th><th class="px-3 py-2 text-left">Batch</th><th class="px-3 py-2 text-left">Station</th><th class="px-3 py-2 text-left">Quantity</th><th class="px-3 py-2 text-left">Total</th><th class="px-3 py-2 text-left">Status</th><th></th></tr></thead><tbody class="divide-y">
            @forelse($sales as $sale)<tr><td class="px-3 py-2">{{ $sale->transaction_number }}</td><td class="px-3 py-2">{{ $sale->sale_date->format('Y-m-d') }}</td><td class="px-3 py-2">{{ $sale->batch->batch_code }}</td><td class="px-3 py-2">{{ $sale->station?->station_code ?? '—' }}</td><td class="px-3 py-2">{{ $sale->heads_sold }}</td><td class="px-3 py-2">₱{{ number_format($sale->total_amount, 2) }}</td><td class="px-3 py-2">{{ ucfirst($sale->status) }}</td><td class="px-3 py-2"><a class="text-indigo-600" href="{{ route('manager.sales.show', $sale) }}">View</a> @if($sale->status === 'completed')<a class="ml-2 text-indigo-600" href="{{ route('manager.sales.edit', $sale) }}">Edit</a>@endif</td></tr>@empty<tr><td colspan="8" class="px-3 py-2">No sales found.</td></tr>@endforelse
        </tbody></table></div>
    </div></div>
</x-app-layout>

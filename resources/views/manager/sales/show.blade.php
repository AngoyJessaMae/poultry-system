<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Sales Transaction</h2></x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm">
        <dl class="space-y-2"><dt class="font-semibold">Transaction</dt><dd>{{ $sale->transaction_number }}</dd><dt class="font-semibold">Batch / Station</dt><dd>{{ $sale->batch->batch_code }} / {{ $sale->station?->station_code }}</dd><dt class="font-semibold">Quantity</dt><dd>{{ $sale->heads_sold }}</dd><dt class="font-semibold">Total</dt><dd>₱{{ number_format($sale->total_amount, 2) }}</dd><dt class="font-semibold">Status</dt><dd>{{ ucfirst($sale->status) }}</dd><dt class="font-semibold">Manager</dt><dd>{{ $sale->user->name }}</dd></dl>
        @if($sale->status === 'completed')<form method="POST" action="{{ route('manager.sales.cancel', $sale) }}" class="mt-6">@csrf<label class="block font-semibold">Cancellation reason<textarea name="cancellation_reason" required class="mt-1 block w-full rounded border-gray-300"></textarea></label><button class="mt-3 rounded bg-red-700 px-4 py-2 text-white">Cancel / Void Sale</button></form>@else<div class="mt-6 rounded bg-yellow-50 p-3">Cancelled {{ $sale->cancelled_at?->format('Y-m-d H:i') }} by {{ $sale->cancelledBy?->name }}: {{ $sale->cancellation_reason }}</div>@endif
    </div></div></div>
</x-app-layout>

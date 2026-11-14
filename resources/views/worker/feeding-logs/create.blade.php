<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Feeding') }} — {{ $batch->station->station_code ?? $batch->station->name }} / {{ $batch->batch_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('worker.batches.feeding-logs.store', $batch) }}" method="POST">
                        @csrf

                        @if($schedule)
                            <input type="hidden" name="scheduled_at" value="{{ $schedule }}">
                            <p class="mb-4 text-sm text-gray-600">Scheduled session: <strong>{{ \Carbon\Carbon::parse($schedule)->format('M d, Y g:i A') }}</strong></p>
                        @else
                            <p class="mb-4 text-sm text-gray-600">Actual completion time will be captured automatically when you submit this record.</p>
                        @endif

                        <!-- Feed Type -->
                        <div class="mb-4">
                            <label for="feed_type" class="block text-gray-700 text-sm font-bold mb-2">Feed Type</label>
                            <input type="text" name="feed_type" id="feed_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <x-input-error :messages="$errors->get('feed_type')" class="mt-2" />
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Actual quantity consumed</label>
                            <div class="flex gap-2"><input type="number" name="quantity" id="quantity" step="0.001" min="0.001" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <select name="unit" class="border-gray-300 rounded-md shadow-sm"><option value="kg">kg</option><option value="g">g</option><option value="lb">lb</option></select></div>
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <div class="mb-4"><label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (optional)</label><textarea name="notes" id="notes" class="shadow appearance-none border rounded w-full py-2 px-3"></textarea></div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Record Feeding
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

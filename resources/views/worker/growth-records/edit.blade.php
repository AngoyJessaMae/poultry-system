<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Growth Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.growth-records.update', $growthRecord) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Batch -->
                        <div class="mt-4">
                            <x-input-label for="batch_id" :value="__('Batch')" />
                            <select id="batch_id" name="batch_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select a batch</option>
                                @foreach(\App\Models\Batch::where('current_quantity', '>', 0)->get() as $batch)
                                    <option value="{{ $batch->id }}" @if($growthRecord->batch_id == $batch->id) selected @endif>{{ $batch->batch_code }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('batch_id')" class="mt-2" />
                        </div>

                        <!-- Average Weight -->
                        <div class="mt-4">
                            <x-input-label for="average_weight" :value="__('Average Weight (g)')" />
                            <x-text-input id="average_weight" class="block mt-1 w-full" type="number" step="0.01" name="average_weight" :value="old('average_weight', $growthRecord->average_weight)" required />
                            <x-input-error :messages="$errors->get('average_weight')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
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
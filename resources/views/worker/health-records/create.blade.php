<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Health Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('worker.health-records.store') }}">
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

                        <!-- Observation -->
                        <div class="mt-4">
                            <x-input-label for="observation" :value="__('Observation')" />
                            <textarea id="observation" name="observation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observation') }}</textarea>
                            <x-input-error :messages="$errors->get('observation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Save Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
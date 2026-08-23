<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Station') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('manager.stations.update', $station) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Station Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $station->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Min Age -->
                        <div class="mt-4">
                            <x-input-label for="min_age_days" :value="__('Minimum Age (Days)')" />
                            <x-text-input id="min_age_days" class="block mt-1 w-full" type="number" name="min_age_days" :value="old('min_age_days', $station->min_age_days)" required />
                            <x-input-error :messages="$errors->get('min_age_days')" class="mt-2" />
                        </div>

                        <!-- Max Age -->
                        <div class="mt-4">
                            <x-input-label for="max_age_days" :value="__('Maximum Age (Days)')" />
                            <x-text-input id="max_age_days" class="block mt-1 w-full" type="number" name="max_age_days" :value="old('max_age_days', $station->max_age_days)" required />
                            <x-input-error :messages="$errors->get('max_age_days')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $station->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Feeding Method -->
                        <div class="mt-4">
                            <x-input-label for="feeding_method" :value="__('Feeding Method')" />
                            <select id="feeding_method" name="feeding_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(\App\Enums\FeedingMethod::cases() as $method)
                                    <option value="{{ $method->value }}" @if($station->feeding_method == $method) selected @endif>{{ str_replace('_', ' ', Str::title($method->name)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('feeding_method')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update Station') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
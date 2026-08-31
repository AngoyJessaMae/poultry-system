<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('View Station') }}
            </h2>
            <a href="{{ route('manager.stations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('Back to Stations') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form>
                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Station Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$station->name" disabled />
                        </div>

                        <!-- Capacity -->
                        <div class="mt-4">
                            <x-input-label for="capacity" :value="__('Capacity')" />
                            <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="$station->capacity" disabled />
                        </div>

                        <!-- Min Age -->
                        <div class="mt-4">
                            <x-input-label for="min_age_days" :value="__('Minimum Age (Days)')" />
                            <x-text-input id="min_age_days" class="block mt-1 w-full" type="number" name="min_age_days" :value="$station->min_age_days" disabled />
                        </div>

                        <!-- Max Age -->
                        <div class="mt-4">
                            <x-input-label for="max_age_days" :value="__('Maximum Age (Days)')" />
                            <x-text-input id="max_age_days" class="block mt-1 w-full" type="number" name="max_age_days" :value="$station->max_age_days" disabled />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>{{ $station->description }}</textarea>
                        </div>

                        <!-- Feeding Method -->
                        <div class="mt-4">
                            <x-input-label for="feeding_method" :value="__('Feeding Method')" />
                            <select id="feeding_method" name="feeding_method" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>
                                @foreach(\App\Enums\FeedingMethod::cases() as $method)
                                    <option value="{{ $method->value }}" @if($station->feeding_method == $method) selected @endif>{{ str_replace('_', ' ', Str::title($method->name)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Station') }}
        </h2>
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


                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
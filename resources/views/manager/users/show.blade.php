<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $user->name }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="font-medium">{{ $user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Role</p>
                                <p class="font-medium capitalize">{{ $user->role }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Created At</p>
                                <p class="font-medium">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('manager.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-brand-orange border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-brand-orange-700 focus:bg-brand-orange-700 active:bg-brand-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                            {{ __('Edit Worker') }}
                        </a>
                        <a href="{{ route('manager.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-manager-layout>
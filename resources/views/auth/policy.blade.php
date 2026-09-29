<x-guest-layout>
    <div class="w-full sm:max-w-2xl">
        <div class="px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6">
                <p class="text-sm text-gray-500">{{ __('Policy revision') }} {{ $policyRevision }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ __('Account and Work Process Policy') }}</h1>
            </div>

            <div class="space-y-3 text-sm leading-6 text-gray-700">
                <p>{{ __('Managers may review the overall farm work process through authorized summaries and reports.') }}</p>
                <p>{{ __('Workers are responsible for accurately recording operational activities, including feeding, growth, health, mortality, and sales records.') }}</p>
                <p>{{ __('Users may access only the functions permitted for their role. User credentials are private: managers may manage accounts or initiate password resets, but must never view or request a user password.') }}</p>
            </div>

            <form method="POST" action="{{ route('account-policy.accept') }}" class="mt-6">
                @csrf
                <label for="policy_acknowledged" class="flex items-start">
                    <input id="policy_acknowledged" type="checkbox" class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm" name="policy_acknowledged" value="1" required>
                    <span class="ml-2 text-sm text-gray-600">{{ __('I have read and agree to this policy.') }}</span>
                </label>
                <x-input-error :messages="$errors->get('policy_acknowledged')" class="mt-2" />

                <div class="mt-6 flex items-center justify-between">
                    @if ($returnTo === 'register')
                        <a href="{{ route('register') }}" class="text-sm underline text-gray-600">{{ __('Back to registration') }}</a>
                    @elseif ($returnTo === 'login')
                        <a href="{{ route('login') }}" class="text-sm underline text-gray-600">{{ __('Back to login') }}</a>
                    @else
                        <a href="{{ route('logout') }}" class="text-sm underline text-gray-600">{{ __('Sign out') }}</a>
                    @endif
                    <button type="submit" class="px-4 py-2 bg-brand-orange-alt text-white rounded-md font-semibold text-xs uppercase tracking-widest">
                        {{ __('Accept policy') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
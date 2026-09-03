<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Sidebar Hamburger -->
                <div class="-me-2 flex items-center">
                    <button @click="$dispatch('toggle-sidebar')" aria-label="Toggle sidebar" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifications Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if($notifications->count() > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $notifications->count() }}</span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Feeding Notifications') }}
                            </div>
                            @forelse($notifications as $notification)
                                <div class="flex items-center justify-between px-4 py-2 text-sm text-gray-700">
                                    <a href="{{ route('worker.batches.feeding-logs.create', $notification->batch_id) }}" class="flex-grow">
                                        {{ $notification->batch_name }} - {{ $notification->schedule_time }} ({{ $notification->status }})
                                    </a>
                                    <form method="POST" action="{{ route('notifications.dismiss') }}">
                                        @csrf
                                        <input type="hidden" name="batch_id" value="{{ $notification->batch_id }}">
                                        <input type="hidden" name="schedule_time" value="{{ $notification->schedule_time }}">
                                        <button type="submit" class="ml-2 text-red-500 hover:text-red-700">&times;</button>
                                    </form>
                                </div>
                            @empty
                                <div class="px-4 py-2 text-sm text-gray-700">No new notifications</div>
                            @endforelse
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }} ({{ Auth::user()->role }})</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    {{ __('Profile') }}
                                </div>
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();" class="bg-red-500 text-white hover:bg-red-600">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->isManager())
                <x-responsive-nav-link :href="route('manager.dashboard')" :active="request()->routeIs('manager.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.stations.index')" :active="request()->routeIs('manager.stations.index')">
                    {{ __('Stations') }}
                </x-responsive-nav-link>
                 <x-responsive-nav-link :href="route('manager.batches.index')" :active="request()->routeIs('manager.batches.index')">
                    {{ __('Batches') }}
                </x-responsive-nav-link>
                 <x-responsive-nav-link :href="route('manager.users.index')" :active="request()->routeIs('manager.users.index')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                 <x-responsive-nav-link :href="route('manager.reports.index')" :active="request()->routeIs('manager.reports.index')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('worker.dashboard')" :active="request()->routeIs('worker.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.stations.index')" :active="request()->routeIs('worker.stations.index')">
                    {{ __('Stations') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.batches.index')" :active="request()->routeIs('worker.batches.index')">
                    {{ __('Batches') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.feeding-logs.index')" :active="request()->routeIs('worker.feeding-logs.index')">
                    {{ __('Feeding') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.growth-records.index')" :active="request()->routeIs('worker.growth-records.index')">
                    {{ __('Growth') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.health-records.index')" :active="request()->routeIs('worker.health-records.index')">
                    {{ __('Health') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.mortality-records.index')" :active="request()->routeIs('worker.mortality-records.index')">
                    {{ __('Mortality') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.sales.index')" :active="request()->routeIs('worker.sales.index')">
                    {{ __('Sales') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('manager.reports.index')" :active="request()->routeIs('manager.reports.index')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
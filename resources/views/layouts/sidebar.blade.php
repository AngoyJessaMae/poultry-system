<div x-show="sidebarOpen" class="w-64 bg-brand-orange-alt text-white min-h-screen p-4 shadow-lg" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
    <div class="h-40 flex items-center justify-center">
        <a href="{{ route('dashboard') }}" class="w-full">
            <img src="{{ asset('images/logo2.png') }}" alt="Babia Poultry Farm Logo" class="w-full h-auto">
        </a>
    </div>
    <nav>
        @if(auth()->user()->isManager())
            <h3 class="text-xs uppercase text-orange-200 font-bold mb-2">Management</h3>
            <a href="{{ route('manager.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Dashboard</a>
            <a href="{{ route('manager.stations.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Stations</a>
            <a href="{{ route('manager.batches.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Batches</a>
            <a href="{{ route('manager.feeding-schedules.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Feeding Schedules</a>
            <a href="{{ route('manager.users.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Users</a>
            <a href="{{ route('manager.reports.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Reports</a>
        @else
            <h3 class="text-xs uppercase text-orange-200 font-bold mb-2">Worker Menu</h3>
            <a href="{{ route('worker.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Dashboard</a>
            <a href="{{ route('worker.stations.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Stations</a>
            <a href="{{ route('worker.batches.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Batches</a>
            <a href="{{ route('worker.feeding-logs.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Feeding</a>
            <a href="{{ route('worker.growth-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Growth</a>
            <a href="{{ route('worker.health-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Health</a>
            <a href="{{ route('worker.mortality-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Mortality</a>
            <a href="{{ route('worker.sales.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Sales</a>
        @endif
    </nav>
</div>
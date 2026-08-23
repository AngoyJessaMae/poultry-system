<div class="w-64 bg-brand-orange text-white min-h-screen p-4 shadow-lg">
    <div class="mb-8">
        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-brand-white">Babia Poultry Farm</a>
    </div>
    <nav>
        @if(auth()->user()->isManager())
            <h3 class="text-xs uppercase text-orange-200 font-bold mb-2">Management</h3>
            <a href="{{ route('manager.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Dashboard</a>
            <a href="{{ route('manager.stations.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Stations</a>
            <a href="{{ route('manager.batches.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Batches</a>
            <a href="{{ route('manager.users.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Users</a>
            <a href="{{ route('manager.reports.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Reports</a>
        @else
            <h3 class="text-xs uppercase text-orange-200 font-bold mb-2">Worker Menu</h3>
            <a href="{{ route('worker.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Dashboard</a>
            <a href="{{ route('worker.feeding-logs.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Feeding</a>
            <a href="{{ route('worker.growth-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Growth</a>
            <a href="{{ route('worker.health-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Health</a>
            <a href="{{ route('worker.mortality-records.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Mortality</a>
            <a href="{{ route('worker.sales.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-brand-red text-white">Sales</a>
        @endif
    </nav>
</div>
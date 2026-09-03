<div x-data="{
        width: localStorage.getItem('sidebarWidth') ? parseInt(localStorage.getItem('sidebarWidth')) : 280,
        resizing: false,
        startResize(event) {
            this.resizing = true;
            let startX = event.clientX;
            let startWidth = this.width;
            const doResize = (event) => {
                if (this.resizing) {
                    let newWidth = startWidth + (event.clientX - startX);
                    if (newWidth > 200 && newWidth < 500) { // Min and max width
                        this.width = newWidth;
                    }
                }
            };
            const stopResize = () => {
                this.resizing = false;
                localStorage.setItem('sidebarWidth', this.width);
                window.removeEventListener('mousemove', doResize);
                window.removeEventListener('mouseup', stopResize);
            };
            window.addEventListener('mousemove', doResize);
            window.addEventListener('mouseup', stopResize);
        }
    }"
    :style="`width: ${width}px; min-width: ${width}px`"
    class="bg-brand-orange-alt text-white min-h-screen p-4 shadow-lg relative flex flex-col flex-shrink-0"
     x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">

    <!-- Sidebar content -->
    <div class="flex-1 overflow-y-auto">
        <div class="h-40 flex items-center justify-center">
            <a href="{{ route('dashboard') }}" class="w-full p-4">
                <img src="{{ asset('images/logo2.png') }}" alt="Babia Poultry Farm Logo" class="w-full h-auto">
            </a>
        </div>
        <nav class="px-2 space-y-2">
            @if(auth()->user()->isManager())
                <h3 class="mb-3 border-b border-white/20 px-4 pb-2 text-xs font-bold uppercase tracking-widest text-orange-100">Management</h3>
                <a href="{{ route('manager.dashboard') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('manager.dashboard') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Dashboard</a>
                <a href="{{ route('manager.stations.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('manager.stations.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Stations</a>
                <a href="{{ route('manager.batches.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('manager.batches.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Batches</a>
                <a href="{{ route('manager.users.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('manager.users.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Users</a>
                <a href="{{ route('manager.reports.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('manager.reports.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Reports</a>
            @else
                <h3 class="mb-3 border-b border-white/20 px-4 pb-2 text-xs font-bold uppercase tracking-widest text-orange-100">Worker Menu</h3>
                <a href="{{ route('worker.dashboard') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.dashboard') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Dashboard</a>
                <a href="{{ route('worker.stations.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.stations.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Stations</a>
                <a href="{{ route('worker.batches.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.batches.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Batches</a>
                <a href="{{ route('worker.feeding-logs.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.feeding-logs.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Feeding</a>
                <a href="{{ route('worker.growth-records.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.growth-records.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Growth</a>
                <a href="{{ route('worker.health-records.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.health-records.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Health</a>
                <a href="{{ route('worker.mortality-records.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.mortality-records.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Mortality</a>
                <a href="{{ route('worker.sales.index') }}" class="flex min-h-11 items-center rounded-lg border px-4 py-2.5 text-sm font-medium shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-white/70 {{ request()->routeIs('worker.sales.index') ? 'border-white bg-white text-orange-700 shadow-md' : 'border-white/10 bg-orange-700/20 text-white hover:border-white/25 hover:bg-orange-700/40' }}">Sales</a>
            @endif
        </nav>
    </div>

    <!-- Resize Handle -->
    <div class="absolute top-0 right-0 h-full w-3 cursor-col-resize border-r-2 border-transparent hover:border-orange-200" title="Drag to resize sidebar" @mousedown.prevent="startResize"></div>
</div>
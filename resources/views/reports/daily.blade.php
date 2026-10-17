<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily Production Report: {{ $batch->batch_code }}</h2>
            <a class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50" href="{{ route('reports.daily.index') }}">All flocks</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Farm and flock information</h3>
                            <p class="mt-1 text-sm text-gray-500">Save the form header before entering daily production values.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-sm">
                            <a class="inline-flex items-center px-3 py-2 bg-orange-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600" href="{{ route('reports.daily.export.pdf', $batch) }}">Print / PDF</a>
                            <a class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50" href="{{ route('reports.daily.export.excel', $batch) }}">Excel</a>
                        </div>
                    </div>
                    <form class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4" method="POST" action="{{ route('reports.daily.header.update', $batch) }}">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label class="min-h-[3rem] flex items-end" for="farm_name" value="Name of Farm" />
                            <div id="farm_name" class="mt-1 flex min-h-[42px] items-center rounded-md border border-gray-300 bg-gray-50 px-3 text-sm text-gray-700">
                                {{ config('farm.name') }}
                            </div>
                        </div>
                        <div>
                            <x-input-label class="min-h-[3rem] flex items-end" for="arrival_date" value="Date of Loading" />
                            <x-text-input id="arrival_date" name="arrival_date" type="date" class="block mt-1 w-full" required :value="old('arrival_date', $batch->arrival_date->toDateString())" />
                        </div>
                        <div>
                            <x-input-label class="min-h-[3rem] flex items-end" for="initial_quantity" value="No. of Heads Load" />
                            <x-text-input id="initial_quantity" name="initial_quantity" type="number" min="1" class="block mt-1 w-full" required :value="old('initial_quantity', $batch->initial_quantity)" />
                        </div>
                        <div>
                            <x-input-label class="min-h-[3rem] flex items-end" for="initial_weight_grams" value="Initial Weight (average grams/bird)" />
                            <x-text-input id="initial_weight_grams" name="initial_weight_grams" type="number" step="0.01" min="0" class="block mt-1 w-full" required :value="old('initial_weight_grams', $batch->initial_weight_grams)" />
                        </div>
                        <div>
                            <x-input-label class="min-h-[3rem] flex items-end" for="flockman_id" value="Flockman / Recorder" />
                            <select id="flockman_id" name="flockman_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Not assigned</option>
                                @foreach(\App\Models\User::where('is_active', true)->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}" @selected(old('flockman_id', $batch->flockman_id) == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-5 flex justify-end">
                            <x-primary-button>Save header</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Daily production monitoring form</h3>
                    <p class="mt-1 text-sm text-gray-500">Age starts at 1 on the loading date. Blank ALW means no weighing was recorded.</p>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-[1500px] divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 text-left" rowspan="2">Days / Age</th><th class="px-2 py-2 text-left" rowspan="2">Date</th>
                                    <th class="px-2 py-2 text-left" colspan="3">Mortality</th><th class="px-2 py-2 text-left" colspan="2">ALW (grams)</th>
                                    <th class="px-2 py-2 text-left" rowspan="2">Feed Type</th><th class="px-2 py-2 text-left" colspan="4">Feed Consumed (grams)</th><th class="px-2 py-2 text-left" colspan="2">FCR</th><th rowspan="2"></th>
                                </tr>
                                <tr>
                                    <th class="px-2 py-2 text-left">Heads</th><th class="px-2 py-2 text-left">CU % (ACT)</th><th class="px-2 py-2 text-left">CU % (STD)</th>
                                    <th class="px-2 py-2 text-left">Actual</th><th class="px-2 py-2 text-left">Standard</th><th class="px-2 py-2 text-left">Actual Daily</th><th class="px-2 py-2 text-left">Actual Total</th>
                                    <th class="px-2 py-2 text-left">Standard Daily</th><th class="px-2 py-2 text-left">Standard Total</th><th class="px-2 py-2 text-left">Actual</th><th class="px-2 py-2 text-left">Standard</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($rows as $row)
                                    <tr>
                                        <form method="POST" action="{{ route('reports.daily.row.update', $batch) }}">
                                            @csrf @method('PUT')
                                            <td class="px-2 py-2">{{ $row['age'] }}</td><td class="px-2 py-2 whitespace-nowrap">{{ $row['date']->format('Y-m-d') }}</td>
                                            <td class="px-2 py-2"><input class="w-24 border-gray-300 rounded-md" type="number" min="0" name="mortality_heads" value="{{ $row['record']?->mortality_heads ?? $row['mortality_heads'] }}"></td>
                                            <td class="px-2 py-2">{{ $row['actual_mortality_percent'] === null ? '' : number_format($row['actual_mortality_percent'], 4) }}</td><td class="px-2 py-2">{{ $row['standard_mortality_percent'] === null ? '' : number_format($row['standard_mortality_percent'], 4) }}</td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="actual_live_weight_grams" value="{{ $row['record']?->actual_live_weight_grams ?? $row['actual_live_weight_grams'] }}"></td>
                                            <td class="px-2 py-2">{{ $row['standard_live_weight_grams'] === null ? '' : number_format($row['standard_live_weight_grams'], 2) }}</td>
                                            <td class="px-2 py-2"><input class="w-32 border-gray-300 rounded-md" name="feed_type" value="{{ $row['record']?->feed_type ?? $row['feed_type'] }}"></td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="actual_feed_grams" value="{{ $row['record']?->actual_feed_grams ?? $row['actual_feed_grams'] }}"></td>
                                            <td class="px-2 py-2">{{ number_format($row['cumulative_actual_feed_grams'], 2) }}</td><td class="px-2 py-2">{{ $row['standard_feed_grams'] === null ? '' : number_format($row['standard_feed_grams'], 2) }}</td><td class="px-2 py-2">{{ number_format($row['cumulative_standard_feed_grams'], 2) }}</td>
                                            <td class="px-2 py-2">{{ $row['actual_fcr'] === null ? '' : number_format($row['actual_fcr'], 6) }}</td><td class="px-2 py-2">{{ $row['standard_fcr'] === null ? '' : number_format($row['standard_fcr'], 6) }}</td>
                                            <td class="px-2 py-2"><input type="hidden" name="recorded_date" value="{{ $row['date']->toDateString() }}"><button class="text-indigo-600 hover:text-indigo-900" type="submit">Save</button></td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(auth()->user()->isManager())
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900">Production standards by age</h3>
                        <p class="mt-1 text-sm text-gray-500">Standards apply from the entered age until a newer age standard is configured. Leave a value blank when the farm has no confirmed standard.</p>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50"><tr><th class="px-2 py-2 text-left">Age</th><th class="px-2 py-2 text-left">CU % STD</th><th class="px-2 py-2 text-left">ALW STD (g)</th><th class="px-2 py-2 text-left">Feed STD (g)</th><th class="px-2 py-2 text-left">FCR STD</th><th></th></tr></thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($standards as $standard)
                                        <tr>
                                            <form method="POST" action="{{ route('reports.daily.standard.update') }}">
                                                @csrf @method('PUT')
                                                <td class="px-2 py-2"><input class="w-20 border-gray-300 rounded-md" type="number" min="1" name="age_days" value="{{ $standard->age_days }}"></td>
                                                <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.0001" min="0" name="mortality_percent" value="{{ $standard->mortality_percent }}"></td>
                                                <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="live_weight_grams" value="{{ $standard->live_weight_grams }}"></td>
                                                <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="feed_grams" value="{{ $standard->feed_grams }}"></td>
                                                <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.000001" min="0" name="fcr" value="{{ $standard->fcr }}"></td>
                                                <td class="px-2 py-2"><button class="text-indigo-600 hover:text-indigo-900" type="submit">Save</button></td>
                                            </form>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <form method="POST" action="{{ route('reports.daily.standard.update') }}">
                                            @csrf @method('PUT')
                                            <td class="px-2 py-2"><input class="w-20 border-gray-300 rounded-md" type="number" min="1" name="age_days" placeholder="Age"></td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.0001" min="0" name="mortality_percent"></td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="live_weight_grams"></td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.01" min="0" name="feed_grams"></td>
                                            <td class="px-2 py-2"><input class="w-28 border-gray-300 rounded-md" type="number" step="0.000001" min="0" name="fcr"></td>
                                            <td class="px-2 py-2"><button class="text-indigo-600 hover:text-indigo-900" type="submit">Add</button></td>
                                        </form>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
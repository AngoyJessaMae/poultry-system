<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\StationFeedingConfig;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StationFeedingConfigController extends Controller
{
    public function edit(Station $station)
    {
        abort_unless(auth()->user()->isManager(), 403);
        $config = $station->feedingConfig;
        return view('manager.station-feeding-configs.edit', compact('station', 'config'));
    }

    public function update(Request $request, Station $station)
    {
        abort_unless(auth()->user()->isManager(), 403);

        $data = $request->validate([
            'method' => ['required', 'in:unlimited,four_times_daily,twice_daily'],
            'schedule_times' => ['nullable', 'array'],
            'schedule_times.*' => ['date_format:H:i'],
        ]);

        $times = array_values(array_filter($data['schedule_times'] ?? []));
        $requiredCount = $data['method'] === 'unlimited' ? 0 : ($data['method'] === 'four_times_daily' ? 4 : 2);
        if (count($times) !== $requiredCount || count(array_unique($times)) !== count($times)) {
            return back()->withErrors(['schedule_times' => "This feeding method requires exactly {$requiredCount} unique feeding times."])->withInput();
        }

        DB::transaction(function () use ($station, $data, $times) {
            StationFeedingConfig::updateOrCreate(
                ['station_id' => $station->id],
                ['method' => $data['method'], 'schedule_times' => $times]
            );

            // Keep existing batches aligned with the station rule. Feeding
            // logs are snapshots and are deliberately left unchanged.
            Batch::where('station_id', $station->id)
                ->update(['feeding_method' => $data['method']]);
        });

        return back()->with('success', 'Station feeding configuration updated.');
    }
}

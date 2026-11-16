<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Http\Requests\StoreFeedingLogRequest;
use App\Models\Batch;
use App\Models\FeedingLog;
use App\Models\Station;
use App\Services\StationFeedingService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Traits\ManagesFeedingSchedules;
use Carbon\Carbon;

class FeedingLogController extends Controller
{
    use ManagesFeedingSchedules;

    public function index()
    {
        $this->authorize('viewAny', FeedingLog::class);

        $user = auth()->user();

        // Get feeding schedules
        $batchQuery = Batch::query()->where('status', 'active');
        $batches = $batchQuery->get();
        $schedules = $this->getUpcomingFeedingsForBatches($batches);

        // Get feeding logs
        $logQuery = FeedingLog::with('batch', 'user');
        if ($user->isWorker()) {
            $logQuery->where('user_id', $user->id);
        }
        $feedingLogs = $logQuery->latest()->get();

        $view = $user->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return view($view, compact('schedules', 'feedingLogs'));
    }

    public function create($batch_id)
    {
        $this->authorize('create', FeedingLog::class);
        $batch = Batch::with('station')->where('status', 'active')->findOrFail($batch_id);
        $schedule = request()->query('scheduled_at');
        $config = app(StationFeedingService::class)->configFor($batch->station);
        $feedType = $batch->station->required_feed_type;
        return view('worker.feeding-logs.create', compact('batch', 'schedule', 'config', 'feedType'));
    }

    public function store(StoreFeedingLogRequest $request, Batch $batch)
    {
        $this->authorize('create', FeedingLog::class);
        abort_unless($batch->status === 'active', 404);

        $data = $request->validated();
        $now = now();
        $station = Station::findOrFail($batch->station_id);
        $config = app(StationFeedingService::class)->configFor($station);
        $scheduledAt = null;

        if ($config['method'] !== 'unlimited') {
            if (empty($data['scheduled_at'])) {
                return back()->withErrors(['scheduled_at' => 'Select a scheduled feeding session.'])->withInput();
            }
            $scheduledAt = Carbon::parse($data['scheduled_at']);
            $validTime = collect($config['times'])->contains(fn (string $time) => $scheduledAt->format('H:i') === $time);
            if (!$validTime) {
                return back()->withErrors(['scheduled_at' => 'The selected feeding session is not configured for this station.'])->withInput();
            }
            if (FeedingLog::where('batch_id', $batch->id)->where('scheduled_at', $scheduledAt)->exists()) {
                return back()->withErrors(['scheduled_at' => 'This feeding session has already been completed.'])->withInput();
            }
        }

        $conversion = ['kg' => 1, 'g' => 0.001, 'lb' => 0.45359237];
        $quantityKg = round((float) $data['quantity'] * $conversion[$data['unit']], 3);

        try {
            $feedingLog = DB::transaction(function () use ($batch, $station, $config, $data, $scheduledAt, $now, $quantityKg) {
                return FeedingLog::create([
                    'batch_id' => $batch->id,
                    'station_id' => $station->id,
                    'user_id' => auth()->id(),
                    'feed_type' => $data['feed_type'],
                    'quantity' => round($quantityKg * 1000, 3),
                    'quantity_kg' => $quantityKg,
                    'unit' => $data['unit'],
                    'method' => $config['method'] === 'unlimited' ? 'Free Choice' : 'Restricted',
                    'feeding_time' => $now,
                    'scheduled_at' => $scheduledAt,
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'feeding_logs_batch_id_scheduled_at_unique')) {
                return back()->withErrors(['scheduled_at' => 'This feeding session has already been completed.'])->withInput();
            }
            throw $exception;
        }
        
        // Log the activity
        ActivityLogger::logActivity(auth()->user(), 'create-feeding-log', "Created feeding log for batch {$batch->name}: {$data['quantity']}g of {$data['feed_type']}");

        return redirect()->route('worker.feeding-logs.station', ['station' => $station, 'date' => $now->toDateString()])->with('success', 'Feeding logged successfully.');
    }

    public function stationIndex(Station $station, Request $request)
    {
        $this->authorize('viewAny', FeedingLog::class);
        $date = Carbon::parse($request->query('date', today()->toDateString()))->startOfDay();
        $batches = Batch::where('status', 'active')->where('station_id', $station->id)->with('station')->get();
        $service = app(StationFeedingService::class);
        $config = $service->configFor($station);
        $schedules = $batches->flatMap(fn (Batch $batch) => $service->schedulesFor($batch, $date));
        $logs = FeedingLog::with(['batch', 'station', 'user'])
            ->where('station_id', $station->id)
            ->whereBetween('feeding_time', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->when(auth()->user()->isWorker(), fn ($query) => $query->where('user_id', auth()->id()))
            ->latest('feeding_time')->get();

        return view('worker.feeding-logs.station', compact('station', 'config', 'date', 'schedules', 'logs'));
    }

    public function show(FeedingLog $feedingLog)
    {
        $this->authorize('view', $feedingLog);
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.show' : 'worker.feeding-logs.show';
        return view($view, compact('feedingLog'));
    }

    public function edit(FeedingLog $feedingLog)
    {
        $this->authorize('update', $feedingLog);
        $view = auth()->user()->isManager() ? 'manager.feeding-logs.edit' : 'worker.feeding-logs.edit';
        return view($view, compact('feedingLog'));
    }

    public function update(StoreFeedingLogRequest $request, FeedingLog $feedingLog)
    {
        $this->authorize('update', $feedingLog);
        $feedingLog->update($request->validated());
        
        // Log the activity
        ActivityLogger::logActivity(auth()->user(), 'update-feeding-log', "Updated feeding log #{$feedingLog->id} for batch {$feedingLog->batch->name}");
        
        $route = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return redirect()->route($route);
    }

    public function destroy(FeedingLog $feedingLog)
    {
        $this->authorize('delete', $feedingLog);
        $batchName = $feedingLog->batch->name;
        $feedingLogId = $feedingLog->id;
        $feedingLog->delete();
        
        // Log the activity
        ActivityLogger::logActivity(auth()->user(), 'delete-feeding-log', "Deleted feeding log #{$feedingLogId} from batch {$batchName}");
        
        $route = auth()->user()->isManager() ? 'manager.feeding-logs.index' : 'worker.feeding-logs.index';
        return redirect()->route($route);
    }

    public function selectBatch()
    {
        $this->authorize('create', FeedingLog::class);
        $batches = Batch::where('status', 'active')
            ->get();
        return view('worker.feeding-logs.select-batch', compact('batches'));
    }

    public function createForBatch(Request $request)
    {
        $this->authorize('create', FeedingLog::class);
        $batch_id = $request->validate(['batch_id' => 'required|exists:batches,id'])['batch_id'];
        return redirect()->route('worker.batches.feeding-logs.create', $batch_id);
    }


}

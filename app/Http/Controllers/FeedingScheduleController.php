<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\FeedingSchedule;
use Illuminate\Http\Request;

class FeedingScheduleController extends Controller
{
    public function index()
    {
        $schedules = FeedingSchedule::with('batch.station')->get();
        return view('manager.feeding-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $batches = Batch::where('feeding_method', 'unlimited')->where('status', 'active')->get();
        return view('manager.feeding-schedules.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        FeedingSchedule::create($request->all());

        return redirect()->route('manager.feeding-schedules.index');
    }

    public function edit(FeedingSchedule $feedingSchedule)
    {
        $batches = Batch::where('feeding_method', 'unlimited')->where('status', 'active')->get();
        return view('manager.feeding-schedules.edit', compact('feedingSchedule', 'batches'));
    }

    public function update(Request $request, FeedingSchedule $feedingSchedule)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'scheduled_time' => 'required|date_format:H:i',
        ]);

        $feedingSchedule->update($request->all());

        return redirect()->route('manager.feeding-schedules.index');
    }

    public function destroy(FeedingSchedule $feedingSchedule)
    {
        $feedingSchedule->delete();

        return redirect()->route('manager.feeding-schedules.index');
    }
}
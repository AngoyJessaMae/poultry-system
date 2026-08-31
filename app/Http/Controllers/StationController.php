<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Station;
use Illuminate\Http\Request;
use App\Enums\FeedingMethod;
use App\Models\FeedingSchedule;

class StationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Station::class);
        $stations = Station::with('batches')->get();
        $view = auth()->user()->isManager() ? 'manager.stations.index' : 'worker.stations.index';
        return view($view, compact('stations'));
    }

    public function create()
    {
        $this->authorize('create', Station::class);
        $view = auth()->user()->isManager() ? 'manager.stations.create' : 'worker.stations.create';
        return view($view);
    }

    public function store(StoreStationRequest $request)
    {
        $this->authorize('create', Station::class);
        Station::create($request->validated());
        $route = auth()->user()->isManager() ? 'manager.stations.index' : 'worker.stations.index';
        return redirect()->route($route);
    }

    public function show(Station $station)
    {
        $this->authorize('view', $station);
        $view = auth()->user()->isManager() ? 'manager.stations.show' : 'worker.stations.show';
        return view($view, compact('station'));
    }

    public function edit(Station $station)
    {
        $this->authorize('update', $station);
        $view = auth()->user()->isManager() ? 'manager.stations.edit' : 'worker.stations.edit';
        return view($view, compact('station'));
    }

    public function update(UpdateStationRequest $request, Station $station)
    {
        $this->authorize('update', $station);
        $station->update($request->validated());

        $route = auth()->user()->isManager() ? 'manager.stations.index' : 'worker.stations.index';
        return redirect()->route($route);
    }

    public function destroy(Station $station)
    {
        $this->authorize('delete', $station);
        $station->delete();
        $route = auth()->user()->isManager() ? 'manager.stations.index' : 'worker.stations.index';
        return redirect()->route($route);
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Station;
use App\Models\ManagementRequest;
use App\Models\User;
use App\Notifications\ManagementRequestSubmitted;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

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
        $data = $request->validated();
        $data['station_code'] = strtoupper(trim($data['station_code']));

        $duplicate = Station::whereRaw('LOWER(station_code) = ?', [strtolower($data['station_code'])])
            ->orWhereRaw('LOWER(name) = ?', [strtolower(trim($data['name']))])
            ->exists();
        $pendingDuplicate = ManagementRequest::where('request_type', 'station')
            ->where('status', 'pending')
            ->get()
            ->contains(function (ManagementRequest $item) use ($data) {
                $payload = $item->payload;
                return strtolower($payload['station_code'] ?? '') === strtolower($data['station_code'])
                    || strtolower($payload['name'] ?? '') === strtolower(trim($data['name']));
            });

        if ($duplicate || $pendingDuplicate) {
            return back()->withErrors(['station_code' => 'A station with this identifier or name already exists or is already pending approval.'])->withInput();
        }

        $managementRequest = ManagementRequest::create([
            'request_type' => 'station',
            'request_key' => strtolower($data['station_code']),
            'requested_by' => auth()->id(),
            'status' => 'pending',
            'payload' => $data,
        ]);
        User::where('role', 'manager')->get()->each(fn (User $manager) => $manager->notify(new ManagementRequestSubmitted($managementRequest)));
        ActivityLogger::logActivity(auth()->user(), 'submit-management-request', "Submitted station request #{$managementRequest->id}");

        return redirect()->route('worker.management-requests.index')->with('success', 'Station request submitted for manager approval.');
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

        $route = auth()->user()->isManager() ? 'manager.stations.index' : 'worker.stations.index';

        if ($station->batches()->exists()) {
            return redirect()->route($route)->withErrors([
                'station' => 'This station cannot be deleted while it has assigned batches. Move or delete the batches first.',
            ]);
        }

        try {
            $station->delete();
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }

            return redirect()->route($route)->withErrors([
                'station' => 'This station cannot be deleted because it is still being used by another record.',
            ]);
        }

        return redirect()->route($route);
    }
}

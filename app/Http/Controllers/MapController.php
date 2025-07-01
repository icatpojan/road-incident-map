<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Disturbance;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $disturbances = Disturbance::with('user')->get();
        return view('map.index', compact('disturbances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:road_construction,traffic_jam,accident,flood,other',
        ]);

        $disturbance = Disturbance::create([
            'title' => $request->title,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'disturbance' => $disturbance->load('user'),
            'message' => 'Gangguan berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ongoing,resolved',
        ]);

        $disturbance = Disturbance::findOrFail($id);

        // Hanya user yang membuat gangguan atau admin yang bisa update
        if ($disturbance->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengubah gangguan ini!'
            ], 403);
        }

        $disturbance->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'disturbance' => $disturbance->load('user'),
            'message' => 'Status gangguan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $disturbance = Disturbance::findOrFail($id);

        // Hanya user yang membuat gangguan atau admin yang bisa hapus
        if ($disturbance->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus gangguan ini!'
            ], 403);
        }

        $disturbance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gangguan berhasil dihapus!'
        ]);
    }

    public function getDisturbances()
    {
        $disturbances = Disturbance::with('user')->get();
        return response()->json($disturbances);
    }
}

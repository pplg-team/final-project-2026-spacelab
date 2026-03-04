<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    public function index()
    {
        $now        = Carbon::now();
        $dayOfWeek  = $now->isoWeekday();
        $majors     = Major::all();

        $rooms = Room::with([
            'building',
            'roomHistories' => function ($query) use ($now) {
                $query->whereDate('start_date', '<=', $now)
                      ->whereDate('end_date', '>=', $now);
            },
            'roomHistories.timetableEntries' => function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek)
                      ->whereHas('template', fn($q) => $q->where('is_active', true));
            },
            'roomHistories.timetableEntries.period',
            'roomHistories.timetableEntries.teacherSubject.teacher',
            'roomHistories.timetableEntries.teacherSubject.subject',
            'roomHistories.timetableEntries.template.class.major',
        ])->get();

        foreach ($rooms as $room) {
            $room->activeEntry = null;
            foreach ($room->roomHistories as $history) {
                foreach ($history->timetableEntries as $entry) {
                    if ($entry->isOngoing($now)) {
                        $room->activeEntry = $entry;
                        break 2;
                    }
                }
            }
        }

        foreach ($majors as $major) {
            $major->cctv_rooms = $rooms->filter(function ($room) use ($major) {
                if ($room->activeEntry) {
                    $class = optional($room->activeEntry->template)->class;
                    return $class && $class->major_id === $major->id;
                }
                return false;
            })->values();
        }

        $emptyRooms = $rooms->filter(fn($room) => $room->activeEntry === null)->values();

        return view('admin.cctv.index', compact('majors', 'emptyRooms', 'rooms'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'camera_type'      => 'required|in:none,webcam,ip_camera',
            'stream_url'       => 'nullable|url',
            'is_camera_active' => 'boolean',
        ]);

        $room = Room::findOrFail($request->room_id);
        $room->camera_type      = $request->camera_type;
        $room->stream_url       = $request->camera_type === 'ip_camera' ? $request->stream_url : null;
        $room->is_camera_active = $request->boolean('is_camera_active', true);
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan kamera ' . $room->name . ' berhasil disimpan.',
            'room'    => [
                'id'               => $room->id,
                'name'             => $room->name,
                'camera_type'      => $room->camera_type,
                'stream_url'       => $room->stream_url,
                'is_camera_active' => $room->is_camera_active,
            ],
        ]);
    }

    public function getRooms()
    {
        $rooms = Room::with('building')
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id'               => $r->id,
                'name'             => $r->name,
                'code'             => $r->code,
                'building'         => optional($r->building)->name,
                'camera_type'      => $r->camera_type ?? 'none',
                'stream_url'       => $r->stream_url ?? '',
                'is_camera_active' => $r->is_camera_active ?? true,
            ]);

        return response()->json($rooms);
    }
}
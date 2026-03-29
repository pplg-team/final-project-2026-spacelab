<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\CctvCameraEvent;
use App\Models\CctvRecordingSegment;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CctvPlaybackController extends Controller
{
    public function index(Request $request)
    {
        $roomId = (string) $request->query('room_id', '');
        if ($roomId === '') {
            $roomId = null;
        }

        $date = $request->query('date', now()->format('Y-m-d'));
        $buildingId = (string) $request->query('building_id', 'all');
        $status = (string) $request->query('status', 'all');

        if (! in_array($status, ['all', 'online', 'degraded', 'offline', 'unknown'], true)) {
            $status = 'all';
        }

        $buildings = Building::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $knownBuildingIds = $buildings->pluck('id')->map(fn ($id) => (string) $id)->all();
        if ($buildingId !== 'all' && ! in_array($buildingId, $knownBuildingIds, true)) {
            $buildingId = 'all';
        }

        $rooms = Room::query()
            ->with(['building', 'latestHealthLog'])
            ->when($buildingId !== 'all', fn ($query) => $query->where('building_id', $buildingId))
            ->orderBy('name')
            ->get();

        if ($status !== 'all') {
            $rooms = $rooms->filter(function ($room) use ($status) {
                if ($status === 'unknown') {
                    return ! $room->latestHealthLog;
                }

                return $room->latestHealthLog && $room->latestHealthLog->status === $status;
            })->values();
        }

        $selectedRoom = null;
        if ($roomId) {
            $selectedRoom = Room::with(['building', 'latestHealthLog'])->find($roomId);
        }

        return view('admin.cctv.playback', compact(
            'rooms',
            'buildings',
            'selectedRoom',
            'roomId',
            'date',
            'buildingId',
            'status'
        ));
    }

    public function segments(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
        ]);

        $roomId = $request->query('room_id');
        $date = Carbon::parse($request->query('date'));
        
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get all segments for the day
        $allSegments = CctvRecordingSegment::query()
            ->where('room_id', $roomId)
            ->where('segment_start_at', '>=', $startOfDay)
            ->where('segment_start_at', '<=', $endOfDay)
            ->orderBy('segment_start_at')
            ->get();

        // Group segments by recording_session_id
        $groupedBySession = $allSegments->groupBy('recording_session_id');
        
        // Create merged segments (1 per session)
        $segments = $groupedBySession->map(function ($sessionSegments, $sessionId) {
            $firstSegment = $sessionSegments->first();
            $lastSegment = $sessionSegments->last();
            
            return [
                'id' => $sessionId ?: $firstSegment->id, // Use session ID or first segment ID
                'room_id' => $firstSegment->room_id,
                'camera_type' => $firstSegment->camera_type,
                'record_mode' => $firstSegment->record_mode,
                'segment_start_at' => $firstSegment->segment_start_at?->toIso8601String(),
                'segment_end_at' => $lastSegment->segment_end_at?->toIso8601String(),
                'duration_seconds' => $sessionSegments->sum('duration_seconds'),
                'file_size_bytes' => $sessionSegments->sum('file_size_bytes'),
                'codec' => $firstSegment->codec,
                'resolution' => $firstSegment->resolution,
                'has_motion' => $sessionSegments->contains('has_motion', true),
                'integrity_status' => $sessionSegments->every('integrity_status', 'ok') ? 'ok' : 'degraded',
                'playback_url' => route('admin.cctv.playback.stream', ['segment' => $sessionId ?: $firstSegment->id]),
                'segment_count' => $sessionSegments->count(),
            ];
        })->values();

        $events = CctvCameraEvent::query()
            ->where('room_id', $roomId)
            ->where('event_at', '>=', $startOfDay)
            ->where('event_at', '<=', $endOfDay)
            ->orderBy('event_at')
            ->get();

        return response()->json([
            'segments' => $segments,
            'events' => $events,
        ]);
    }

    public function stream($segmentOrSession): StreamedResponse
    {
        // Try to find by session ID first
        $segments = CctvRecordingSegment::query()
            ->where('recording_session_id', $segmentOrSession)
            ->orderBy('segment_start_at')
            ->get();
        
        // If not found by session, try by segment ID
        if ($segments->isEmpty()) {
            $segment = CctvRecordingSegment::findOrFail($segmentOrSession);
            $segments = collect([$segment]);
        }
        
        // If only 1 segment, stream it directly
        if ($segments->count() === 1) {
            $segment = $segments->first();
            
            if (!Storage::disk('local')->exists($segment->file_path)) {
                Log::error('Segment file not found', [
                    'segment_id' => $segment->id,
                    'file_path' => $segment->file_path
                ]);
                abort(404, 'Segment file not found.');
            }
            
            return Storage::disk('local')->response($segment->file_path, null, [
                'Content-Type' => 'video/webm',
                'Accept-Ranges' => 'bytes',
            ]);
        }
        
        // Multiple segments - merge them
        return response()->stream(function () use ($segments) {
            foreach ($segments as $segment) {
                if (!Storage::disk('local')->exists($segment->file_path)) {
                    Log::error('Segment file not found during merge', [
                        'segment_id' => $segment->id,
                        'file_path' => $segment->file_path
                    ]);
                    continue;
                }
                
                $stream = Storage::disk('local')->readStream($segment->file_path);
                if ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                }
            }
        }, 200, [
            'Content-Type' => 'video/webm',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache',
        ]);
    }
}

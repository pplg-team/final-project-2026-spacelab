<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;

class ScheduleController extends Controller
{
    public function index()
    {
        // Get all schedules total

        // Get all schedules hours total today

        // Get all classes total

        // Get all rooms total in use

        // Get active term
        $activeTerm = Term::where('is_active', true)->first();

        return view('admin.schedules.index', [
            'activeTerm' => $activeTerm,
            'title' => 'Jadwal',
            'description' => 'Halaman jadwal',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        DB::enableQueryLog();

        $events = Event::with('venue')
            ->withCount('participants')
            ->orderBy('id')
            ->limit(50)
            ->get();

        return view('events.index', ['events' => $events]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Note;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Lists for the dropdowns
        $venues = Venue::withCount('events')->orderBy('name')->get();
        $events = Event::orderBy('title')->get();
        $participants = Participant::orderBy('name')->get();
        $notes = Note::orderBy('id')->get();

        // The chosen items (default to the first one)
        $venue = Venue::with(['events', 'notes'])
            ->findOrFail($request->query('venue', $venues->first()->id));
        $event = Event::with(['participants', 'notes'])
            ->findOrFail($request->query('event', $events->first()->id));
        $participant = Participant::with('events')
            ->findOrFail($request->query('participant', $participants->first()->id));
        $note = Note::with('notable')
            ->findOrFail($request->query('note', $notes->first()->id));

        // Events with at least one waitlisted participant
        $waitlistedEvents = Event::with('venue')
            ->whereHas('participants', function ($query) {
                $query->where('event_participant.status', 'waitlisted');
            })
            ->get();

        // What the chosen note belongs to
        $noteOwnerKind = class_basename($note->notable_type);
        $noteOwnerName = $note->notable->title ?? $note->notable->name;

        return view('report', compact(
            'venues', 'events', 'participants', 'notes',
            'venue', 'event', 'participant', 'note',
            'waitlistedEvents', 'noteOwnerKind', 'noteOwnerName'
        ));
    }
}
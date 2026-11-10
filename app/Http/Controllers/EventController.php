<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
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

    public function filtered(Request $request)
    {
        // Three chained scopes (atVenue takes a parameter)
        $base = Event::query()
            ->upcoming()
            ->when($request->query('venue'), fn ($q, $venueId) => $q->atVenue((int) $venueId))
            ->soonestFirst();

        // JSONB filter, way 1: arrow syntax
        $arrowQuery = (clone $base)->where('details->dress_code', 'costume');

        // JSONB filter, way 2: whereJsonContains
        $containsQuery = (clone $base)->whereJsonContains('details->dress_code', 'costume');

        $arrowSql = $arrowQuery->toSql() . '  | bindings: ' . json_encode($arrowQuery->getBindings());
        $containsSql = $containsQuery->toSql() . '  | bindings: ' . json_encode($containsQuery->getBindings());

        $events = $arrowQuery->with('venue')->limit(50)->get();

        return view('events.filtered', [
            'events' => $events,
            'arrowSql' => $arrowSql,
            'containsSql' => $containsSql,
        ]);
        }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Event::query()
            ->whereFullText('description', $q)
            ->orderBy('id')
            ->limit(50);

        $sql = $query->toSql() . '  | bindings: ' . json_encode($query->getBindings());

        $events = $q === '' ? collect() : $query->get();

        return view('events.search', [
            'q' => $q,
            'events' => $events,
            'sql' => $sql,
        ]);
    }
}
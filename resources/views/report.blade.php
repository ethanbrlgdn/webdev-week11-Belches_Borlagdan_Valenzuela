<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Events Report</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 30px auto; padding: 0 15px; }
        h1 { margin-bottom: 5px; }
        h2 { background: #1e3a5f; color: #fff; padding: 8px 12px; margin-top: 30px; font-size: 18px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background: #eee; }
        form { background: #f4f4f4; padding: 12px; }
        label { margin-right: 10px; }
        select { margin-right: 15px; }
    </style>
</head>
<body>
    <h1>Campus Events Report</h1>

    <form method="GET" action="{{ route('report') }}">
        <label>Venue
            <select name="venue">
                @foreach ($venues as $v)
                    <option value="{{ $v->id }}" {{ $v->id == $venue->id ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Event
            <select name="event">
                @foreach ($events as $e)
                    <option value="{{ $e->id }}" {{ $e->id == $event->id ? 'selected' : '' }}>{{ $e->title }}</option>
                @endforeach
            </select>
        </label>
        <label>Participant
            <select name="participant">
                @foreach ($participants as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $participant->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </label>
        <label>Note
            <select name="note">
                @foreach ($notes as $n)
                    <option value="{{ $n->id }}" {{ $n->id == $note->id ? 'selected' : '' }}>#{{ $n->id }}: {{ \Illuminate\Support\Str::limit($n->body, 25) }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit">Show</button>
    </form>

    <h2>1. Every venue with the number of events it hosts</h2>
    <table>
        <tr><th>Venue</th><th>Number of events</th></tr>
        @foreach ($venues as $v)
            <tr><td>{{ $v->name }}</td><td>{{ $v->events_count }}</td></tr>
        @endforeach
    </table>

    <h2>2. All events at the chosen venue: {{ $venue->name }}</h2>
    <table>
        <tr><th>Event</th><th>Starts at</th></tr>
        @foreach ($venue->events as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->starts_at->format('M d, Y g:i A') }}</td></tr>
        @endforeach
    </table>

    <h2>3. All participants of the chosen event: {{ $event->title }}</h2>
    <table>
        <tr><th>Participant</th><th>Registration status</th></tr>
        @foreach ($event->participants as $p)
            <tr><td>{{ $p->name }}</td><td>{{ $p->pivot->status }}</td></tr>
        @endforeach
    </table>

    <h2>4. All events joined by the chosen participant: {{ $participant->name }}</h2>
    <table>
        <tr><th>Event</th><th>Registration status</th></tr>
        @foreach ($participant->events as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->pivot->status }}</td></tr>
        @endforeach
    </table>

    <h2>5. All events with at least one waitlisted participant</h2>
    <table>
        <tr><th>Event</th><th>Venue</th></tr>
        @foreach ($waitlistedEvents as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->venue->name }}</td></tr>
        @endforeach
    </table>

    <h2>6a. Notes of the chosen event: {{ $event->title }}</h2>
    <table>
        <tr><th>Note</th></tr>
        @forelse ($event->notes as $n)
            <tr><td>{{ $n->body }}</td></tr>
        @empty
            <tr><td>No notes for this event.</td></tr>
        @endforelse
    </table>

    <h2>6b. Notes of the chosen venue: {{ $venue->name }}</h2>
    <table>
        <tr><th>Note</th></tr>
        @forelse ($venue->notes as $n)
            <tr><td>{{ $n->body }}</td></tr>
        @empty
            <tr><td>No notes for this venue.</td></tr>
        @endforelse
    </table>

    <h2>7. The chosen note belongs to</h2>
    <table>
        <tr><th>Note</th><th>Belongs to</th><th>Kind of thing</th></tr>
        <tr>
            <td>{{ $note->body }}</td>
            <td>{{ $noteOwnerName }}</td>
            <td>{{ $noteOwnerKind }}</td>
        </tr>
    </table>
</body>
</html>
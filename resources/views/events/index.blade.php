<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Events</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
    </style>
</head>
<body>
    <h1>Events (first 50)</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Venue</th>
                <th>Participants</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->venue->name }}</td>
                    <td>{{ $event->participants_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Queries sent: {{ count(\Illuminate\Support\Facades\DB::getQueryLog()) }}</strong></p>
</body>
</html>
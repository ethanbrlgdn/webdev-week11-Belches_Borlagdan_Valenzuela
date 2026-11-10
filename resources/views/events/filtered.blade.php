<!DOCTYPE html>
<html>
<head>
    <title>Filtered Events</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 6px 10px; }
        pre { background: #f4f4f4; padding: 8px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Costume events (JSONB filter)</h1>

    <h3>Way 1: where('details->dress_code', 'costume')</h3>
    <pre>{{ $arrowSql }}</pre>

    <h3>Way 2: whereJsonContains('details->dress_code', 'costume')</h3>
    <pre>{{ $containsSql }}</pre>

    <p><strong>Rows found: {{ $events->count() }}</strong></p>

    <table>
        <tr><th>Title</th><th>Venue</th><th>Starts at</th><th>Dress code</th></tr>
        @foreach ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->venue->name }}</td>
                <td>{{ $event->starts_at }}</td>
                <td>{{ $event->details['dress_code'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>

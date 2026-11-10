<!DOCTYPE html>
<html>
<head>
    <title>Search Events</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 6px 10px; }
        pre { background: #f4f4f4; padding: 8px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Search events</h1>

    <form method="GET" action="/events/search">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search descriptions">
        <button type="submit">Search</button>
    </form>

    <h3>whereFullText('description', '{{ $q }}')</h3>
    <pre>{{ $sql }}</pre>

    <p><strong>Results: {{ $events->count() }} (max 50)</strong></p>

    <table>
        <tr><th>ID</th><th>Title</th><th>Description</th></tr>
        @foreach ($events as $event)
            <tr>
                <td>{{ $event->id }}</td>
                <td>{{ $event->title }}</td>
                <td>{{ $event->description }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
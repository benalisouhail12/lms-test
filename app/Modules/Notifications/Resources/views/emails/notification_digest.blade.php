<!DOCTYPE html>
<html>
<head>
    <title>Notification Digest</title>
</head>
<body>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Here is your {{ $frequency }} notification digest:</p>
    <ul>
        @foreach ($notifications as $notification)
            <li>{{ $notification }}</li>
        @endforeach
    </ul>
</body>
</html>

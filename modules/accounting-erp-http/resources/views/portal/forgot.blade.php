<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal access</title>
</head>
<body style="font-family: system-ui, sans-serif; max-width: 24rem; margin: 4rem auto; padding: 0 1rem;">
    <h1>Portal access</h1>

    @if (session('status'))
        <p style="color: #15803d;">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color: #b91c1c;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <p>Enter your email and we'll send a link to set your password.</p>
    <form method="POST" action="{{ $action }}">
        @csrf
        <p>
            <label>Email<br>
                <input type="email" name="email" required autocomplete="email" style="width:100%">
            </label>
        </p>
        <button type="submit">Email me a link</button>
    </form>
</body>
</html>

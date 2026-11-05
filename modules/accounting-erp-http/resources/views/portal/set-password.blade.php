<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set your password</title>
</head>
<body style="font-family: system-ui, sans-serif; max-width: 24rem; margin: 4rem auto; padding: 0 1rem;">
    <h1>Set your password</h1>

    @if ($errors->any())
        <ul style="color: #b91c1c;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ $action }}">
        @csrf
        <p>
            <label>New password<br>
                <input type="password" name="password" required autocomplete="new-password" style="width:100%">
            </label>
        </p>
        <p>
            <label>Confirm password<br>
                <input type="password" name="password_confirmation" required autocomplete="new-password" style="width:100%">
            </label>
        </p>
        <button type="submit">Set password</button>
    </form>
</body>
</html>

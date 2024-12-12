<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    @if (session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif
    <form method="POST" action="{{ url('/login') }}">
        @csrf
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
    <p>Chưa có tài khoản? <a href="{{ route('register.form') }}">Đăng ký ngay</a></p>
</body>
</html>
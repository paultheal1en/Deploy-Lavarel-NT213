<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notes</title>
</head>
<body>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h3>Notes</h3>
    <table>
        <thead>
            <tr>
                <th>Postname</th>
                <th>Author</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($result) && count($result) > 0)
                @foreach ($result as $note)
                    <tr>
                        <td>{{ $note->postName }}</td>
                        <td>{{ $note->Author }}</td>
                        <td>
                            @if ($note->isSecret == 1 && $note->Author == Session::get('user_name'))  <!-- Giả định bạn đã lưu tên người dùng trong session -->
                                <a href="/brac/{{$note->PostID}}">Click to see note</a>
                            @elseif ($note->isSecret == 0)
                                <a href="/brac/{{$note->PostID}}">Click to see note</a>
                            @else
                                Not Available
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">No notes available.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <h3>Add your note! :D </h3>
    <form action="/brac" method="post">
        @csrf
        <p>Postname</p>
        <input type="text" name="postName" required>
        <p>Author name</p>
        <input type="text" name="author" value="{{ Session::get('user_name') }}" readonly>
        <p>Is Secret</p>
        <select name="isSecret">
            <option value="1">True</option>
            <option value="0">False</option>
        </select>
        <p>Content Post </p>
        <textarea name="content" required></textarea>
        <br>
        <input type="submit" value="Add Note">
    </form>
</body>
</html>
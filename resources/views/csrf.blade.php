<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSRF</title>
</head>
<body>
    <form id="csrfForm" action="/csrf" method="post">
        @csrf
        Email: <input type="text" name="email" id="email" required>
        <button type="submit">Submit</button>
    </form>
    <script>
        setInterval(function(){
            location.reload(); 
        }, 60000);
    </script>

    @foreach($result as $row)
        Username: {{$row->username}}<br>
        Email: {{$row->email}}<br>
    @endforeach
    <h3>Lưu ý:</h3>
    <p>Để kiểm tra CSRF đúng cách, hãy sử dụng công cụ khác hoặc thử với các bước sau:</p>
    <ol>
        <li>Tạo một CSRF PoC từ một trang bên ngoài và gửi yêu cầu đến trang này.</li>
        <li>Xóa cookie trước khi thử lại nếu bạn muốn attack với người dùng khác.</li>
        <li>Nếu bạn khai thác trên token của chính bản thân mình thì nó chưa thành công.</li>
    </ol>
</body>
</html>
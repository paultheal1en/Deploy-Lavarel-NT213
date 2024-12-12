<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>File Upload</title>
    <script>
        function validateFile(event) {
            const fileInput = document.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            // Check if file is selected
            if (!file) {
                alert("Vui lòng chọn một file.");
                event.preventDefault();
                return;
            }

            // Allowed file types (images only in this example)
            const allowedTypes = ['image/png', 'image/jpeg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Chỉ cho phép file PNG và JPEG.');
                event.preventDefault();
                return;
            }

            // File size limit (2MB in this case)
            const maxSizeInBytes = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSizeInBytes) {
                alert('File quá lớn. Vui lòng chọn file nhỏ hơn 2MB.');
                event.preventDefault();
                return;
            }
        }
    </script>
</head>
<body>
    <form action="/upload" method="post" enctype="multipart/form-data" onsubmit="validateFile(event)">
        @csrf
        <div class="form-group">
            <label for="file">Chọn File ảnh</label>
            <input type="file" name="file" id="file" accept=".png, .jpeg, .jpg" required>
        </div>
        <button type="submit">Tải lên</button>
    </form>
</body>
</html>

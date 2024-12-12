<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;
use finfo;

class FileUploadController extends Controller {

    // Trang hiển thị danh sách file đã upload
    public function index(Request $request) {
        $files = Storage::files('uploads');
        return view('upload')->with('files', $files);
    }

    // Hàm kiểm tra Content-Type của file
    private function isValidContentType($filePath) {
        $allowedContentTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);
        return in_array($mimeType, $allowedContentTypes);
    }

    // Hàm kiểm tra magic bytes của file
    private function isValidFile($filePath) {
        $magicBytes = bin2hex(file_get_contents($filePath, false, null, 0, 20)); // Lấy 20 byte đầu tiên
        $validMagicBytes = [
            'jpeg' => 'ffd8ff',
            'png' => '89504e470d0a1a0a',
            'gif' => '47494638'
        ];
        foreach ($validMagicBytes as $type => $bytes) {
            if (strpos($magicBytes, $bytes) === 0) {
                return true;
            }
        }
        return false;
    }

    // Hàm xử lý upload file
    public function upload(Request $request) {  
        $errorMessage = "Invalid file upload!"; // Thông báo lỗi chung
    
        // Kiểm tra header Content-Type
        $contentType = $request->header('Content-Type');
        if (strpos($contentType, 'multipart/form-data') === false) {
            return response("Content-Type must be multipart/form-data.", 400);
        }
    
        // Các extension hợp lệ
        $allowedExtensions = ['jpg', 'png', 'gif'];
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
    
            // Kiểm tra extension của file
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, $allowedExtensions)) {
                return response("Invalid file extension.", 400);
            }
    
            // Kiểm tra MIME type bằng finfo để xác minh loại file
            if (!$this->isValidContentType($file->getPathname())) {
                return response("Invalid MIME type.", 400);
            }
    
            // Kiểm tra magic bytes để xác nhận đây là file ảnh thật sự
            if (!$this->isValidFile($file->getPathname())) {
                return response("Invalid file content.", 400);
            }
    
            // Kiểm tra tên file để loại bỏ các trường hợp file PHP giả mạo
            $filename = $file->getClientOriginalName();
            if (preg_match('/\.php[3-7]?$/i', $filename) || preg_match('/\.(php|phtml|phar)$/i', $filename)) {
                return response("Invalid file name.", 400); // Từ chối nếu tên file có đuôi PHP
            }
    
            // Kiểm tra kích thước file (giới hạn 2MB)
            if ($file->getSize() > 2000000) { // 2MB
                return response("File size exceeds the 2MB limit.", 400);
            }
    
            // Loại bỏ các ký tự không hợp lệ khỏi tên file
            $filename = basename($this->sanitizeFilename($file->getClientOriginalName()));
    
            // Tạo tên file ngẫu nhiên để tránh Path Traversal
            $fileNameToStore = bin2hex(random_bytes(16)) . '.' . $extension;
    
            // Lưu file với chất lượng 100% trong thư mục công khai
            $image = Image::make($file->getPathname())->save(public_path('/store/' . $fileNameToStore), 100);
    
            // Phản hồi kết quả cho người dùng
            return response("<a href=\"/store/" . $fileNameToStore . "\">Enter the file</a>");
        }
        return response($errorMessage, 400);
    }

    // Hàm loại bỏ các ký tự không hợp lệ trong tên file
    private function sanitizeFilename($filename) {
        // Loại bỏ các ký tự không an toàn (chỉ cho phép chữ cái, số và dấu chấm)
        return preg_replace('/[^a-zA-Z0-9\._-]/', '', $filename);
    }
}

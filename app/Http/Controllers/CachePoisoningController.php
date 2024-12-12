<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class CachePoisoningController extends Controller
{
    public function index(Request $request)
    {
        // Sử dụng một khóa cache cố định thay vì dựa trên chuỗi truy vấn do người dùng kiểm soát
        $cacheKey = 'cachepoisoning_page';
        
        // Sử dụng một giá trị header cố định hoặc lấy từ nguồn đáng tin cậy
        // Thay vì lấy từ 'X-Forwarded-Host' có thể bị thao túng bởi người dùng
        $header = 'Safe Header Value'; // Hoặc có thể lấy từ config nếu cần
        
        // Kiểm tra cache với khóa cố định
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        } else {
            // Tạo nội dung phản hồi an toàn không phụ thuộc vào dữ liệu không đáng tin cậy
            $response = View::make('cachepoisoning')->with('header', $header)->render();
            
            // Thiết lập thời gian hết hạn cho cache
            $expirationTime = now()->addSeconds(10);
            Cache::put($cacheKey, $response, $expirationTime);
            
            return $response;
        }
    }
}

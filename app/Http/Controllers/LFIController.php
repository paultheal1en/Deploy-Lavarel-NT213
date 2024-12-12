<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LFIController extends Controller
{
    public function index(Request $request)
    {
        return view('lfi');
    }
    public function lfi(Request $request)
    {
        $request->validate([
            'file' => 'required|string|regex:/^[\w,\s-]+\.[A-Za-z0-9]{2,4}$/'
        ]);
        $filename = basename($request->input('file'));
        $allowedExtensions = ['txt', 'md']; 
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($extension, $allowedExtensions)) {
            return response('Phần mở rộng file không hợp lệ', 400);
        }
        $whitelistDir = 'includes';  
        $filePath = storage_path("app/$whitelistDir/$filename");
        if (Storage::exists("$whitelistDir/$filename") && realpath($filePath) !== false && strpos(realpath($filePath), realpath(storage_path("app/$whitelistDir"))) === 0) {
            if (Storage::size("$whitelistDir/$filename") > 1024 * 1024) {
                return response('File quá lớn', 400);
            }
            $mimeType = mime_content_type($filePath);
            if (in_array($mimeType, ['text/plain', 'text/markdown'])) {
                return response()->file($filePath);
            } else {
                return response('File không hợp lệ hoặc không tồn tại', 404);
            }
        } else {
            return response('File không tồn tại hoặc không hợp lệ', 404);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BrokenAccessControlController extends Controller
{
    public function showNote(Request $request)
    {
        $userId = Session::get('user_id'); // Lấy user_id từ session
        $userName = Session::get('user_name'); // Lấy tên người dùng từ session

        if (!$userId) {
            return redirect('/login'); // Nếu không có user_id, chuyển hướng đến trang đăng nhập
        }

        // Truy vấn để lấy ghi chú công khai và ghi chú của người dùng
        $result = DB::select("
            SELECT * 
            FROM note 
            WHERE isSecret = 0 OR Author = (SELECT name FROM users WHERE ID = ?)", 
            [$userId]
        );

        return view('brac')->with('result', $result);
    }

    public function insertNote(Request $request)
    {
            // Xác thực các trường bắt buộc
        $request->validate([
            'postName' => 'required|string|max:255',
            'isSecret' => 'required|boolean',
            'content' => 'required|string',
        ], [
            'postName.required' => 'Tên ghi chú không được để trống.',
            'isSecret.required' => 'Vui lòng chọn trạng thái công khai của ghi chú.',
            'content.required' => 'Nội dung ghi chú không được để trống.',
        ]);

        // Lấy thông tin tác giả từ session
        $author = Session::get('user_name');

        // Thêm ghi chú vào cơ sở dữ liệu
        DB::insert('INSERT INTO note (postName, Author, isSecret, Content) VALUES (?, ?, ?, ?)', [
            $request->postName,
            $author,
            $request->isSecret,
            $request->content,
        ]);
        return redirect('/brac'); // Chuyển hướng đến trang ghi chú sau khi thêm thành công
    }

    public function showSpecificNote($PostID)
    {
        $userId = Session::get('user_id'); // Lấy user_id từ session
        $userName = Session::get('user_name'); // Lấy tên người dùng từ session

        if (!$userId) {
            return redirect('/login'); // Nếu không có user_id, chuyển hướng đến trang đăng nhập
        }

        // Lấy thông tin của ghi chú
        $result = DB::select("SELECT * FROM note WHERE PostID = ?", [$PostID]);
        
        if (empty($result)) {
            return redirect('/brac')->with('error', 'Ghi chú không tồn tại.'); // Nếu không có ghi chú, chuyển hướng và hiển thị thông báo lỗi
        }

        // Kiểm tra xem ghi chú có phải của người dùng không
        if ($result && $result[0]->isSecret == 1 && $result[0]->Author !== $userName) {
            return redirect('/brac')->with('error', 'You do not have permission to view this note.'); // Nếu không phải của người dùng, chuyển hướng
        }

        return view('brac_specific_note')->with('result', $result);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SQLiController extends Controller
{
    public function getUser(Request $request, $id)
    {
        // Xac thuc du lieu dau vao id phai la so nguyen va ton tai trong bang users hay khong
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Su dung Eloquent de tim gia tri id cua user
        $user = User::find($id);

        // Kiem tra xem co ton tai user khong
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Tra ve du lieu cua user duoi dang JSON
        return response()->json($user);
    }
}
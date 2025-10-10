<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Hiển thị thông tin tài khoản
    public function show()
    {
        $user = Auth::user();
        return view('account.show', compact('user'));
    }

    // Hiển thị form chỉnh sửa
    public function edit()
    {
        $user = Auth::user();
        return view('account.edit', compact('user'));
    }

    // Cập nhật thông tin
    public function update(UpdateAccountRequest $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $data = $request->only(['name', 'phone', 'address']);
        try {
            $user->update($data);
        } catch (\Exception $e) {
            Log::error('Failed to update user profile: ' . $e->getMessage());
            return redirect()->route('account.edit')->with('error', 'Không thể cập nhật thông tin. Vui lòng thử lại.');
        }
    return redirect()->route('account.show')->with('success', 'Đã thay đổi thông tin thành công.');
    }

    // Cập nhật mật khẩu
    public function updatePassword(UpdatePasswordRequest $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->route('account.show')->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return redirect()->route('account.show')->with('success', 'Đổi mật khẩu thành công.');
    }
}

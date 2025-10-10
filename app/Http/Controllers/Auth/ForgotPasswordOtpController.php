<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class ForgotPasswordOtpController extends Controller
{
    // Hiển thị form nhập email
    public function showEmailForm()
    {
        return view('auth.forgot-password-otp-email');
    }

    // Xử lý gửi OTP về email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại!']);
        }
        $otp = rand(100000, 999999);
        Session::put('otp', $otp);
        Session::put('otp_email', $request->email);
        // Gửi email OTP
        Mail::raw('Mã xác thực của bạn là: ' . $otp, function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Mã xác thực quên mật khẩu');
        });
        return redirect()->route('password.otp.form')->with('status', 'Mã OTP đã được gửi tới email!');
    }

    // Hiển thị form nhập OTP
    public function showOtpForm()
    {
        return view('auth.forgot-password-otp-verify');
    }

    // Xác thực OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);
        $otp = Session::get('otp');
        if ($request->otp != $otp) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng!']);
        }
        Session::put('otp_verified', true);
        return redirect()->route('password.otp.reset');
    }

    // Hiển thị form nhập mật khẩu mới
    public function showResetForm()
    {
        if (!Session::get('otp_verified')) {
            return redirect()->route('password.otp.email');
        }
        return view('auth.forgot-password-otp-reset');
    }

    // Đổi mật khẩu mới
    public function resetPassword(Request $request)
    {
        if (!Session::get('otp_verified')) {
            return redirect()->route('password.otp.email');
        }
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);
        $email = Session::get('otp_email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.otp.email')->withErrors(['email' => 'Email không tồn tại!']);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        // Xóa session OTP
        Session::forget(['otp', 'otp_email', 'otp_verified']);
        return redirect()->route('login')->with('status', 'Đổi mật khẩu thành công!');
    }
}

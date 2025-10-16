<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $orders = $user->orders()->with(['items.product', 'items.color'])->orderByDesc('created_at')->get();
        return view('profile.edit', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * Show the authenticated user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        // reuse orders query in edit for consistency
        $orders = $user->orders()->with(['items.product', 'items.color'])->orderByDesc('created_at')->get();
        return view('profile.show', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

    return Redirect::route('profile.view')->with('success', 'Đã cập nhật thông tin.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update password from profile page.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required','confirmed', new \App\Rules\StrongPassword()],
        ]);

        $user = $request->user();

        if (!
            \Illuminate\Support\Facades\Hash::check($request->input('current_password'), $user->password)
        ) {
            return Redirect::route('profile.view')->with('error', 'Mật khẩu hiện tại không đúng.');
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return Redirect::route('profile.view')->with('success', 'Đổi mật khẩu thành công.');
    }
}

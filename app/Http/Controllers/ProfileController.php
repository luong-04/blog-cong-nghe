<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
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
        
        // [MỚI] Xử lý upload Avatar
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }
            // Lưu ảnh mới
            $path = $request->file('avatar')->store('avatars', 'public');
            $request->user()->avatar = $path;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
    public function requestAuthor(Request $request)
    {
        // Lấy user hiện tại
        $user = $request->user();

        // Nếu đang là user thường thì chuyển thành 'pending' (chờ duyệt)
        if ($user->role === 'user') {
            $user->role = 'pending'; 
            $user->save();
            return back()->with('status', 'request-sent'); // Gửi thông báo thành công
        }

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user->identity_status === 'pending') {
            return redirect()->route('student.identity.pending');
        }

        if ($user->identity_status === 'approved') {
            return redirect()->route('student.dashboard');
        }

        return view('student.identity-verify', compact('user'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'tazkira_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'tazkira_image.required' => 'لطفاً تصویر تذکره خود را انتخاب کنید.',
            'tazkira_image.image'    => 'فایل باید تصویر باشد.',
            'tazkira_image.mimes'    => 'فرمت تصویر باید jpg، jpeg، png یا webp باشد.',
            'tazkira_image.max'      => 'حجم تصویر نباید بیشتر از 5 مگابایت باشد.',
        ]);

        $user = Auth::user();

        if ($user->tazkira_image) {
            Storage::disk('public')->delete($user->tazkira_image);
        }

        $path = $request->file('tazkira_image')->store('tazkira', 'public');

        $user->update([
            'tazkira_image'   => $path,
            'identity_status' => 'pending',
            'identity_rejection_reason' => null,
        ]);

        return redirect()->route('student.identity.pending')
            ->with('success', 'تصویر تذکره شما با موفقیت آپلود شد. لطفاً منتظر تأیید ادمین بمانید.');
    }

    public function pending()
    {
        $user = Auth::user();

        if ($user->identity_status === 'approved') {
            return redirect()->route('student.dashboard');
        }

        if ($user->identity_status === 'not_submitted' || $user->identity_status === null) {
            return redirect()->route('student.identity.show');
        }

        return view('student.identity-pending', compact('user'));
    }
}

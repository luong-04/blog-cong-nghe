<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'link' => 'nullable|url',
        ]);

        $path = $request->file('image')->store('ads', 'public');

        Ad::create([
            'title' => $request->title,
            'image' => $path,
            'link' => $request->link,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ads.index')->with('success', 'Thêm Banner thành công!');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image) Storage::disk('public')->delete($ad->image);
        $ad->delete();
        return back()->with('success', 'Đã xóa Banner!');
    }

    public function toggle(Ad $ad)
    {
        $ad->update(['is_active' => !$ad->is_active]);
        return back()->with('success', 'Đã đổi trạng thái!');
    }
}
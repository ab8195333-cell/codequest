<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            // حفظ الصورة في storage/app/public/editor
            $path = $request->file('image')->store('editor', 'public');

            return response()->json([
                'url' => asset('storage/' . $path)
            ], 200);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
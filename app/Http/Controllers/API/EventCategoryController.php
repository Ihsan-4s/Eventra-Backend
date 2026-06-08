<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::all();
        return response()->json([
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name'
        ]);
            $category = EventCategory::create([
            'name' => $request->name
        ]);
        return response()->json([
            'message'  => 'Kategori berhasil dibuat.',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, EventCategory $eventCategory)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:event_categories,name,' . $eventCategory->id,
        ]);
        $eventCategory->update([
            'name' => $request->name
        ]);
        return response()->json([
            'message'  => 'Kategori berhasil diperbarui.',
            'category' => $eventCategory
        ]);
    }

    public function destroy(EventCategory $eventCategory)
    {
        // cek category lagi dipake event ga?
        if ($eventCategory->events()->exists()) {
            return response()->json([
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan event.'
            ], 422);
        }
        $eventCategory->delete();
        return response()->json([
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }
}

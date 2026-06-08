<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class EventController extends Controller
{
    public function show(string $slug)
    {
        $event = Event::with(['category', 'user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        return response()->json(['event' => $event]);
    }

    public function show_organizer(Event $event, Request $request)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        return response()->json(['event' => $event->load('category')]);
    }

    public function myEvents(Request $request)
    {
        $perPage = $request->get('per_page', 9);
        $events = Event::with('category')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'=> 'required|exists:event_categories,id',
            'organization_name'=> 'required|string|max:255',
            'title'=> 'required|string|max:255',
            'banner'=> 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
            'description'=> 'required|string',
            'location'=> 'required|string|max:255',
            'event_date'=> 'required|date|after:today',
            'price'=> 'required|numeric|min:0',
            'quota'=> 'required|integer|min:1',
            'status'=> 'sometimes|in:draft,published,cancelled',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }
        $slug = Str::slug($request->title);
        $event = Event::create([
            'user_id'=> $request->user()->id,
            'category_id'=> $request->category_id,
            'organization_name'=> $request->organization_name,
            'title'=> $request->title,
            'slug'=> $slug,
            'banner'=> $bannerPath,
            'description'=> $request->description,
            'location'=> $request->location,
            'event_date'=> $request->event_date,
            'price'=> $request->price,
            'quota'=> $request->quota,
            'status'=> $request->status ?? 'draft',
        ]);

        return response()->json([
            'message'=> 'Event berhasil dibuat.',
            'event'=> $event->load('category'),
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'category_id' => 'sometimes|exists:event_categories,id',
            'organization_name' => 'sometimes|string|max:255',
            'title'=> 'sometimes|string|max:255',
            'banner'=> 'sometimes|image|mimes:jpg,jpeg,png|max:5120',
            'description'=> 'sometimes|string',
            'location'=> 'sometimes|string|max:255',
            'event_date'=> 'sometimes|date',
            'price'=> 'sometimes|numeric|min:0',
            'quota'=> 'sometimes|integer|min:1',
            'status'=> 'sometimes|in:draft,published,cancelled',
        ]);

        if ($request->hasFile('banner')) {
            if ($event->banner) {
                Storage::disk('public')->delete($event->banner);
            }
            $event->banner = $request->file('banner')->store('banners', 'public');
        }
        $event->fill($request->except(['banner', 'slug']));
        $event->save();

        return response()->json([
            'message' => 'Event berhasil diperbarui.',
            'event'   => $event->load('category'),
        ]);
    }

    public function destroy(Request $request, Event $event)
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($event->banner) {
            Storage::disk('public')->delete($event->banner);
        }

        $event->delete();

        return response()->json(['message' => 'Event berhasil dihapus.']);
    }

    public function adminIndex(Request $request)
    {
        $events = Event::with(['category', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($events);
    }
}

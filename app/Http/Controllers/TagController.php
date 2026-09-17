<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('trades')
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:strategy,emotion,mistake',
        ]);

        $tag = Tag::firstOrCreate([
            'name' => trim($validated['name']),
            'type' => $validated['type'],
        ]);

        return redirect()->route('tags.index')
            ->with('success', "Tag \"{$tag->name}\" berhasil ditambahkan!");
    }

    public function destroy(Tag $tag)
    {
        // Detach from trades and delete
        $tag->trades()->detach();
        $name = $tag->name;
        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', "Tag \"{$name}\" berhasil dihapus.");
    }
}

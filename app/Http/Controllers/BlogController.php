<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index()
    {
        $blogs = Blog::with('catalog')
            ->orderBy('publish_date', 'desc')
            ->paginate(10);

        return view('pages.apps.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        $catalogs = Catalog::where('is_active', true)->orderBy('name')->get();
        return view('pages.apps.blogs.functions.create', compact('catalogs'));
    }

    /**
     * Store a newly created blog in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'publish_date' => 'required|date',
            'catalog_id' => 'nullable|exists:catalogs,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'author_name' => 'required|string|max:255',
            'author_position' => 'nullable|string|max:255',
            'author_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'allow_comments' => 'nullable|boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Auto-generate meta title if not provided
        if (!isset($validated['meta_title']) || empty($validated['meta_title'])) {
            $validated['meta_title'] = $validated['title'];
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blogs/featured', 'public');
        }

        // Handle author profile image upload
        if ($request->hasFile('author_profile_image')) {
            $validated['author_profile_image'] = $request->file('author_profile_image')->store('blogs/authors', 'public');
        }

        // Convert allow_comments checkbox
        $validated['allow_comments'] = $request->has('allow_comments') ? 1 : 0;

        $blog = Blog::create($validated);

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog created successfully!');
    }

    /**
     * Display the specified blog.
     */
    public function show(Blog $blog)
    {
        $blog->load('catalog');
        return view('pages.apps.blogs.functions.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog)
    {
        $catalogs = Catalog::where('is_active', true)->orderBy('name')->get();
        return view('pages.apps.blogs.functions.update', compact('blog', 'catalogs'));
    }

    /**
     * Update the specified blog in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|string',
            'publish_date' => 'required|date',
            'catalog_id' => 'nullable|exists:catalogs,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'author_name' => 'required|string|max:255',
            'author_position' => 'nullable|string|max:255',
            'author_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'allow_comments' => 'nullable|boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blogs/featured', 'public');
        }

        // Handle author profile image upload
        if ($request->hasFile('author_profile_image')) {
            // Delete old image if exists
            if ($blog->author_profile_image) {
                Storage::disk('public')->delete($blog->author_profile_image);
            }
            $validated['author_profile_image'] = $request->file('author_profile_image')->store('blogs/authors', 'public');
        }

        // Convert allow_comments checkbox
        $validated['allow_comments'] = $request->has('allow_comments') ? 1 : 0;

        $blog->update($validated);

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog updated successfully!');
    }

    /**
     * Remove the specified blog from storage (soft delete).
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog moved to trash successfully!'
            ]);
        }

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog moved to trash successfully!');
    }
}

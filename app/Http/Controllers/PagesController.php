<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagesController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Page::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortField = $request->get('sort_field', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $pages = $query->paginate($request->get('per_page', 10));

            return response()->json($pages);
        }

        return view('pages.apps.pages.index');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set default status
        if (empty($validated['status'])) {
            $validated['status'] = 'draft';
        }

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully!',
            'page' => $page
        ]);
    }

    /**
     * Display the specified page.
     */
    public function show(Request $request, Page $page)
    {
        // If it's an AJAX request, return JSON for editing
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($page);
        }

        // Otherwise return the view
        return view('pages.apps.pages.show', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully!',
            'page' => $page
        ]);
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully!'
        ]);
    }

    /**
     * Show the form for editing SEO data.
     */
    public function seoEdit(Page $page)
    {
        return view('pages.apps.pages.seo-edit', compact('page'));
    }

    /**
     * Update the SEO data for the specified page.
     */
    public function seoUpdate(Request $request, Page $page)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
            'og_type' => 'nullable|string|max:50',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string|max:500',
            'twitter_image' => 'nullable|url',
            'canonical_url' => 'nullable|url',
            'structured_data' => 'nullable|json',
        ]);

        $page->update($validated);

        return redirect()
            ->route('pages.index')
            ->with('success', 'SEO data updated successfully!');
    }

    /**
     * Show the form for editing page content.
     */
    public function contentEdit(Page $page)
    {
        return view('pages.apps.pages.content-edit', compact('page'));
    }

    /**
     * Update the content for the specified page.
     */
    public function contentUpdate(Request $request, Page $page)
    {
        $validated = $request->validate([
            'content_blocks' => 'nullable|json',
        ]);

        // Decode and validate content_blocks if provided
        if (isset($validated['content_blocks'])) {
            $contentBlocks = json_decode($validated['content_blocks'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()
                    ->back()
                    ->withErrors(['content_blocks' => 'Invalid JSON format'])
                    ->withInput();
            }

            $page->content_blocks = $contentBlocks;
        } else {
            $page->content_blocks = null;
        }
        $page->save();

        return redirect()
            ->route('pages.index')
            ->with('success', 'Page content updated successfully!');
    }
}

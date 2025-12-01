<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;

class ArtikelController extends Controller
{
    public function index()
    {
        $articles = Artikel::where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        $categories = KategoriArtikel::withCount(['artikels' => function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        }])->get();

        return view('frontend.artikel.index', compact('articles', 'categories'));
    }

    public function show($slug)
    {
        $article = Artikel::where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['kategori', 'author']) // Eager load relationships to avoid N+1 problems
            ->firstOrFail();

        // Get related articles (optional - same category, excluding current article)
        $relatedArticles = Artikel::where('kategori_id', $article->kategori_id)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $article->id)
            ->with(['kategori', 'author']) // Eager load relationships
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('frontend.artikel.show', compact('article', 'relatedArticles'));
    }
}
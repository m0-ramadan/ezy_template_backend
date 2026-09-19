<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles.index', ['articles' => Article::latest()->paginate(15)]);
    }
    public function create()
    {
        return view('admin.articles.form', ['article' => new Article]);
    }
    public function store(Request $r)
    {
        $d = $this->v($r);
        $d['slug'] = Str::slug($d['title']) . '-' . Str::lower(Str::random(4));
        if ($r->hasFile('cover_image_file')) {
            $path = $r->file('cover_image_file')->store('articles/covers', 'public');
            $d['cover_image'] = '/storage/' . $path;
        }
        Article::create($d);
        return redirect()->route('admin.articles.index')->with('success', 'Article created.');
    }
    public function edit(Article $article)
    {
        return view('admin.articles.form', compact('article'));
    }
    public function update(Request $r, Article $article)
    {
        $d = $this->v($r);
        $d['slug'] = Str::slug($d['title']);
        if ($r->hasFile('cover_image_file')) {
            $path = $r->file('cover_image_file')->store('articles/covers', 'public');
            $d['cover_image'] = '/storage/' . $path;
        }
        $article->update($d);
        return back()->with('success', 'Article updated.');
    }
    public function destroy(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Article deleted.');
    }
    private function v(Request $r)
    {
        return $r->validate(['title' => 'required|max:180', 'excerpt' => 'nullable|max:500', 'content' => 'required', 'cover_image' => 'nullable|max:500', 'category' => 'nullable|max:80', 'author_name' => 'nullable|max:120', 'reading_time' => 'integer|min:1|max:120', 'status' => 'required|in:draft,published,archived', 'published_at' => 'nullable|date', 'meta_title' => 'nullable|max:180', 'meta_description' => 'nullable|max:500']);
        return $r->validate([
            'title' => 'required|max:180',
            'title_ar' => 'nullable|max:180',
            'excerpt' => 'nullable|max:500',
            'excerpt_ar' => 'nullable|max:500',
            'content' => 'required',
            'content_ar' => 'nullable',
            'cover_image' => 'nullable|max:500',
            'category' => 'nullable|max:80',
            'author_name' => 'nullable|max:120',
            'reading_time' => 'integer|min:1|max:120',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|max:180',
            'meta_description' => 'nullable|max:500',
        ]);
    }
}

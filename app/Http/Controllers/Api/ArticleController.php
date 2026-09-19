<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\Article;
class ArticleController extends Controller { public function index(){return response()->json(Article::published()->latest('published_at')->paginate(12));} public function show(string $slug){return response()->json(Article::published()->where('slug',$slug)->firstOrFail());} }

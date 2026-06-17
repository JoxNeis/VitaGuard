<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTopic;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $articles = Article::all();
        return view('pages.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::find($id);
        dd($article);
        // return view('articles.read', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //WHO CAN EDIT?
        //ADMIN -> Check by MIDDLEWARE & CREATOR -> Check by BUSSINESS LOGIC (Service)
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getLatestArticles(Request $request)
    {
        $query = Article::query();

        if ($request->has('topic')) {
            $query->where('article_topic_id', $request->topic);
        }

        $articles = $query->latest()->take(10)->get();

        if (count($articles) > 0) {
            return response()->json([
                'success' => true,
                'data' => $articles
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Data not found'
            ]);
        }
    }

    public function getArticleTopics()
    {
        $topics = ArticleTopic::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->take(5)
            ->get();

        if (count($topics) > 0) {
            return response()->json([
                'success' => true,
                'data' => $topics,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Data not found',
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function show(Category $category,Request $request,Topic $topic)
    {
        //读取分类id关联的话题
        $topics = $topic->withOrder($request->order)
                        ->where('category_id',$category->id)
                        ->paginate(20);
        return view('topics.index',compact('topics','category'));
    }
}

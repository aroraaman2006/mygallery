<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Auth;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->category){
            $posts = Post::where('user_id', Auth()->id())->where('category', $request->category);
            $data['images']=$posts->simplePaginate(8);
            return view('home', $data);
        }
        else{
            $posts = Post::where('user_id', Auth()->id());
            $data['images']=$posts->simplePaginate(8);
            return view('home', $data);
        }       
    }
}
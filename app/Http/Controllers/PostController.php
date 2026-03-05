<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //Se ajusta para mirar la pagina de inicio
    public function index(){
    
        return view('index',[
            'posts' => Post::latest()->paginate()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\V1\PostResource;

class PostController extends Controller
{
    
    //Se consulta id 
    public function show(Post $post)
    {

        return new PostResource($post);

    }

    //Se consulta todos los datos
    public function index()
    {
        return PostResource::collection(Post::latest()->paginate());
    }

    //Se elimina un registro
    public function destroy(Post $post){

        try {
            $post->delete();
            return response()->json(['message' => 'Post eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar', 'error' => $e->getMessage()], 500);
        }

    }

}

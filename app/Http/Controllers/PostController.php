<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    //

    public function index(): view
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index',compact('posts'));
    }

    public function create(): view
    {
        return view('posts.create');
    }
    
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request,[
            'image'=>'required|image|mimes:jpeg,jpg,png|max:2048',
            'title'=>'required|min:5',
            'content'=>'required|min:10',
        ]);

        $image = $request->file('image');
        $image->storeAs('public/posts',$image->hashName());

        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan']);
    }

    public function show(string $id): view
    {
        $post =Post::findOrFail($id);

        return view('posts.show',compact('post'));
    }

    public function edit(string $id): view
    {
        $post = Post::findOrFail($id);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request,[
            
            'title'=>'required|min:5',
            'content'=>'required|min:10',
        ]);

        $post = Post::findOrFail($id);

        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $image->storeAs('public/posts',$image->hashName());

            Storage::delete('public/posts'.$post->image);
            
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content
            ]);

        }
        else 
        {

            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        $post = Post::findOrFail($id);  

        Storage::delete('public/posts/'.$post->image);

        $post->delete();
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}

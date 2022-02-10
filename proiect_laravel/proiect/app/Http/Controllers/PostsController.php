<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Cviebrock\EloquentSluggable\Services\SlugService;

class PostsController extends Controller
{

    // constructorul foloseste middleware-ul pentru autentificare la randarea paginii
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // returneaza vederea indexului paginii blogului cu postarile ordonate descrescator in functie de data actualizarii
    // get() injecteaza datele obtinute in vedere
    public function index()
    {
        return view('blog.index')
            ->with('posts', Post::orderBy('updated_at', 'DESC')->get());
        // SELECT * FROM posts ORDER BY updated_at DESC;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // returneaza vederea pentru crearea unei postari
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // metoda pentru stocarea datelor introduse de catre utilizator in baza de date
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg|max:5048' // doar formatele jpg,png,jpeg si maxim 5Mb
        ]);

        // formateaza numele imaginii pentru stocare
        $newImageName = uniqid() . '-' . $request->title . '.' . $request->image->extension();

        // plaseaza imaginea in directorul imaginilor din server (unde se salveaza imaginile)
        $request->image->move(public_path('images'), $newImageName);

        // introduce datele in baza de date
        Post::create([
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'slug' => SlugService::createSlug(Post::class, 'slug', $request->title), // acronimul postarii (/blog/<titlu_postare>/)
            'image_path' => $newImageName,
            'user_id' => auth()->user()->id // utilizatorul care a facut postarea
        ]); // INSERT INTO posts(title, category, ..., user_id) VALUES(X, X, X, etc.)

        // redirectionare catre pagina blog dupa crearea postarii si afiseaza un mesaj
        return redirect('/blog')
            ->with('message', 'Postarea a fost adaugata!');
    }

    /**
     * Display the specified resource.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    // returneaza vederea postarii in functie de acronim (alege primul rezultat)
    public function show($slug)
    {
        return view('blog.show')
            ->with('post', Post::where('slug', $slug)->first()); // cerere de tip POST
        // SELECT * FROM posts WHERE slug = ? LIMIT 1;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    // returneaza vederea pentru editarea postarii (in functie de acronim si primul rezultat)
    public function edit($slug)
    {
        return view('blog.edit')
            ->with('post', Post::where('slug', $slug)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    // metoda pentru actualizarea unei postari cu datele introduse de catre untilizator
    public function update(Request $request, $slug)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        // UPDATE FROM posts SET title = X, category = X, ... WHERE slug = X
        Post::where('slug', $slug)
            ->update([
                'title' => $request->input('title'),
//                'category' => $request->input('category'),
                'description' => $request->input('description'),
                'slug' => SlugService::createSlug(Post::class, 'slug', $request->title),
                'user_id' => auth()->user()->id
            ]);

        // redirectionare spre pagina blog + mesaj
        return redirect('/blog')
            ->with('message', 'Postarea a fost actualizata');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    // metoda pentru stergerea unei postari in functie de acronim
    public function destroy($slug)
    {
        $post = Post::where('slug', $slug); // SELECT * FROM posts WHERE slug = X;
        $post->delete(); // DELETE FROM posts WHERE slug = X;

        // redirectionare catre pagina blog dupa stergerea postarii
        return redirect('/blog')
            ->with('message', 'Postarea a fost stearsa!');
    }
}

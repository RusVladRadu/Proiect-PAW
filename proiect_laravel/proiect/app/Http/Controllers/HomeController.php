<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); // folosire middleware pentru autentificare/inregistrare
    }

    /**
     * Returneaza vederea index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('index');
    }
}

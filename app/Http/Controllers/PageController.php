<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function petunjuk()
    {
        return view('petunjuk');
    }

    public function tentang()
    {
        return view('tentang');
    }
}

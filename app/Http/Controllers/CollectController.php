<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class CollectController extends Controller
{

    public function index()
    {
        $sites = Site::all();

        return view('collect.index', compact('sites'));
    }


}

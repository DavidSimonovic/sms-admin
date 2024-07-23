<?php

namespace App\Http\Controllers;

use App\Jobs\RunScriptJob;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::all();
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'site_url' => 'required',
            'script' => 'required'
        ]);

        $site = new Site();
        $site->name = $request->name;
        $site->site_url = $request->site_url;
        $site->script = $request->script;
        $site->save();

        return redirect("/sites")->with('message', 'Record Saved');
    }

    public function edit($id)
    {
        $m = Site::findOrFail($id);
        return view('sites.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $site = Site::findOrFail($id);
        $site->name = $request->name;
        $site->site_url = $request->site_url;
        $site->script = $request->script;
        $site->save();

        return redirect("/sites")->with('message', 'Record Updated');
    }

    public function delete($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();

        return redirect("/sites")->with('message', 'Site deleted');
    }

}

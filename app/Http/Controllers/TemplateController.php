<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;

class TemplateController extends Controller
{
    public function index(Request $request)
    {
        $m = Template::where('id', '!=', 999)->get();
        return view('templates.index', compact('m'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'template' => 'required',
        ]);

        $template = new Template;
        $template->title = $request->name;
        $template->text = $request->template;
        $template->save();

        return redirect("/templates")->with('message', 'Record Saved');
    }

    public function edit($id)
    {
        $m = Template::findOrFail($id);
        return view('templates.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'text' => 'required',
        ]);

        $template = Template::findOrFail($id);
        $template->title = $request->title;
        $template->text = $request->text;
        $template->save();

        return redirect("/templates")->with('message', 'Record Updated');
    }

    public function delete($id)
    {
        $template = Template::findOrFail($id);
        $template->delete();

        return redirect("/templates")->with('message', 'Template deleted');
    }
}

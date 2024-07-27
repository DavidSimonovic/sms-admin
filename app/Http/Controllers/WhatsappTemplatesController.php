<?php

namespace App\Http\Controllers;

use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;

class WhatsappTemplatesController extends Controller
{

    public function index(Request $request)
    {
        $m = WhatsappTemplate::where('id', '!=', 999)->get();
        return view('whatsapp_templates.index', compact('m'));
    }

    public function create()
    {
        return view('whatsapp_templates.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'template' => 'required',
        ]);

        $template = new WhatsappTemplate;
        $template->title = $request->name;
        $template->text = $request->template;
        $template->save();

        return redirect("/whatsapp_templates")->with('message', 'Record Saved');
    }

    public function edit($id)
    {
        $m = WhatsappTemplate::findOrFail($id);
        return view('whatsapp_templates.edit', compact('m'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'text' => 'required',
        ]);

        $template = WhatsappTemplate::findOrFail($id);
        $template->title = $request->title;
        $template->text = $request->text;
        $template->save();

        return redirect("/whatsapp_templates")->with('message', 'Record Updated');
    }

    public function delete($id)
    {
        $template = WhatsappTemplate::findOrFail($id);
        $template->delete();

        return redirect("/whatsapp_templates")->with('message', 'WhatsappTemplate deleted');
    }

}

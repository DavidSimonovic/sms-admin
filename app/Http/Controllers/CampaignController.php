<?php

namespace App\Http\Controllers;

use App\Models\Number;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\Site;
use App\Models\Template;

class CampaignController extends Controller
{

    public function index()
    {

        $m = Campaign::all();

        return view('campaigns.index', compact('m'));
    }


    public function create()
    {
        $sites = Site::all();
        $templates = Template::all();

        return view('campaigns.create', compact('sites', 'templates'));
    }

    public function save(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'sites' => 'required|array',
            'sites.*' => 'exists:sites,id',
            'template' => 'nullable|array',
            'template.*' => 'exists:templates,id',
            'originator' => 'required|string',
            'status' => 'required|integer|in:0,1',
        ]);

        // Retrieve validated input
        $name = $request->input('name');
        $sites = $request->input('sites'); // Array of site IDs
        $templates = $request->input('template', []); // Default to empty array if not provided
        $status = $request->input('status');
        $originator = $request->input('originator');
        // Create a new campaign
        Campaign::create([
            'name' => $name,
            'site_ids' => json_encode($sites), // Convert array to JSON
            'template_ids' => json_encode($templates), // Convert array to JSON
            'originator' => $originator,
            'status' => $status,
        ]);

        // Redirect with success message
        return redirect()->back()->with('success', 'Campaign saved successfully!');
    }

    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        $sites = Site::all();
        $templates = Template::all();
        return view('campaigns.edit', compact('campaign', 'sites', 'templates'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'sites' => 'required',
            'template' => 'required|array',
            'status' => 'required',
            'originator' => 'required'
        ]);

        $campaign = Campaign::findOrFail($request->id);
        $campaign->name = $request->name;
        $campaign->site_ids = json_encode($request->sites);
        $campaign->template_ids = json_encode($request->template);
        $campaign->status = $request->status;
        $campaign->originator = $request->originator;
        $campaign->save();

        return redirect("/campaigns")->with('message', 'Record Updated');
    }

    public function delete($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return redirect("/campaigns")->with('message', 'Campaign deleted');
    }
}

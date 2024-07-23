<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Number;
use App\Models\Site;

class NumberController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->put("filter_site_id", '');
        $request->session()->put("filter_number_id", '');

        $m = Number::with('site')
            ->where(function ($query) use ($request) {
                if ($request->has('site_id') && $request->site_id != "all") {
                    $query->where('site_id', $request->site_id);
                    $request->session()->put("filter_site_id", $request->site_id);
                }

                if ($request->has('number_id')) {
                    $query->where('id', $request->number_id);
                    $request->session()->put("filter_number_id", $request->number_id);
                }
            })
            ->paginate(25);

        $sites = Site::all();

        return view('numbers.index', compact('m', 'sites'));
    }

    public function create()
    {
        return view('numbers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Add validation rules here
        ]);

        // Assuming the creation logic is added here

        return redirect()->route('numbers.index')->with('message', 'Record Saved');
    }

    public function edit($id)
    {
        $number = Number::findOrFail($id);

        return view('numbers.edit', compact('number'));
    }

    public function update(Request $request)
    {
      $number =  Number::findOrFail($request->id);

      switch ($request->column) {
          case 'ad_title':
              $number->ad_title = $request->value;
              break;
          case 'number':
              $number->number = $request->value;
              break;
          case 'city':
              $number->city = $request->value;
              break;
          default:
              break;
      }

      $number->save();

       return response()->json($number);

        // Assuming the update logic is added here

        return redirect()->route('numbers.index')->with('message', 'Record Updated');
    }

    public function block($id)
    {
        $number = Number::findOrFail($id);
        $number->active = 1;
        $number->save();

        return redirect()->back()->with('message', 'Number Blocked');
    }

    public function destroy($id)
    {
        $number = Number::findOrFail($id);

        $number->delete();

        return redirect()->back()->with('message', 'Number Deleted');
    }

    public function unblock(Request $request)
    {
        $number = Number::findOrFail($request->id);
        $number->active = 0;
        $number->save();

        return redirect()->back()->with('message', 'Number Unblocked');
    }

    public function search(Request $request)
    {
        $search = Number::with('site')
            ->where('number', 'LIKE', "%{$request->q}%")
            ->orWhere('ad_title', 'LIKE', "%{$request->q}%");

        if ($request->site_id) {
            $search->where('site_id', $request->site_id);
        }

        $searchResults = $search->get();

        $data = [];
        foreach ($searchResults as $i => $result) {
            $data[] = [
                'id' => $result->id,
                'name' => "{$result->name} - {$result->mobile_number} - {$result->site->name}",
                'number' => $result->mobile_number,
            ];
        }

        return response()->json(['results' => $data]);
    }
}

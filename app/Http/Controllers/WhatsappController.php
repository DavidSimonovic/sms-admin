<?php

namespace App\Http\Controllers;

use App\Models\Number;
use App\Models\Site;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->put("filter_site_id", '');
        $request->session()->put("filter_number_id", '');

        $m = Number::with('site')->where('whatsapp', true)
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

        $m->getCollection()->transform(function ($item) {
            $item->formatted_number = $this->formatNumber($item->number); // Use 'number' instead of 'm'
            return $item;
        });

        $waTemplates = WhatsappTemplate::all();
        $sites = Site::all();

        return view('whatsapp.index', compact('m', 'sites', 'waTemplates'));
    }

    public function formatNumber($number)
    {
        // Remove any non-numeric characters from the number
        $number = preg_replace('/\D/', '', $number);

        // Check if the number already starts with 49
        if (strpos($number, '49') === 0) {
            return $number;
        }

        // Remove leading 0 if present
        if (strpos($number, '0') === 0) {
            $number = substr($number, 1);
        }

        // Add 49 to the start of the number
        return '49' . $number;
    }

    public function hasWhatsapp($id)
    {
       $number = Number::find($id);

       if($number->whatsapp)
       {
           $number->whatsapp = false;
           $number->save();
       }
       else {
           $number->whatsapp = true;
           $number->save();
       }
        return redirect()->back()->with('message', 'Updated');
    }


}

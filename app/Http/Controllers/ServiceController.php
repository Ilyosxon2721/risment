<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_active', true)->orderBy('sort');
        
        if ($request->has('marketplace')) {
            $query->whereIn('marketplace', [$request->marketplace, 'all']);
        }
        
        if ($request->has('scheme')) {
            $query->whereIn('scheme', [$request->scheme, 'all']);
        }
        
        $services = $query->get();
        
        return view('services.index', compact('services'));
    }
    
    public function show($locale, $slug)
    {
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        return view('services.show', compact('service'));
    }
}

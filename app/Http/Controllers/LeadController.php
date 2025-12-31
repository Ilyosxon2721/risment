<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'marketplaces' => 'nullable|array',
            'schemes' => 'nullable|array',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $validated['source_page'] = $request->header('Referer');
        $validated['status'] = 'new';
        
        $lead = Lead::create($validated);
        
        // Send email notification to admin
        try {
            \Mail::to(config('mail.admin_email', 'info@risment.uz'))
                ->send(new \App\Mail\NewLeadNotification($lead));
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send lead notification email', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return back()->with('success', __('Thank you! Your request has been sent. We will contact you soon.'));
    }
}

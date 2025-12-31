<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $tickets = Ticket::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('cabinet.tickets.index', compact('tickets'));
    }
    
    public function create()
    {
        return view('cabinet.tickets.create');
    }
    
    public function store(Request $request)
    {
        $company = $request->attributes->get('currentCompany');
        
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);
        
        $ticket = Ticket::create([
            'company_id' => $company->id,
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);
        
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);
        
        return redirect()->route('cabinet.tickets.show', $ticket)
            ->with('success', __('Ticket created successfully'));
    }
    
    public function show(Request $request, Ticket $ticket)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($ticket->company_id !== $company->id) {
            abort(403);
        }
        
        $ticket->load('messages.user');
        
        return view('cabinet.tickets.show', compact('ticket'));
    }
    
    public function addMessage(Request $request, Ticket $ticket)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($ticket->company_id !== $company->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'message' => 'required|string',
        ]);
        
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);
        
        // Reopen ticket if closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }
        
        return back()->with('success', __('Message added'));
    }
}

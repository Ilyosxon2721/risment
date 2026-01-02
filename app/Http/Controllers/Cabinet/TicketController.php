<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);
        
        $ticket = Ticket::create([
            'company_id' => $company->id,
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);
        
        $ticketMessage = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'ticket_message_id' => $ticketMessage->id,
                    'filename' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ]);
            }
        }
        
        return redirect()->route('cabinet.tickets.show', $ticket)
            ->with('success', __('Ticket created successfully'));
    }
    
    public function show(Request $request, Ticket $ticket)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($ticket->company_id !== $company->id) {
            abort(403);
        }
        
        $ticket->load(['messages.user', 'messages.attachments']);
        
        return view('cabinet.tickets.show', compact('ticket'));
    }
    
    public function reply(Request $request, Ticket $ticket)
    {
        $company = $request->attributes->get('currentCompany');
        
        if ($ticket->company_id !== $company->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'message' => 'required|string',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);
        
        $ticketMessage = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/' . $ticket->id, 'public');
                
                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'ticket_message_id' => $ticketMessage->id,
                    'filename' => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ]);
            }
        }
        
        // Reopen ticket if closed
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }
        
        return back()->with('success', __('Message added'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PettyCash;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralNotification;
use App\Models\User;
use App\Helpers\NotificationHelper;


class PettyCashController extends Controller
{
    /**
     * Show all petty cash requests.
     * Admins see all, normal users see only their own requests.
     */
    public function index()
    {
        $projects = Project::all();

        if (Auth::user()->can('approve petty cash')) {
            $pettyCashes = PettyCash::with(['user', 'project', 'approver'])->latest()->get();
        } else {
            $pettyCashes = PettyCash::with(['user', 'project', 'approver'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return view('pettycash.index', compact('pettyCashes', 'projects'));
    }

    /**
     * Show the petty cash request form.
     */
    public function create()
    {
        $projects = Project::all();
        return view('pettycash.create', compact('projects'));
    }

 
/**
 * Store a new petty cash request.
 */
public function store(Request $request)
{
    $request->validate([
        'request_type'    => 'required|in:Operational,Project',
        'expense_type'    => 'required|string|max:100',
        'project_id'      => 'required_if:request_type,Project|nullable|exists:projects,id',
        'amount'          => 'required|numeric|min:1',
        'currency'        => 'required|in:KES,USD',
        'payee_name'      => 'required|string|max:150',
        'payment_mode'    => 'required|in:Cash,Bank,Mobile Money',
        'account_details' => 'nullable|string|max:255',
        'purpose'         => 'required|string|max:500',
        'additional_notes'=> 'nullable|string|max:500',
        'expense_date'    => 'required|date',
        'receipt'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $data = $request->only([
        'request_type', 'expense_type', 'project_id', 'amount', 'currency', 
        'payee_name', 'payment_mode', 'account_details', 'purpose', 
        'additional_notes', 'expense_date'
    ]);

    $data['user_id'] = Auth::id();
    $data['status'] = 'pending';

    if ($request->hasFile('receipt')) {
        $data['receipt'] = $request->file('receipt')->store('pettycash_receipts', 'public');
    }

    $pettyCash = PettyCash::create($data);

    /**
     * Notify approvers (users who can approve petty cash)
     */
    $approvers = User::permission('approve petty cash')->get();
    foreach ($approvers as $approver) {
        $notificationData = [
            'title' => 'New Petty Cash Request',
            'message' => 'A new petty cash request has been submitted by ' . Auth::user()->name,
            'url' => route('pettycash.index'),
            'icon' => 'fa-money-bill',
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->name,
        ];
        $approver->notify(new GeneralNotification($notificationData, $approver->id));

        // WhatsApp Alert (optional)
        NotificationHelper::sendWhatsApp($approver->detail('whatsapp') ?? null, 'New petty cash request of ' . $pettyCash->currency . ' ' . $pettyCash->amount . ' request from ' . Auth::user()->name);
    }

    return redirect()->back()->with('success', 'Petty cash request submitted.');
}



public function approve(PettyCash $pettyCash)
{
    $this->authorize('approve petty cash', $pettyCash);

    $pettyCash->update([
        'status'      => 'approved',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    // Notify requester
    $user = $pettyCash->user;
    $data = [
        'title' => 'Petty Cash Approved',
        'message' => 'Your petty cash request of ' . $pettyCash->amount . ' ' . $pettyCash->currency . ' has been approved.',
        'url' => route('pettycash.index'),
        'icon' => 'fa-check-circle',
        'sender_id' => Auth::id(),
        'sender_name' => Auth::user()->name,
    ];

    $user->notify(new GeneralNotification($data, $user->id));

    NotificationHelper::sendWhatsApp($user->detail('whatsapp') ?? null, 'Your petty cash request of ' . $pettyCash->currency . ' ' . $pettyCash->amount . '  has been approved.');

    return redirect()->back()->with('success', 'Petty cash approved.');
}

/**
 * Reject a petty cash request.
 */
public function reject(PettyCash $pettyCash)
{
    $this->authorize('approve petty cash', $pettyCash);

    $pettyCash->update([
        'status'      => 'rejected',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    // Notify requester
    $user = $pettyCash->user;
    $data = [
        'title' => 'Petty Cash Rejected',
        'message' => 'Your petty cash request of ' . $pettyCash->amount . ' ' . $pettyCash->currency . ' has been rejected.',
        'url' => route('pettycash.index'),
        'icon' => 'fa-times-circle',
        'sender_id' => Auth::id(),
        'sender_name' => Auth::user()->name,
    ];

    $user->notify(new GeneralNotification($data, $user->id));
    NotificationHelper::sendWhatsApp($user->detail('whatsapp') ?? null, 'Your petty cash request of ' . $pettyCash->amount . ' ' . $pettyCash->currency . ' has been rejected.');

    return redirect()->back()->with('success', 'Petty cash rejected.');
}

}

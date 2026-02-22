<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ManagerTask;
use App\Models\ShipmentFbo;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmationController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $tasks = ManagerTask::forCompany($company->id)
            ->fromSellermind()
            ->pending()
            ->with('creator')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('manager.confirmations.index', compact('tasks'));
    }

    public function confirm(Request $request, ManagerTask $task, BillingService $billingService)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($task->company_id === $company->id, 403);
        abort_unless($task->status === ManagerTask::STATUS_PENDING, 422, 'Задача уже обработана');

        DB::transaction(function () use ($task, $company, $billingService) {
            $task->update([
                'status' => ManagerTask::STATUS_CONFIRMED,
                'confirmed_by' => auth('manager')->id(),
                'confirmed_at' => now(),
            ]);

            // Fire billing via shared method
            TaskController::fireBilling($task, $company, $billingService);
        });

        return redirect()->route('manager.confirmations.index')
            ->with('success', 'Задача подтверждена, биллинг начислен');
    }

    public function reject(Request $request, ManagerTask $task)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($task->company_id === $company->id, 403);
        abort_unless($task->status === ManagerTask::STATUS_PENDING, 422, 'Задача уже обработана');

        $task->update([
            'status' => ManagerTask::STATUS_REJECTED,
            'confirmed_by' => auth('manager')->id(),
            'confirmed_at' => now(),
        ]);

        return redirect()->route('manager.confirmations.index')
            ->with('success', 'Задача отклонена');
    }
}

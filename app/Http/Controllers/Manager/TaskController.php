<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BillingItem;
use App\Models\ManagerTask;
use App\Models\PricingRate;
use App\Models\ShipmentFbo;
use App\Services\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $query = ManagerTask::forCompany($company->id)->with('creator');

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('manager.tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $company = $request->attributes->get('managerCompany');

        $shipments = ShipmentFbo::where('company_id', $company->id)
            ->whereIn('status', ['submitted', 'picking', 'packed'])
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('manager.tasks.create', compact('shipments'));
    }

    public function store(Request $request, BillingService $billingService)
    {
        $company = $request->attributes->get('managerCompany');

        $validated = $request->validate([
            'task_type' => 'required|in:inbound,pickpack,delivery,storage,return',
            'task_date' => 'required|date',
            'comment' => 'nullable|string|max:500',
            'boxes_count' => 'nullable|integer|min:1',
            'reference' => 'nullable|string|max:100',
            'shipment_id' => 'nullable|exists:shipments_fbo,id',
            'storage_boxes' => 'nullable|integer|min:0',
            'storage_bags' => 'nullable|integer|min:0',
            'return_qty' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $company, $billingService) {
            $task = ManagerTask::create([
                'company_id' => $company->id,
                'created_by' => auth('manager')->id(),
                'confirmed_by' => auth('manager')->id(),
                'task_type' => $validated['task_type'],
                'source' => ManagerTask::SOURCE_MANUAL,
                'status' => ManagerTask::STATUS_CONFIRMED,
                'details' => $this->buildDetails($validated),
                'comment' => $validated['comment'] ?? null,
                'task_date' => $validated['task_date'],
                'confirmed_at' => now(),
            ]);

            $this->fireBilling($task, $company, $billingService);
        });

        return redirect()->route('manager.tasks.index')
            ->with('success', 'Задача добавлена, биллинг начислен');
    }

    public function show(Request $request, ManagerTask $task)
    {
        $company = $request->attributes->get('managerCompany');
        abort_unless($task->company_id === $company->id, 403);

        $task->load('creator', 'confirmer', 'billingItems');

        return view('manager.tasks.show', compact('task'));
    }

    private function buildDetails(array $validated): array
    {
        $details = [];

        switch ($validated['task_type']) {
            case 'inbound':
                $details['boxes_count'] = $validated['boxes_count'] ?? 0;
                $details['reference'] = $validated['reference'] ?? '';
                break;
            case 'pickpack':
            case 'delivery':
                $details['shipment_id'] = $validated['shipment_id'] ?? null;
                break;
            case 'storage':
                $details['storage_boxes'] = $validated['storage_boxes'] ?? 0;
                $details['storage_bags'] = $validated['storage_bags'] ?? 0;
                break;
            case 'return':
                $details['return_qty'] = $validated['return_qty'] ?? 0;
                break;
        }

        return $details;
    }

    public static function fireBilling(ManagerTask $task, $company, BillingService $billingService): void
    {
        $details = $task->details ?? [];
        $items = [];

        switch ($task->task_type) {
            case ManagerTask::TYPE_INBOUND:
                $qty = $details['boxes_count'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'INBOUND_BOX')->where('is_active', true)->value('value') ?? 0;
                    if ($rate > 0) {
                        $item = $billingService->accrueManual(
                            $company->id,
                            'inbound',
                            'Приёмка товаров',
                            'Tovarlarni qabul qilish',
                            (int) $rate,
                            $qty,
                            $task->comment
                        );
                        $items[] = $item;
                    }
                }
                break;

            case ManagerTask::TYPE_PICKPACK:
                $shipmentId = $details['shipment_id'] ?? null;
                if ($shipmentId) {
                    $shipment = ShipmentFbo::with('items.variant')->find($shipmentId);
                    if ($shipment && $shipment->company_id === $company->id) {
                        $task->update([
                            'source_type' => ShipmentFbo::class,
                            'source_id' => $shipment->id,
                        ]);
                        $items = $billingService->accrueForShipmentPickPack($shipment);
                    }
                }
                break;

            case ManagerTask::TYPE_DELIVERY:
                $shipmentId = $details['shipment_id'] ?? null;
                if ($shipmentId) {
                    $shipment = ShipmentFbo::with('items.variant')->find($shipmentId);
                    if ($shipment && $shipment->company_id === $company->id) {
                        $task->update([
                            'source_type' => ShipmentFbo::class,
                            'source_id' => $shipment->id,
                        ]);
                        $items = $billingService->accrueForShipmentDelivery($shipment);
                    }
                }
                break;

            case ManagerTask::TYPE_STORAGE:
                $boxes = $details['storage_boxes'] ?? 0;
                $bags = $details['storage_bags'] ?? 0;

                if ($boxes > 0) {
                    $rate = PricingRate::where('code', 'STORAGE_BOX_DAY')->where('is_active', true)->value('value') ?? 0;
                    if ($rate > 0) {
                        $items[] = $billingService->accrueManual(
                            $company->id, 'storage',
                            'Хранение (коробки)', 'Saqlash (qutlar)',
                            (int) $rate, $boxes, $task->comment
                        );
                    }
                }
                if ($bags > 0) {
                    $rate = PricingRate::where('code', 'STORAGE_BAG_DAY')->where('is_active', true)->value('value') ?? 0;
                    if ($rate > 0) {
                        $items[] = $billingService->accrueManual(
                            $company->id, 'storage',
                            'Хранение (мешки)', 'Saqlash (sumkalar)',
                            (int) $rate, $bags, $task->comment
                        );
                    }
                }
                break;

            case ManagerTask::TYPE_RETURN:
                $qty = $details['return_qty'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'DELIVERY_MGT')->where('is_active', true)->value('value') ?? 0;
                    if ($rate > 0) {
                        $items[] = $billingService->accrueManual(
                            $company->id, 'returns',
                            'Обратная логистика', 'Qaytish logistikasi',
                            (int) $rate, $qty, $task->comment
                        );
                    }
                }
                break;
        }

        // Link billing items to manager task
        foreach ($items as $item) {
            if ($item instanceof BillingItem) {
                $item->update(['manager_task_id' => $task->id]);
            }
        }
    }
}

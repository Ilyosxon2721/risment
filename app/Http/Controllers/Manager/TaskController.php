<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BillingBalance;
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

        $taskTypes = implode(',', array_keys(ManagerTask::getTaskTypes()));

        $validated = $request->validate([
            'task_type' => "required|in:{$taskTypes}",
            'task_date' => 'required|date',
            'comment' => 'nullable|string|max:500',
            // Inbound
            'boxes_count' => 'nullable|integer|min:1',
            'reference' => 'nullable|string|max:100',
            // Pickpack/Delivery with shipment
            'shipment_id' => 'nullable|exists:shipments_fbo,id',
            // Pickpack/Delivery manual (without shipment)
            'items_count' => 'nullable|integer|min:1',
            'order_number' => 'nullable|string|max:100',
            'pickpack_rate' => 'nullable|numeric|min:0', // Manual rate for pickpack
            // Storage
            'storage_boxes' => 'nullable|integer|min:0',
            'storage_bags' => 'nullable|integer|min:0',
            // Return
            'return_qty' => 'nullable|integer|min:1',
            'return_category' => 'nullable|in:micro,mgt,sgt,kgt',
            // Shipping (delivery) - by category
            'delivery_micro' => 'nullable|integer|min:0',
            'delivery_mgt' => 'nullable|integer|min:0',
            'delivery_sgt' => 'nullable|integer|min:0',
            'delivery_kgt' => 'nullable|integer|min:0',
            'delivery_address' => 'nullable|string|max:500',
            'recipient_name' => 'nullable|string|max:100',
            'recipient_phone' => 'nullable|string|max:20',
            // Packaging/Labeling/Photo
            'units_count' => 'nullable|integer|min:1',
            // Other/Custom
            'custom_amount' => 'nullable|numeric|min:0',
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
                $details['items_count'] = $validated['items_count'] ?? 0;
                $details['order_number'] = $validated['order_number'] ?? '';
                $details['pickpack_rate'] = $validated['pickpack_rate'] ?? null;
                break;
            case 'storage':
                $details['storage_boxes'] = $validated['storage_boxes'] ?? 0;
                $details['storage_bags'] = $validated['storage_bags'] ?? 0;
                break;
            case 'return':
                $details['return_qty'] = $validated['return_qty'] ?? 0;
                $details['return_category'] = $validated['return_category'] ?? 'mgt';
                break;
            case 'shipping':
                $details['delivery_micro'] = $validated['delivery_micro'] ?? 0;
                $details['delivery_mgt'] = $validated['delivery_mgt'] ?? 0;
                $details['delivery_sgt'] = $validated['delivery_sgt'] ?? 0;
                $details['delivery_kgt'] = $validated['delivery_kgt'] ?? 0;
                $details['delivery_address'] = $validated['delivery_address'] ?? '';
                $details['recipient_name'] = $validated['recipient_name'] ?? '';
                $details['recipient_phone'] = $validated['recipient_phone'] ?? '';
                break;
            case 'packaging':
            case 'labeling':
            case 'photo':
                $details['units_count'] = $validated['units_count'] ?? 0;
                break;
            case 'inventory_check':
                $details['units_count'] = $validated['units_count'] ?? 0;
                break;
            case 'other':
                $details['custom_amount'] = $validated['custom_amount'] ?? 0;
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
                } else {
                    // Manual pickpack without shipment - use custom rate
                    $qty = $details['items_count'] ?? 0;
                    $rate = $details['pickpack_rate'] ?? null;
                    if ($qty > 0 && $rate > 0) {
                        $items[] = $billingService->accrueManual(
                            $company->id, 'pickpack',
                            'Сборка заказа', 'Buyurtmani yig\'ish',
                            (int) $rate, $qty, $task->comment ?: ($details['order_number'] ?? null)
                        );
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
                } else {
                    // Manual delivery without shipment
                    $qty = $details['items_count'] ?? 0;
                    if ($qty > 0) {
                        $rate = PricingRate::where('code', 'DELIVERY_MGT')->where('is_active', true)->value('value') ?? 10000;
                        $items[] = $billingService->accrueManual(
                            $company->id, 'shipping',
                            'Отгрузка', 'Yuklash',
                            (int) $rate, $qty, $task->comment ?: ($details['order_number'] ?? null)
                        );
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
                    $category = strtoupper($details['return_category'] ?? 'MGT');
                    $rate = PricingRate::where('code', "DELIVERY_{$category}")->where('is_active', true)->value('value') ?? 0;
                    if ($rate > 0) {
                        $items[] = $billingService->accrueManual(
                            $company->id, 'returns',
                            "Обратная логистика ({$category})", "Qaytish logistikasi ({$category})",
                            (int) $rate, $qty, $task->comment
                        );
                    }
                }
                break;

            case ManagerTask::TYPE_SHIPPING:
                $address = $details['delivery_address'] ?? '';
                $categories = ['MICRO', 'MGT', 'SGT', 'KGT'];
                foreach ($categories as $cat) {
                    $qty = $details['delivery_' . strtolower($cat)] ?? 0;
                    if ($qty > 0) {
                        $rate = PricingRate::where('code', "DELIVERY_{$cat}")->where('is_active', true)->value('value') ?? 0;
                        if ($rate > 0) {
                            $items[] = $billingService->accrueManual(
                                $company->id, 'shipping',
                                "Доставка ({$cat})", "Yetkazib berish ({$cat})",
                                (int) $rate, $qty, $task->comment ?: $address
                            );
                        }
                    }
                }
                break;

            case ManagerTask::TYPE_PACKAGING:
                $qty = $details['units_count'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'PACKAGING_UNIT')->where('is_active', true)->value('value') ?? 5000;
                    $items[] = $billingService->accrueManual(
                        $company->id, 'packaging',
                        'Упаковка товаров', 'Tovarlarni qadoqlash',
                        (int) $rate, $qty, $task->comment
                    );
                }
                break;

            case ManagerTask::TYPE_LABELING:
                $qty = $details['units_count'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'LABELING_UNIT')->where('is_active', true)->value('value') ?? 3000;
                    $items[] = $billingService->accrueManual(
                        $company->id, 'labeling',
                        'Маркировка товаров', 'Tovarlarni markalash',
                        (int) $rate, $qty, $task->comment
                    );
                }
                break;

            case ManagerTask::TYPE_PHOTO:
                $qty = $details['units_count'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'PHOTO_UNIT')->where('is_active', true)->value('value') ?? 15000;
                    $items[] = $billingService->accrueManual(
                        $company->id, 'photo',
                        'Фотосъёмка товаров', 'Tovarlarni suratga olish',
                        (int) $rate, $qty, $task->comment
                    );
                }
                break;

            case ManagerTask::TYPE_INVENTORY:
                $qty = $details['units_count'] ?? 0;
                if ($qty > 0) {
                    $rate = PricingRate::where('code', 'INVENTORY_CHECK')->where('is_active', true)->value('value') ?? 2000;
                    $items[] = $billingService->accrueManual(
                        $company->id, 'inventory',
                        'Инвентаризация', 'Inventarizatsiya',
                        (int) $rate, $qty, $task->comment
                    );
                }
                break;

            case ManagerTask::TYPE_OTHER:
                $amount = $details['custom_amount'] ?? 0;
                if ($amount > 0) {
                    $items[] = $billingService->accrueManual(
                        $company->id, 'other',
                        'Дополнительная услуга', 'Qo\'shimcha xizmat',
                        (int) $amount, 1, $task->comment
                    );
                }
                break;
        }

        // Link billing items to manager task
        foreach ($items as $item) {
            if ($item instanceof BillingItem) {
                $item->update(['manager_task_id' => $task->id]);
            }
        }

        // Deduct from BillingBalance: apply overage discounts, then charge
        $rawTotal = collect($items)
            ->filter(fn ($i) => $i instanceof BillingItem)
            ->sum('amount');

        if ($rawTotal > 0) {
            $chargeAmount = $company->applyDiscounts((float) $rawTotal, 'overage');
            $billingBalance = BillingBalance::getOrCreate($company->id);
            $billingBalance->charge(
                $chargeAmount,
                "Задача #{$task->id}: {$task->task_type_label}",
                ManagerTask::class,
                $task->id
            );
        }
    }
}

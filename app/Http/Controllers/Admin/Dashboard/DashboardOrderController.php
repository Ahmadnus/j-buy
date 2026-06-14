<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardOrderController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->get('q');
        $status   = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $orders = Order::query()
            ->with('user:id,name_ar,phone')
            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('order_number', 'like', "%$q%")
                      ->orWhere('customer_name', 'like', "%$q%")
                      ->orWhere('customer_phone', 'like', "%$q%");
                });
            })
            ->when($status, fn ($qb) => $qb->where('status', $status))
            ->when($dateFrom, fn ($qb) => $qb->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($qb) => $qb->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders'    => $orders,
            'q'         => $q,
            'status'    => $status,
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'statuses'  => OrderStatus::cases(),
        ]);
    }

    public function show(int $id)
    {
        $order = Order::with([
            'items',
            'user:id,name_ar,phone,email',
            'statusLogs',
        ])->findOrFail($id);

        // Convert enum objects to plain strings on each log so the Blade
        // view can safely concatenate them without touching ->value.
        // This is the permanent fix — it works regardless of the view cache.
        $order->statusLogs->transform(function ($log) {
            $log->from_status = $log->from_status instanceof \App\Enums\OrderStatus
                ? $log->from_status->value
                : (string) ($log->from_status ?? '');
            $log->to_status = $log->to_status instanceof \App\Enums\OrderStatus
                ? $log->to_status->value
                : (string) $log->to_status;
            return $log;
        });

        return view('admin.orders.show', [
            'order'              => $order,
            'allowedTransitions' => $order->status->allowedTransitions(),
        ]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|string|in:' . collect(OrderStatus::cases())
                ->map(fn ($s) => $s->value)->implode(','),
        ]);

        $order = Order::findOrFail($id);
        $next  = OrderStatus::from($request->status);

        try {
            $order->transitionTo($next, changedBy: $request->user()?->id);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', __('dashboard.updated_success'));
    }
}
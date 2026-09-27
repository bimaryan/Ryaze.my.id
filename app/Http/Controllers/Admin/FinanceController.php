<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingPayment;
use App\Models\JokiPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->filled('from') && strtotime($request->from)
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();
        $end = $request->filled('to') && strtotime($request->to)
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end, $start];
        }

        $service = in_array($request->get('service', 'all'), ['all', 'joki', 'hosting'])
            ? $request->get('service', 'all')
            : 'all';
        $method = $request->get('method');

        $rows = collect();

        if (in_array($service, ['all', 'joki'])) {
            $query = JokiPayment::with(['order.client', 'order.service'])
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end]);
            if ($method) {
                $query->where('payment_method', $method);
            }
            foreach ($query->get() as $payment) {
                $rows->push([
                    'source'          => 'joki',
                    'invoice'         => $payment->invoice_number ?? ($payment->order->order_number ?? '-'),
                    'invoice_type'    => 'Joki Code',
                    'paid_at'         => $payment->paid_at,
                    'amount'          => (int) $payment->amount,
                    'original_price'  => (int) $payment->amount,
                    'discount_amount' => 0,
                    'discount_pct'    => 0,
                    'discount_type'   => null,
                    'discount_label'  => null,
                    'method'          => $payment->payment_method ?: 'Manual',
                    'client'          => $payment->order->client->name ?? '-',
                    'client_email'    => $payment->order->client->email ?? '-',
                    'detail'          => $payment->order->service->name ?? ($payment->order->project_name ?? '-'),
                    'plan'            => null,
                ]);
            }
        }

        if (in_array($service, ['all', 'hosting'])) {
            $query = HostingPayment::with(['user', 'project'])
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end]);
            if ($method) {
                $query->where('payment_method', $method);
            }
            foreach ($query->get() as $payment) {
                $paymentMethod = $payment->payment_method ?: 'Manual';
                $notes         = $payment->notes;
                $amountPaid    = (int) $payment->amount;
                $invoiceNum    = $payment->invoice_number ?? '-';

                $invoiceType = null;
                if (str_starts_with($invoiceNum, 'HST-INV-')) {
                    $invoiceType = 'Langganan';
                } elseif (str_starts_with($invoiceNum, 'HST-UPG-')) {
                    $invoiceType = 'Upgrade Storage';
                }

                $originalPrice = $amountPaid;
                if ($notes && in_array($notes, ['starter', 'pro', 'business', 'free'])) {
                    $originalPrice = User::getPlanPrice($notes);
                }

                $discountAmount = max(0, $originalPrice - $amountPaid);
                $discountPct    = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100) : 0;

                $discountType  = null;
                $discountLabel = null;
                if ($paymentMethod === 'Free Plan') {
                    $discountType  = 'free';
                    $discountLabel = 'Free Plan (100%)';
                } elseif ($paymentMethod === 'Voucher' && $amountPaid === 0) {
                    $discountType  = 'voucher';
                    $discountLabel = 'Voucher (Gratis 100%)';
                } elseif ($paymentMethod === 'Voucher' && $discountAmount > 0) {
                    $discountType  = 'voucher';
                    $discountLabel = 'Voucher (-' . $discountPct . '%)';
                } elseif ($paymentMethod === 'Wallet') {
                    $discountType  = 'wallet';
                    $discountLabel = null;
                } elseif ($amountPaid === 0) {
                    $discountType  = 'free';
                    $discountLabel = 'Gratis (100%)';
                }

                $planLabel = null;
                if ($notes && in_array($notes, ['starter', 'pro', 'business', 'free'])) {
                    $planLabel = ucfirst($notes);
                } elseif ($notes) {
                    $planLabel = $notes;
                }

                $rows->push([
                    'source'          => 'hosting',
                    'invoice'         => $invoiceNum,
                    'invoice_type'    => $invoiceType,
                    'paid_at'         => $payment->paid_at,
                    'amount'          => $amountPaid,
                    'original_price'  => $originalPrice,
                    'discount_amount' => $discountAmount,
                    'discount_pct'    => $discountPct,
                    'discount_type'   => $discountType,
                    'discount_label'  => $discountLabel,
                    'method'          => $paymentMethod,
                    'client'          => $payment->user->name ?? '-',
                    'client_email'    => $payment->user->email ?? '-',
                    'detail'          => $planLabel ? 'Paket ' . $planLabel : ($payment->project->project_name ?? '-'),
                    'plan'            => $planLabel,
                ]);
            }
        }

        $rows = $rows->sortByDesc('paid_at')->values();

        $page    = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 25;
        $transactions = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $totalRevenue  = (int) $rows->sum('amount');
        $totalCount    = $rows->count();
        $totalDiscount = (int) $rows->sum('discount_amount');
        $totalGross    = (int) $rows->sum('original_price');

        $jokiRows       = $rows->where('source', 'joki');
        $hostingRows    = $rows->where('source', 'hosting');
        $jokiRevenue    = (int) $jokiRows->sum('amount');
        $jokiCount      = $jokiRows->count();
        $hostingRevenue = (int) $hostingRows->sum('amount');
        $hostingCount   = $hostingRows->count();

        $voucherRows          = $rows->where('discount_type', 'voucher');
        $freeRows             = $rows->where('discount_type', 'free');
        $totalVoucherDiscount = (int) $voucherRows->sum('discount_amount');
        $voucherCount         = $voucherRows->count();
        $freeCount            = $freeRows->count();

        $planBreakdown = $hostingRows
            ->whereNotNull('plan')
            ->groupBy('plan')
            ->map(fn($g) => [
                'count'    => $g->count(),
                'revenue'  => (int) $g->sum('amount'),
                'gross'    => (int) $g->sum('original_price'),
                'discount' => (int) $g->sum('discount_amount'),
            ])
            ->sortByDesc('revenue');

        $methods = $rows->groupBy('method')
            ->map(fn($group) => [
                'total'    => (int) $group->sum('amount'),
                'count'    => $group->count(),
                'discount' => (int) $group->sum('discount_amount'),
            ])
            ->sortByDesc('total');

        $availableMethods = JokiPayment::where('status', 'paid')
            ->whereNotNull('payment_method')
            ->pluck('payment_method')
            ->merge(HostingPayment::where('status', 'paid')->whereNotNull('payment_method')->pluck('payment_method'))
            ->unique()
            ->filter()
            ->values();

        $chartMonths  = [];
        $chartJoki    = [];
        $chartHosting = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::parse($end)->startOfMonth()->subMonths($i);
            $chartMonths[]  = $month->translatedFormat('M Y');
            $chartJoki[]    = (int) JokiPayment::where('status', 'paid')
                ->whereYear('paid_at', $month->year)->whereMonth('paid_at', $month->month)->sum('amount');
            $chartHosting[] = (int) HostingPayment::where('status', 'paid')
                ->whereYear('paid_at', $month->year)->whereMonth('paid_at', $month->month)->sum('amount');
        }

        return view('pages.admin.finance', compact(
            'transactions', 'totalRevenue', 'totalCount', 'totalDiscount', 'totalGross',
            'jokiRevenue', 'jokiCount', 'hostingRevenue', 'hostingCount',
            'voucherCount', 'freeCount', 'totalVoucherDiscount', 'planBreakdown',
            'methods', 'availableMethods', 'chartMonths', 'chartJoki', 'chartHosting',
            'start', 'end', 'service', 'method',
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingPayment;
use App\Models\JokiPayment;
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
                    'source' => 'joki',
                    'invoice' => $payment->invoice_number ?? ($payment->order->order_number ?? '-'),
                    'paid_at' => $payment->paid_at,
                    'amount' => (int) $payment->amount,
                    'method' => $payment->payment_method ?: 'Manual',
                    'client' => $payment->order->client->name ?? '-',
                    'detail' => $payment->order->service->name ?? ($payment->order->project_name ?? '-'),
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
                $notes = $payment->notes;
                $amount = (int) $payment->amount;

                // Tentukan tipe diskon/pembayaran khusus
                $discountType = null;
                if ($paymentMethod === 'Free Plan') {
                    $discountType = 'free';
                } elseif ($paymentMethod === 'Voucher') {
                    $discountType = 'voucher';
                } elseif ($amount === 0 && $paymentMethod !== 'Free Plan') {
                    $discountType = 'free'; // gratis via cara lain
                }

                // Buat label paket yang lebih deskriptif
                $planLabel = null;
                if ($notes && in_array($notes, ['starter', 'pro', 'business', 'free'])) {
                    $planLabel = 'Paket ' . ucfirst($notes);
                } elseif ($notes) {
                    $planLabel = $notes;
                } elseif ($payment->project) {
                    $planLabel = $payment->project->project_name ?? '-';
                }

                // Prefix invoice untuk tipe transaksi
                $invoiceNum = $payment->invoice_number ?? '-';
                $invoiceType = null;
                if (str_starts_with($invoiceNum, 'HST-INV-')) {
                    $invoiceType = 'Langganan';
                } elseif (str_starts_with($invoiceNum, 'HST-UPG-')) {
                    $invoiceType = 'Upgrade Storage';
                }

                $rows->push([
                    'source'        => 'hosting',
                    'invoice'       => $invoiceNum,
                    'invoice_type'  => $invoiceType,
                    'paid_at'       => $payment->paid_at,
                    'amount'        => $amount,
                    'method'        => $paymentMethod,
                    'client'        => $payment->user->name ?? '-',
                    'detail'        => $planLabel ?? '-',
                    'discount_type' => $discountType,
                ]);
            }
        }

        $rows = $rows->sortByDesc('paid_at')->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 25;
        $transactions = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $totalRevenue = (int) $rows->sum('amount');
        $totalCount = $rows->count();

        $jokiRows = $rows->where('source', 'joki');
        $hostingRows = $rows->where('source', 'hosting');
        $jokiRevenue = (int) $jokiRows->sum('amount');
        $jokiCount = $jokiRows->count();
        $hostingRevenue = (int) $hostingRows->sum('amount');
        $hostingCount = $hostingRows->count();

        $methods = $rows->groupBy('method')
            ->map(fn ($group) => ['total' => (int) $group->sum('amount'), 'count' => $group->count()])
            ->sortByDesc('total');

        $availableMethods = JokiPayment::where('status', 'paid')
            ->whereNotNull('payment_method')
            ->pluck('payment_method')
            ->merge(HostingPayment::where('status', 'paid')->whereNotNull('payment_method')->pluck('payment_method'))
            ->unique()
            ->filter()
            ->values();

        $chartMonths = [];
        $chartJoki = [];
        $chartHosting = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::parse($end)->startOfMonth()->subMonths($i);
            $chartMonths[] = $month->translatedFormat('M Y');
            $chartJoki[] = (int) JokiPayment::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('amount');
            $chartHosting[] = (int) HostingPayment::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('amount');
        }

        return view('pages.admin.finance', compact(
            'transactions',
            'totalRevenue',
            'totalCount',
            'jokiRevenue',
            'jokiCount',
            'hostingRevenue',
            'hostingCount',
            'methods',
            'availableMethods',
            'chartMonths',
            'chartJoki',
            'chartHosting',
            'start',
            'end',
            'service',
            'method',
        ));
    }
}

<?php

namespace App\Http\Controllers\Hosting\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(15);
        return view('pages.hosting.admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('pages.hosting.admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:255',
            'discount_type' => 'required|in:amount,percentage',
            'discount_value' => 'required|integer|min:1',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $voucher = new Voucher();
        $voucher->code = strtoupper(trim($request->code));
        
        if ($request->discount_type === 'amount') {
            $voucher->discount_amount = $request->discount_value;
            $voucher->discount_percentage = null;
        } else {
            $voucher->discount_amount = null;
            $voucher->discount_percentage = min(100, $request->discount_value);
        }
        
        $voucher->max_uses = $request->max_uses;
        $voucher->expires_at = $request->expires_at;
        $voucher->is_active = $request->has('is_active');
        
        $voucher->save();

        return redirect()->route('admin_hosting.vouchers.index')->with('success', 'Voucher berhasil dibuat!');
    }

    public function edit($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $voucher = Voucher::findOrFail($decoded[0]);
        return view('pages.hosting.admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, $hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $voucher = Voucher::findOrFail($decoded[0]);

        $request->validate([
            'code' => 'required|string|max:255|unique:vouchers,code,' . $voucher->id,
            'discount_type' => 'required|in:amount,percentage',
            'discount_value' => 'required|integer|min:1',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $voucher->code = strtoupper(trim($request->code));
        
        if ($request->discount_type === 'amount') {
            $voucher->discount_amount = $request->discount_value;
            $voucher->discount_percentage = null;
        } else {
            $voucher->discount_amount = null;
            $voucher->discount_percentage = min(100, $request->discount_value);
        }
        
        $voucher->max_uses = $request->max_uses;
        $voucher->expires_at = $request->expires_at;
        $voucher->is_active = $request->has('is_active');
        
        $voucher->save();

        return redirect()->route('admin_hosting.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    public function destroy($hashid)
    {
        $decoded = \Vinkla\Hashids\Facades\Hashids::decode($hashid);
        if (empty($decoded)) abort(404);
        
        $voucher = Voucher::findOrFail($decoded[0]);
        $voucher->delete();
        
        return redirect()->route('admin_hosting.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}

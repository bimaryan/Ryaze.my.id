@extends('index')

@section('content')
    <x-ui.page-layout>
        <x-ui.page-header title="Edit Voucher: {{ $voucher->code }}" icon="ticket" iconColor="indigo">
            <x-slot:actions>
                <a href="{{ route('admin_hosting.vouchers.index') }}" class="inline-flex items-center justify-center bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                    Kembali
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl mt-4">
            <form action="{{ route('admin_hosting.vouchers.update', $voucher->hashid) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Kode Voucher <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $voucher->code) }}" required class="w-full uppercase px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all font-mono">
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Tipe Diskon <span class="text-rose-500">*</span></label>
                        <select name="discount_type" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                            <option value="amount" {{ old('discount_type', $voucher->discount_amount ? 'amount' : 'percentage') == 'amount' ? 'selected' : '' }}>Nominal (Rp)</option>
                            <option value="percentage" {{ old('discount_type', $voucher->discount_percentage ? 'percentage' : 'amount') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                        @error('discount_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Nilai Diskon <span class="text-rose-500">*</span></label>
                        <input type="number" name="discount_value" value="{{ old('discount_value', $voucher->discount_amount ?? $voucher->discount_percentage) }}" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                        @error('discount_value') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Batas Penggunaan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="number" name="max_uses" value="{{ old('max_uses', $voucher->max_uses) }}" placeholder="Kosongkan untuk tanpa batas" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                        @error('max_uses') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Berlaku Sampai <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm transition-all">
                        @error('expires_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-slate-700">Voucher Aktif</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-lg transition-colors shadow-sm shadow-indigo-200">
                        Perbarui Voucher
                    </button>
                </div>
            </form>
        </div>
    </x-ui.page-layout>
@endsection

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AffiliateCommission;

class AffiliateController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Generate referral_code for existing users if missing
        if (empty($user->referral_code)) {
            $user->referral_code = \Illuminate\Support\Str::random(8);
            $user->save();
        }
        
        // Count total referrals
        $totalReferrals = $user->referrals()->count();
        
        // Sum total commission (paid)
        $totalCommission = $user->affiliateCommissions()->where('status', 'paid')->sum('amount');
        
        // Get commission history
        $commissions = $user->affiliateCommissions()->latest()->paginate(15);
        
        return view('pages.hosting.user.affiliate_dashboard', compact('user', 'totalReferrals', 'totalCommission', 'commissions'));
    }
}

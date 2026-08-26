<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletWithdrawal;
use App\Models\WalletTransaction;

class WalletTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_withdraw_more_than_balance(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 10000]);

        $response = $this->actingAs($user)->post(route('user.wallet.withdraw.process'), [
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
        ]);

        $response->assertSessionHas('error', 'Saldo Anda tidak mencukupi untuk melakukan penarikan ini.');
        
        $this->assertEquals(10000, $wallet->fresh()->balance);
        $this->assertDatabaseCount('wallet_withdrawals', 0);
    }

    public function test_user_can_withdraw_with_sufficient_balance(): void
    {
        $user = User::factory()->create(['role' => 'user_hosting']);
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 100000]);

        $response = $this->actingAs($user)->post(route('user.wallet.withdraw.process'), [
            'amount' => 50000,
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'John Doe',
        ]);

        $response->assertRedirect(route('user.wallet.history'));
        $response->assertSessionHas('success');
        
        $this->assertEquals(50000, $wallet->fresh()->balance);
        $this->assertDatabaseHas('wallet_withdrawals', [
            'user_id' => $user->id,
            'amount' => 50000,
            'bank_name' => 'BCA',
        ]);
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $wallet->id,
            'amount' => 50000,
            'type' => 'debit',
            'status' => 'pending',
        ]);
    }
}

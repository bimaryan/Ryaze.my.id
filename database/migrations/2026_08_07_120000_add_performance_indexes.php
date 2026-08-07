<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexing untuk query yang paling sering dijalankan.
     */
    public function up(): void
    {
        // 1 wallet per user (mencegah duplikasi wallet bentrok)
        Schema::table('wallets', function (Blueprint $table) {
            if (! $this->hasIndex('wallets', 'wallets_user_id_unique')) {
                $table->unique('user_id', 'wallets_user_id_unique');
            }
        });

        // Riwayat transaksi wallet paling sering difilter per wallet + status
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! $this->hasIndex('wallet_transactions', 'wallet_transactions_wallet_id_status_index')) {
                $table->index(['wallet_id', 'status'], 'wallet_transactions_wallet_id_status_index');
            }
        });

        // Joki payment: filter invoice (sudah unique) + status/paid_at untuk laporan
        Schema::table('joki_payments', function (Blueprint $table) {
            if (! $this->hasIndex('joki_payments', 'joki_payments_status_paid_at_index')) {
                $table->index(['status', 'paid_at'], 'joki_payments_status_paid_at_index');
            }
        });

        // Hosting payment: filter invoice + status (dashboard & laporan keuangan)
        Schema::table('hosting_payments', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_payments', 'hosting_payments_status_paid_at_index')) {
                $table->index(['status', 'paid_at'], 'hosting_payments_status_paid_at_index');
            }
        });

        // Project: filter per user (dashboard user)
        Schema::table('hosting_projects', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_projects', 'hosting_projects_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'hosting_projects_user_id_status_index');
            }
        });

        // Team membership: lookup user → project (getValidProject hot path)
        Schema::table('hosting_project_users', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_project_users', 'hosting_project_users_user_id_project_id_index')) {
                $table->index(['user_id', 'project_id'], 'hosting_project_users_user_id_project_id_index');
            }
        });

        // Deployment: project → deployment terbaru (polling build log)
        Schema::table('hosting_deployments', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_deployments', 'hosting_deployments_project_id_created_at_index')) {
                $table->index(['project_id', 'created_at'], 'hosting_deployments_project_id_created_at_index');
            }
        });

        // Billing: user + status + due date (cron suspend & cek langganan)
        Schema::table('hosting_billings', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_billings', 'hosting_billings_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'hosting_billings_user_id_status_index');
            }
            if (! $this->hasIndex('hosting_billings', 'hosting_billings_next_due_date_index')) {
                $table->index('next_due_date', 'hosting_billings_next_due_date_index');
            }
        });

        // Cron: filter project aktif (routes/console.php)
        Schema::table('hosting_crons', function (Blueprint $table) {
            if (! $this->hasIndex('hosting_crons', 'hosting_crons_project_id_is_active_index')) {
                $table->index(['project_id', 'is_active'], 'hosting_crons_project_id_is_active_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique('wallets_user_id_unique');
        });
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_wallet_id_status_index');
        });
        Schema::table('joki_payments', function (Blueprint $table) {
            $table->dropIndex('joki_payments_status_paid_at_index');
        });
        Schema::table('hosting_payments', function (Blueprint $table) {
            $table->dropIndex('hosting_payments_status_paid_at_index');
        });
        Schema::table('hosting_projects', function (Blueprint $table) {
            $table->dropIndex('hosting_projects_user_id_status_index');
        });
        Schema::table('hosting_project_users', function (Blueprint $table) {
            $table->dropIndex('hosting_project_users_user_id_project_id_index');
        });
        Schema::table('hosting_deployments', function (Blueprint $table) {
            $table->dropIndex('hosting_deployments_project_id_created_at_index');
        });
        Schema::table('hosting_billings', function (Blueprint $table) {
            $table->dropIndex('hosting_billings_user_id_status_index');
            $table->dropIndex('hosting_billings_next_due_date_index');
        });
        Schema::table('hosting_crons', function (Blueprint $table) {
            $table->dropIndex('hosting_crons_project_id_is_active_index');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        return Schema::getIndexes($table) !== [] && collect(Schema::getIndexes($table))->contains('name', $index);
    }
};

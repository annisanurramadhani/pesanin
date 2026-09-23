<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::table('withdrawals')
            ->where('status', 'approved')
            ->update([
                'status' => 'paid',
            ]);
 
        // SQLite does not support MODIFY ENUM; it does not enforce Laravel's
        // enum declaration either, so the data update above is sufficient for
        // the in-memory test database.
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE withdrawals
                MODIFY status ENUM(
                    'pending',
                    'processing',
                    'paid',
                    'failed',
                    'rejected'
                )
                DEFAULT 'pending'
            ");
        }

    }

    public function down(): void
    {

        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement("
                ALTER TABLE withdrawals
                MODIFY status ENUM(
                    'pending',
                    'approved',
                    'rejected'
                )
                DEFAULT 'pending'
            ");
        }

    }
};

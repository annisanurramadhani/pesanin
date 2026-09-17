<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {


        DB::table('withdrawals')
            ->where('status','approved')
            ->update([
                'status'=>'paid'
            ]);



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



    public function down(): void
    {

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

};
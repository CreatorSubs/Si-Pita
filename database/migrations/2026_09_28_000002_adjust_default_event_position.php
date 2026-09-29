<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('certificates')
            ->where('pos_event_x', 250)
            ->where('pos_event_y', 300)
            ->update(['pos_event_y' => 258]);
    }

    public function down(): void
    {
        DB::table('certificates')
            ->where('pos_event_x', 250)
            ->where('pos_event_y', 258)
            ->update(['pos_event_y' => 300]);
    }
};
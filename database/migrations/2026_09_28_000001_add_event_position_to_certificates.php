<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->unsignedSmallInteger('pos_event_x')->default(250);
            $table->unsignedSmallInteger('pos_event_y')->default(258);
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['pos_event_x', 'pos_event_y']);
        });
    }
};
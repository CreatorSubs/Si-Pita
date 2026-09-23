<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->string('recipient_name');
            $table->string('recipient_identity');
            $table->string('institution')->nullable();
            $table->string('event_name');
            $table->string('role')->default('Peserta');
            $table->date('issue_date');
            $table->string('template_path')->nullable();
            $table->integer('pos_name_x')->default(200);
            $table->integer('pos_name_y')->default(150);
            $table->integer('pos_number_x')->default(200);
            $table->integer('pos_number_y')->default(100);
            $table->integer('pos_qr_x')->default(50);
            $table->integer('pos_qr_y')->default(300);
            $table->string('qr_token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

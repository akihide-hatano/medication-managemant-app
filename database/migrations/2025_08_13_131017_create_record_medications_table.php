<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('record_medications', function (Blueprint $table) {
            $table->id('record_medication_id'); // 主キー（規約に寄せるなら id でもOK）
            $table->foreignId('record_id')
                ->constrained('records', 'record_id')
                ->cascadeOnDelete();
            $table->foreignId('medication_id')
                ->constrained('medications', 'medication_id')
                ->cascadeOnDelete();

            $table->string('taken_dosage')->nullable();   // 例: "1錠" / "100mg"
            $table->boolean('is_completed')->default(false);
            $table->text('reason_not_taken')->nullable(); // 未服用の理由（自由記述）

            $table->timestamps();

            // 1レコード中に同じ薬を重複登録させないならユニーク制約を付ける
            $table->unique(['record_id', 'medication_id']);
            $table->index(['record_id', 'is_completed']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_medications');
    }
};

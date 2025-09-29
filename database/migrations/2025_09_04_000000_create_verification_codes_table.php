<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id()->comment(__('verification_code.migration.id'));
            $table->string('receiver', 100)->comment(__('verification_code.migration.receiver'));
            $table->string('purpose', 100)->comment(__('verification_code.migration.purpose'));
            $table->unsignedBigInteger('user_id')->nullable()->comment(__('verification_code.migration.user_id'));
            $table->string('code', 20)->comment(__('verification_code.migration.code'));
            $table->timestamp('expires_at')->comment(__('verification_code.migration.expires_at'));
            $table->unsignedTinyInteger('attempts')->default(0)->comment(__('verification_code.migration.attempts'));
            $table->boolean('is_verified')->default(false)->comment(__('verification_code.migration.is_verified'));
            $table->timestamp('verified_at')->nullable()->comment(__('verification_code.migration.verified_at'));
            $table->boolean('is_revoked')->default(false)->comment(__('verification_code.migration.is_revoked'));
            $table->timestamp('revoked_at')->nullable()->comment(__('verification_code.migration.revoked_at'));
            $table->timestamps();
        });

        Schema::table('verification_codes', function (Blueprint $table) {
            $table->indeX(['receiver', 'purpose'], 'idx_receiver_purpose')->comment(__('verification_code.migration.idx_receiver_purpose'));
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};

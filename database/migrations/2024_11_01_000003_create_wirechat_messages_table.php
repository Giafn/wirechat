<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Namu\WireChat\Facades\WireChat;
use Namu\WireChat\Models\Conversation;
use Namu\WireChat\Models\Message;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $usesUuid = WireChat::usesUuid();
        Schema::create((new Message)->getTable(), function (Blueprint $table) use ($usesUuid) {
            if ($usesUuid) {
                $table->uuid('id')->primary();
                $table->uuid('conversation_id');
                $table->uuid('reply_id')->nullable();
            } else {
                $table->id();
                $table->unsignedBigInteger('conversation_id');
                $table->unsignedBigInteger('reply_id')->nullable();
            }
            $table->foreign('conversation_id')->references('id')->on((new Conversation)->getTable())->cascadeOnDelete();

            $table->unsignedBigInteger('sendable_id');
            $table->string('sendable_type');

            $table->text('body')->nullable();
            $table->string('type')->default('text');

            $table->timestamp('kept_at')->nullable()->comment('filled when a message is kept from disappearing');

            $table->softDeletes();
            $table->timestamps();

            // Indexes for optimization
            $table->index(['conversation_id']);
            $table->index(['sendable_id', 'sendable_type']);
        });

        Schema::table('wire_messages', function (Blueprint $table) {
            $table->foreign('reply_id')
                ->references('id')
                ->on('wire_messages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Message)->getTable());
    }
};

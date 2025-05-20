<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Namu\WireChat\Facades\WireChat;
use Namu\WireChat\Models\Attachment;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $usesUuid = WireChat::usesUuid();
        Schema::create((new Attachment)->getTable(), function (Blueprint $table) use ($usesUuid) {
            if ($usesUuid) {
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->uuid('attachable_id');
            } else {
                $table->id();
                $table->unsignedBigInteger('attachable_id');
            }
            $table->string('attachable_type');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('url');
            $table->string('mime_type');
            $table->timestamps();
            $table->index(['attachable_id', 'attachable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists((new Attachment)->getTable());
    }
};

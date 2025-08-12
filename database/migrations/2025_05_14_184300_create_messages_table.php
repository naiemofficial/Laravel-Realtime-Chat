<?php

use App\Models\Participant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Conversation;
use \App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Conversation::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Participant::class)->nullable()->constrained()->nullOnDelete();
            $table->text('text');
            $table->enum('type', ['regular', 'starter', 'individual'])->default('regular');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

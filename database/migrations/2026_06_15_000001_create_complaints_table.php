<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $t) {
            $t->id();
            // FK to users — nullable so guests can't submit but we don't
            // break if the user account is later deleted.
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('subject', 255);          // short subject line
            $t->text('message');                  // full complaint body
            $t->enum('status', ['new', 'in_review', 'resolved', 'closed'])->default('new');
            $t->text('admin_notes')->nullable();  // internal admin response / notes
            $t->timestamps();

            $t->index(['user_id', 'created_at']);
            $t->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
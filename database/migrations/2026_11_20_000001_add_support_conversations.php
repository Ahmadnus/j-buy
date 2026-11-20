<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turns one-way complaints into support conversations.
 *
 *  - complaints.contact_name / contact_phone: lets guests (no account) reach
 *    support; for signed-in users the account's name/phone are used instead.
 *  - complaints.admin_unread / customer_unread: drive the dashboard badge and
 *    the "new reply" dot in the app.
 *  - complaint_messages: the thread itself (customer ⇄ support).
 *
 * Existing complaints are kept: each one's original message becomes the first
 * message of its thread.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $t) {
            $t->string('contact_name', 255)->nullable()->after('user_id');
            $t->string('contact_phone', 32)->nullable()->after('contact_name');
            $t->boolean('admin_unread')->default(true)->after('status');
            $t->boolean('customer_unread')->default(false)->after('admin_unread');
            $t->timestamp('last_message_at')->nullable()->after('customer_unread');

            $t->index(['admin_unread', 'last_message_at']);
        });

        Schema::create('complaint_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $t->enum('sender', ['customer', 'admin']);
            // Which admin replied — kept for accountability, nullable so
            // deleting a staff account doesn't delete the conversation.
            $t->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $t->text('body');
            $t->timestamps();

            $t->index(['complaint_id', 'created_at']);
        });

        // Backfill: each existing complaint's message becomes its first thread
        // entry. Old complaints were reviewed through the old screen, so they
        // are not flagged unread.
        $now = now();
        foreach (DB::table('complaints')->orderBy('id')->cursor() as $c) {
            DB::table('complaint_messages')->insert([
                'complaint_id' => $c->id,
                'sender'       => 'customer',
                'body'         => $c->message,
                'created_at'   => $c->created_at ?? $now,
                'updated_at'   => $c->created_at ?? $now,
            ]);
            DB::table('complaints')->where('id', $c->id)->update([
                'admin_unread'    => $c->status === 'new',
                'last_message_at' => $c->created_at ?? $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_messages');
        Schema::table('complaints', function (Blueprint $t) {
            $t->dropIndex(['admin_unread', 'last_message_at']);
            $t->dropColumn(['contact_name', 'contact_phone', 'admin_unread', 'customer_unread', 'last_message_at']);
        });
    }
};

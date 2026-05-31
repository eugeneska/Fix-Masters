<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('conversion_id')->nullable()->unique()->after('id');
            $table->text('admin_note')->nullable()->after('comment');
            $table->string('form_url')->nullable()->after('yclid');
            $table->string('first_contact_url')->nullable()->after('form_url');
            $table->string('last_click')->nullable()->after('first_contact_url');
            $table->string('referrer')->nullable()->after('last_click');
            $table->string('ym_client_id')->nullable()->after('referrer');
            $table->string('ga_client_id')->nullable()->after('ym_client_id');
            $table->string('messenger')->nullable()->after('ga_client_id');
            $table->boolean('telegram_sent')->default(false)->after('messenger');
            $table->boolean('ga4_conversion_sent')->default(false)->after('conversion_sent');
            $table->boolean('metrika_conversion_sent')->default(false)->after('ga4_conversion_sent');
            $table->timestamp('conversion_sent_at')->nullable()->after('metrika_conversion_sent');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'conversion_id',
                'admin_note',
                'form_url',
                'first_contact_url',
                'last_click',
                'referrer',
                'ym_client_id',
                'ga_client_id',
                'messenger',
                'telegram_sent',
                'ga4_conversion_sent',
                'metrika_conversion_sent',
                'conversion_sent_at',
            ]);
        });
    }
};

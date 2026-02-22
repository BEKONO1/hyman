<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsertypeNotificationTemplateContentMappingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('notification_template_content_mapping', 'user_type')) {
            Schema::table('notification_template_content_mapping', function (Blueprint $table) {
                $table->string('user_type')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_template_content_mapping', function (Blueprint $table) {

        });
    }
}

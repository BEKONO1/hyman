<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Constant;
use App\Models\MailTemplates;

class AlterMailTemplateTextChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $provider_payout = DB::table('mail_templates')
        ->where('type', 'provider_payout')
        ->first();
    
    if ($provider_payout) {
        // Update the 'to' field for the provider payout template
        DB::table('mail_templates')
            ->where('type', 'provider_payout')
            ->update(['to' => '["provider","admin"]']);
    
        // Proceed to update the content mappings
        DB::table('mail_template_content_mappings')
            ->where('template_id', $provider_payout->id)
            ->where('user_type', 'provider')
            ->update([
                'user_type' => 'provider',
                'subject' => 'Payout Received',
                'template_detail' => '<p>Hello [[ provider_name ]],</p>
                           <p>Your payout of [[ amount ]] has been received.</p>
                           <p>&nbsp;</p>
                           <p>Best regards,<br />[[ company_name ]]</p>',
            ]);
    
        DB::table('mail_template_content_mappings')
            ->where('template_id', $provider_payout->id)
            ->where('user_type', 'handyman')
            ->update([
                'user_type' => 'admin',
                'subject' => 'Payout Processed',
                'template_detail' => '<p>Hello [[ admin_name ]],</p>
                           <p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>
                           <p>&nbsp;</p>
                           <p>Best regards,<br />[[ company_name ]]</p>',
            ]);
    }



        $template = MailTemplates::create([
            'type' => 'handyman_payout',
            'name' => 'handyman_payout',
            'label' => 'Payout Update',
            'status' => 1,
            'to' => '["provider","handyman"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Processed',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            //
        });
    }
}

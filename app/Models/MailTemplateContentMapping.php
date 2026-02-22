<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class MailTemplateContentMapping extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mail_template_content_mappings';

    protected $fillable = [
        'template_id',
        'template_detail',
        'notification_message',
        'notification_link',
        'language',
        'subject',
        'status',
        'user_type'
    ];

    public function template()
    {
        return $this->belongsTo(MailTemplates::class, 'template_id', 'id');
    }
}

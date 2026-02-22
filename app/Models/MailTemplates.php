<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Constant;
class MailTemplates extends BaseModel
{
    use HasFactory;
    use SoftDeletes;
    const CUSTOM_FIELD_MODEL = 'App\Models\MailTemplate';

    protected $table = 'mail_templates';

    protected $fillable = [
        'name',
        'label',
        'description',
        'type',
        'status',
        'to',
        'bcc',
        'cc',
        'channels',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    public function defaultMailTemplateMap()
    {
        return $this->hasOne(MailTemplateContentMapping::class, 'template_id', 'id')->where('language', 'en');
    }

    public function constant()
    {
        return $this->belongsTo(Constant::class, 'type', 'value')->where('type', 'notification_type');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}

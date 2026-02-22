<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class HelpDesk extends Model implements  HasMedia
{
    use InteractsWithMedia,HasFactory,SoftDeletes;
    protected $table = 'help_desk';
    protected $fillable = [
    'subject','employee_id','email','contact_number','mode','description','status'
    ];
    protected $casts = [
        'employee_id'               => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($row) {
            
            if($row->forceDeleting === true)
            {
               
            }
        });

        static::restoring(function($row) {
           
        });
    }

    public function users(){
        return $this->belongsTo('App\Models\User','employee_id','id');
    }
    public function helpdeskactivity(){
        return $this->hasMany('App\Models\HelpDeskActivityMapping','helpdesk_id','id');
    }
}

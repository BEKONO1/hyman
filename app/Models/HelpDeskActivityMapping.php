<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class HelpDeskActivityMapping extends Model implements  HasMedia
{
    use InteractsWithMedia,HasFactory;
    protected $table = 'help_desk_activity_mapping';
    protected $fillable = [ 'helpdesk_id', 'sender_id', 'receiver_id', 'messages','activity_type','activity_message'];

    protected $casts = [
        'helpdesk_id'    => 'integer',
        'sender_id'      => 'integer',
        'receiver_id'  => 'integer',
    ];

    public function receiver(){
        return $this->belongsTo(User::class,'receiver_id', 'id')->withTrashed();
    }
    public function sender(){
        return $this->belongsTo(User::class,'sender_id', 'id')->withTrashed();
    }
    public function HelpDesk(){
        return $this->belongsTo(HelpDesk::class,'helpdesk_id', 'id')->withTrashed();
    }
}

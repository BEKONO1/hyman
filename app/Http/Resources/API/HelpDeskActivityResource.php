<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class HelpDeskActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'helpdesk_id'       => $this->helpdesk_id,
            'sender_id'         => $this->sender_id,
            'sender_name'       => optional($this->sender)->display_name ?? '',
            'sender_image'      => getSingleMedia(optional($this->sender), 'profile_image',null),
            'receiver_id'       => $this->receiver_id,
            'recevier_name'     => optional($this->receiver)->display_name ?? '',
            'recevier_image'    => getSingleMedia(optional($this->recevier), 'profile_image',null),
            'messages'          => $this->messages,
            'activity_type'     => $this->activity_type,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'attachments'        => getAttachments($this->getMedia('helpdesk_activity_attachment')),
            'attachments_array'  => getAttachmentArray($this->getMedia('helpdesk_activity_attachment'),null),
            'helpdesk_attachments'        => getAttachments($this->HelpDesk->getMedia('helpdesk_attachment')),
            'helpdesk_attachments_array'  => getAttachmentArray($this->HelpDesk->getMedia('helpdesk_attachment'),null),
        ];
    }
}

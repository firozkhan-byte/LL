<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CrmCampaign extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'channel', // email, sms, whatsapp
        'subject',
        'content',
        'status', // draft, sent
        'sent_count',
    ];
}

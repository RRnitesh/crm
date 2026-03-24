<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'customer_name',
        'subject',
        'description',
        'status',
        'priority',
        'sla_breached',
        'responded_at',
        'created_by'
    ];
}

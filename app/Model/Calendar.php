<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = "calendar";

    protected $hidden = [
        'user_id'
    ];

    public function chain()
    {
        return $this->belongsTo('App\Model\Chain');
    }
}

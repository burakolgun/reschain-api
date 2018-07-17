<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Chain extends Model
{
    protected $table = "chain";

    protected $fillable = ['start_date', 'end_date', 'note', 'name'];

    public function calendar()
    {
        return $this->hasMany('App\Model\Calendar');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}

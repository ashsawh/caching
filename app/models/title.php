<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    public $timestamps = false;
    protected $primaryKey = "emp_no";
    protected $dates = [
        'from_date',
        'to_date'
    ];

    protected $fillable = [
        'title',
        'from_date',
        'to_date'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Model\Employee', 'emp_no');
    }
}
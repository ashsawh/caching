<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dept_Emp extends Model
{
    public $timestamps = false;
    public $table = "dept_emp";
    
    protected $primaryKey = "emp_no";
    protected $dates = [
        'from_date',
        'to_date'
    ];

    protected $fillable = [
        'dept_no',
        'from_date',
        'to_date'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Model\Employee', 'emp_no');
    }
}
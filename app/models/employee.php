<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $timestamps = false;
    protected $primaryKey = "emp_no";
    protected $dates = [
        'hire_date',
        'birth_date'
    ];

    protected $fillable = [
        'birth_date',
        'first_name',
        'last_name',
        'gender',
        'hire_date'
    ];

    public function salaries()
    {
        return $this->hasMany('App\Models\Salary', 'emp_no');
    }

    public function titles()
    {
        return $this->hasMany('App\Models\Title', 'emp_no');
    }

    public function departments()
    {
        return $this->hasMany('App\Models\Dept_Emp', 'emp_no');
    }
}
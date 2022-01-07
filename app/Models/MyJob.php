<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\SoftDeletes;


class MyJob extends BaseModel
{
    use HasFactory, SoftDeletes;


    public function questions()
    {
        return $this->hasMany(MyJobQuestion::class, 'my_job_id');
    }

}

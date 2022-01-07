<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyJob extends BaseModel
{
    use HasFactory;


    public function answered()
    {
        return $this->hasMany(ApplyJobQuestion::class, 'apply_job_id');
    }


}

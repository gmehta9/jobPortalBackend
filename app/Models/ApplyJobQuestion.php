<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplyJobQuestion extends BaseModel
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(MyJobQuestion::class, 'my_job_question_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTopic extends Model
{
    use HasFactory;

    protected $table = 'course_topics';
    protected $guarded = [];

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class, 'topic_id')->orderBy('sort_order')->orderBy('id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

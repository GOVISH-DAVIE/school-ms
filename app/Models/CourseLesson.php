<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;

    protected $table = 'course_lessons';
    protected $guarded = [];

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'lesson_id')->orderBy('id');
    }

    public function topic()
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

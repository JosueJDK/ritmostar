<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    protected $fillable=['user_id','course_id','rate','review','status'];

    public function user_info(){
        return $this->hasOne('App\User','id','user_id');
    }

    public static function getAllReview(){
        return CourseReview::with('user_info')->paginate(10);
    }
    public static function getAllUserReview(){
        return CourseReview::where('user_id',auth()->user()->id)->with('user_info')->paginate(10);
    }

    public function course(){
        return $this->hasOne(Course::class,'id','course_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;

class Course extends Model
{

    protected $fillable=['title','slug','summary','description','cat_id','child_cat_id','price','discount','status','photo','vacantes','is_featured','condition'];

    public function cat_info(){
        return $this->hasOne('App\Models\Category','id','cat_id');
    }
    public function sub_cat_info(){
        return $this->hasOne('App\Models\Category','id','child_cat_id');
    }
    public static function getAllCourse(){
        return Course::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(10);
    }
    public function rel_cours(){
        return $this->hasMany('App\Models\Course','cat_id','cat_id')->where('status','active')->orderBy('id','DESC')->limit(8);
    }
    public function getReview(){
        return $this->hasMany('App\Models\CourseReview','course_id','id')->with('user_info')->where('status','active')->orderBy('id','DESC');
    }
    public static function getCourseBySlug($slug){
        return Course::with(['cat_info','rel_cours','getReview'])->where('slug',$slug)->first();
    }
    public static function countActiveCourse(){
        $data=Course::where('status','active')->count();
        if($data){
            return $data;
        }
        return 0;
    }

    public function carts(){
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class)->whereNotNull('cart_id');
    }
}

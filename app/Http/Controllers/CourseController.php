<?php

namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\Category;
// use App\Models\PostTag;
// use App\Models\PostCategory;
// use App\Models\Post;
// use App\Models\Cart;
// use App\Models\Brand;
// use App\Models\User;
// use Auth;
// use Session;
// use Newsletter;
// use DB;
// use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Course::getAllCourse();
        return view('backend.course.index')->with('courses',$courses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category=Category::where('is_parent',1)->get();
        // return $category;
        return view('backend.course.create')->with('categories',$category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo' => 'required|mimes:jpg,jpeg,png',
            'vacantes'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric'
        ]);

        $data=$request->all();
        $slug=Str::slug($request->title);
        $count=Course::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;
        $data['is_featured']=$request->input('is_featured',0);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $img_name = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $request->photo->move(public_path('upload'), $img_name);
            $img_url = '/upload/'.$img_name;
            $data['photo'] = $img_url;
        }

        $status=Course::create($data);
        if($status){
            request()->session()->flash('success','Curso creado satisfactoriamente!');
        }
        else{
            request()->session()->flash('error','Error al crear el curso!!');
        }
        return redirect()->route('course.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course=Course::findOrFail($id);
        $category=Category::where('is_parent',1)->get();
        $items=Course::where('id',$id)->get();
        // return $items;
        return view('backend.course.edit')->with('course',$course)
                    ->with('categories',$category)->with('items',$items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course=Course::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required',
            'summary'=>'string|required',
            'description'=>'string|nullable',
            'photo' => 'mimes:jpeg,png','string|required',
            'vacantes'=>"required|numeric",
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'is_featured'=>'sometimes|in:1',
            'status'=>'required|in:active,inactive',
            'condition'=>'required|in:default,new',
            'price'=>'required|numeric',
            'discount'=>'nullable|numeric'
        ]);

        $data=$request->all();
        $data['is_featured']=$request->input('is_featured',0);

        if((strcmp($request->title, $course->title) !== 0))
        {
            $slug=Str::slug($request->title);
            $count=Course::where('slug',$slug)->count();
            if($count>0){
                $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
            }
            $data['slug']=$slug;
        }

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $img_name = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $request->photo->move(public_path('upload'), $img_name);
            $img_url = '/upload/'.$img_name;
            $data['photo'] = $img_url;
        }

        $status=$course->fill($data)->save();
        if($status){
            request()->session()->flash('success','Curso actualizado satisfactoriamente');
        }
        else{
            request()->session()->flash('error','Error al actualizar el curso!!');
        }
        return redirect()->route('course.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course=Course::findOrFail($id);
        $status=$course->delete();

        if($status){
            request()->session()->flash('success','Curso elimando satisfactoriamente!');
        }
        else{
            request()->session()->flash('error','Error al eliminar el curso!');
        }
        return redirect()->route('course.index');
    }

    public function courseDetail($slug){
        $course_detail= Course::getCourseBySlug($slug);
        return view('frontend.pages.course_detail')->with('course_detail',$course_detail);
    }

    public function courseGrids(){
        $courses=Course::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            $courses->whereIn('cat_id',$cat_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $courses=$courses->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $courses=$courses->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);

            $courses->whereBetween('price',$price);
        }

        $recent_courses=Course::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $courses=$courses->where('status','active')->paginate($_GET['show']);
        }
        else{
            $courses=$courses->where('status','active')->paginate(9);
        }
        // Sort by name , price, category


        return view('frontend.pages.course-grids')->with('courses',$courses)->with('recent_courses',$recent_courses);
    }

    public function courseLists(){
        $courses=Course::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            $courses->whereIn('cat_id',$cat_ids)->paginate;
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $courses=$courses->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $courses=$courses->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);

            $courses->whereBetween('price',$price);
        }

        $recent_courses=Course::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $courses=$courses->where('status','active')->paginate($_GET['show']);
        }
        else{
            $courses=$courses->where('status','active')->paginate(6);
        }
        // Sort by name , price, category


        return view('frontend.pages.course-lists')->with('courses',$courses)->with('recent_courses',$recent_courses);
    }

    public function productFilter(Request $request){
            $data= $request->all();
            // return $data;
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }

    public function productSearch(Request $request){
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $products=Product::orwhere('title','like','%'.$request->search.'%')
                    ->orwhere('slug','like','%'.$request->search.'%')
                    ->orwhere('description','like','%'.$request->search.'%')
                    ->orwhere('summary','like','%'.$request->search.'%')
                    ->orwhere('price','like','%'.$request->search.'%')
                    ->orderBy('id','DESC')
                    ->paginate('9');
        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }

    // public function productBrand(Request $request){
    //     $products=Brand::getProductByBrand($request->slug);
    //     $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
    //     if(request()->is('e-shop.loc/product-grids')){
    //         return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
    //     }
    //     else{
    //         return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
    //     }

    // }


    public function courseCat(Request $request){
        $products=Category::getCourseByCat($request->slug);

        $recent_courses=Course::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        if(request()->is('e-shop.loc/course-grids')){
            return view('frontend.pages.course-grids')->with('courses',$courses->courses)->with('recent_courses',$recent_courses);
        }
        else{
            return view('frontend.pages.course-lists')->with('courses',$courses->courses)->with('recent_courses',$recent_courses);
        }

    }

}

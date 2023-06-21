<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Str;
class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banner=Banner::orderBy('id','DESC')->paginate(10);
        return view('backend.banner.index')->with('banners',$banner);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.banner.create');
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
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'photo' => 'required|mimes:jpeg,png',
            'status'=>'required|in:active,inactive',
        ]);
        $data=$request->all();
        $slug=Str::slug($request->title);
        $count=Banner::where('slug',$slug)->count();
        if($count>0){
            $slug=$slug.'-'.date('ymdis').'-'.rand(0,999);
        }
        $data['slug']=$slug;

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $img_name = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $request->photo->move(public_path('upload'), $img_name);
            $img_url = '/upload/'.$img_name;
            $data['photo'] = $img_url;
        }

        $status=Banner::create($data);
        if($status){
            request()->session()->flash('success','Banner agregado correctamente!');
        }
        else{
            request()->session()->flash('error','Ocurrio un error al crear el banner!');
        }
        return redirect()->route('banner.index');
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
        $banner=Banner::findOrFail($id);
        return view('backend.banner.edit')->with('banner',$banner);
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
        $banner=Banner::findOrFail($id);
        $this->validate($request,[
            'title'=>'string|required|max:50',
            'description'=>'string|nullable',
            'photo' => 'mimes:jpeg,png',
            'status'=>'required|in:active,inactive',
        ]);

        $data=$request->all();

        if((strcmp($request->title, $banner->title) !== 0))
        {
            $slug=Str::slug($request->title);
            $count=Banner::where('slug',$slug)->count();
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


        $status=$banner->fill($data)->save();
        if($status){
            request()->session()->flash('success','Banner actualizado correctamente!');
        }
        else{
            request()->session()->flash('error','Ocurrio un error al actualizar el banner!"');
        }
        return redirect()->route('banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner=Banner::findOrFail($id);
        $status=$banner->delete();
        if($status){
            request()->session()->flash('success','Banner eliminado correctamete!');
        }
        else{
            request()->session()->flash('error','Ocurrio un error al eliminar el banner!');
        }
        return redirect()->route('banner.index');
    }
}

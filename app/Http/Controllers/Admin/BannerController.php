<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Banners';
        $empty_message = 'No Result Found';
        $banner = Banner::all();
        return view ('admin.banner.index',compact('banner','page_title','empty_message'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Categories';
        return view ('admin.banner.create',compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $banner = new Banner();
        $banner->title  = $request->title;
        $banner->status  = 1;
        $banner->desc   = $request->desc;

        $image = $request->file('cover');
        $path = imagePath()['banner']['path'];
        $size = imagePath()['banner']['size'];
        $filename = $request->image;
        if ($request->hasFile('cover')) {
            try {
                $filename = uploadImage($image, $path, $size, $filename);
//                    dd($filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
            $banner->cover=$filename;
        }
        if ($banner->save()) {
            return redirect()->route('admin.banner')->with('success', 'Data added successfully');
           } else {
            return redirect()->route('admin.banner.create')->with('error', 'Data failed to add');
    
           }
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
        $page_title = 'Categories';
        $banner = Banner::findOrFail($id);
        return view ('admin.banner.edit', compact('banner','page_title'));
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
        $banner = Banner::findOrFail($id);
        $banner->title  = $request->title;
        $banner->desc   = $request->desc;
        $image = $request->file('cover');
        $path = 'assets/images/banner/';
        $size = imagePath()['banner']['size'];
        $filename = $request->cover;
        if ($request->hasFile('cover')) {
            try {
                $filename = uploadImage($image, $path, $size, $filename);
//                    dd($filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        $banner->cover = $filename;
    }   
    // dd($banner);
        if ($banner->update()) {
            return redirect()->route('admin.banner')->with('success', 'Data updated successfully');
           } else {
            return redirect()->route('admin.banner.edit')->with('error', 'Data failed to update');
    
           }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->delete()) {
            if($banner->cover && file_exists(storage_path('app/public/' . $banner->cover))){
                \Storage::delete('public/'. $banner->cover);
            }
        }
        
        return redirect()->route('admin.banner')->with('success', 'Data deleted successfully');
    }
    public function status($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = ($banner->status ? 0 : 1);
        $banner->save();

        $notify[] = ['success', 'Status updated!'];
        return back()->withNotify($notify);
    }
}

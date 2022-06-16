<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Image;

class CategoryController extends Controller
{
    public function index()
    {
        $page_title = 'Categories';
        $empty_message = 'No Result Found';
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('page_title', 'categories', 'empty_message'));
    }

    public function store()
    {

        \request()->validate([
            'name' => 'required|string|max:70|unique:categories,name',
            'type' => 'required',
        ]);
        $request = \request();
        $category = new Category();
        $category->name = $request->name;
        $category->custom_additional_field_name=$request->custom_additional_field_name;
        $category->field_name=$request->field_name;
        $category->api=$request->api;
        $category->type = $request['type'];
        $request->sort ? $category->sort=$request->sort :' ';
        if ($request['type'] == "BALANCE" || $request['type'] == "OTHER"){
            if ($request['special_field'] != ""){
                $category->field_name = $request['special_field'];
            }else{
                $category->field_name = \request()->field_name;;
            }
        }
        if ($request['type']=="GAME")
        {
            $category->field_name = 'رقم اللاعب';
        }


        $image = $request->file('image');
            $path = imagePath()['category']['path'];
            $size = imagePath()['category']['size'];
            $filename = $request->image;
            if ($request->hasFile('image')) {
                try {
                    $filename = uploadImage($image, $path, $size, $filename);
//                    dd($filename);
                } catch (\Exception $exp) {
                    $notify[] = ['errors', 'Image could not be uploaded.'];
                    return back()->withNotify($notify);
                }
                $category->image=$filename;
        }
            $category->save();
            $notify[] = ['success', 'Category added!'];
            return back()->withNotify($notify);


    }
    public function update($id,Request $request)
    {
        \request()->validate([
            'name' => 'required|string|max:70|unique:categories,name,' . $id
        ]);
        $category = Category::findOrFail($id);
        $category->name = \request()->name;
        $category->custom_additional_field_name=\request()->custom_additional_field_name;
        $category->api=\request()->api;
        $category->field_name=\request()->field_name;
        $category->sort=$request->sort ;
        $image = $request->file('image');
        $path = 'assets/images/category/';
        $size = imagePath()['category']['size'];
        $filename = $request->image;
        if ($request['type'] == "BALANCE" || $request['type'] == "OTHER"){
            if ($request['special_field'] != ""){
                $category->field_name= $request['special_field'];
            }else{
                $category->field_name = \request()->field_name;;
            }
        }
        if ($request['type']=="GAME") {
            $category->field_name = 'رقم اللاعب';
        }
        $category->type = $request['type'];
        if ($request->hasFile('image')) {
            try {
                $filename = uploadImage($image, $path, $size, $filename);
//                    dd($filename);
            } catch (\Exception $exp) {
                $notify[] = ['errors', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }

//            $filename = time() . '_' . $category->name . '.jpg';
//            $location = 'assets/images/category/' . $filename;
//            $in['image'] = $filename;
//            $path = './assets/images/category/';
//            $link = $path . $category->image;
//            if (file_exists($link)) {
//                @unlink($link);
//            }
////            $size = imagePath()['category']['size'];
////            $image = Image::make($image);
////            $size = explode('x', strtolower($size));
////            $image->resize($size[0], $size[1]);
//            $image->store($path);
            $category->image=$filename;
        }
        $category->save();

        $notify[] = ['success', 'Category updated!'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $cat = Category::findOrFail($id);
        $cat->status = ($cat->status ? 0 : 1);
        $cat->save();

        $notify[] = ['success', 'Status updated!'];
        return back()->withNotify($notify);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Image;
use Auth;
use DB;

class PostsController extends Controller
{
    public function uploadImage(Request $request){
        
        $request->validate(
            [
                'caption' => 'required|max:255',
                'category' => 'required',
                'image' => 'required|image|mimes:png,jpeg,jpg,bmp'
            ], 
            [
                'caption.required' => 'Please add a caption',
                'category.required' => 'Please add a category',
                'image.required' => 'Please add an image'
            ]    
        );

        if($request->hasFile('image')){
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();

            $file_name = $request->category.rand(1000,9999).'_'.time().'.'.$ext;

            $thumbnail_path = public_path('uploaded_images/thumbnails');

            $resized_image = Image::make($file->getRealPath());
            $resized_image->resize(400,200,function($const){})->save($thumbnail_path.'/'.$file_name);

            $file->move(public_path('uploaded_images'), $file_name);
        }

        $store = new Post;
        $store->caption = $request->caption;
        $store->category = $request->category;
        $store->image = $file_name;
        $store->user_id = Auth::user()->id;
        $store->save();

        return redirect()->back()->with('success', 'Image uploaded!');

    }

    public function delete($id){

        $search = Post::find($id);

        if ($search){
            if ($search->user_id == Auth()->id()){
                
                $file_path = public_path('uploaded_images'.$search->image);
                if(/File::exists($file_path)){
                    /File::delete($file_path);
                }

                $file_thumbnail_path = public_path('uploaded_images/thumbnails/'.$search->image);
                if(/File::exists($file_thumbnail_path)){
                    /File::delete($file_thumbnail_path);
                }

                $search->delete();
                return redirect()->back()->with('success', 'Image Deleted!');

            }
            else{
                return redirect()->back()->with('error', 'You are not authorized to delete this image!');
            }
        }
        else{
            return redirect()->back()->with('error', 'Image not found!');
        }
    }
}
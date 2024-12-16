<?php


namespace App\Http\Controllers\admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
{
   public function index(Request $request) {
        $categories = Category::latest();


        if (!empty($request->get('keyword'))) {
          $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories = $categories->paginate(10);
        return view('admin.category.list',compact('categories'));
   }
   public function create() {
       return view('admin.category.create');
   }
   public function store(Request $request) {
       $validator = Validator::make($request->all(),[
           'name' => 'required',
           'slug' => 'required|unique:categories',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
       ]);


       if ($validator->passes()){


          $category = new Category();
          $category->name = $request->name;
          $category->slug = $request->slug;
          $category->status = $request->status;
          if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('category_images', 'public'); // Save in storage/app/public/category_images
            $category->image = $imagePath; // Save the path in the database
          }
          $category->save();


          $request->session()->flash('success','Category added successfully');


          return response()->json([
            'status' => true,
            'message' => 'Category added successfully'
          ]);


       } else {
           return response()->json([
             'status' => false,
             'errors' => $validator->errors()
           ]);
       }
   }
   public function edit($categoryId, Request $request) {
      $category = Category::find($categoryId);
      if (empty($category)) {
        return redirect()->route('categories.index');
      }
      return view('admin.category.edit',compact('category'));
   }
   public function update($categoryId, Request $request) {
      $category = Category::find($categoryId);
      if (empty($category)) {
         $request->session()->flash('error','Category not found');
         return response()->json([
              'status' => false,
              'notFound' => true,
              'message' => 'Category not found'
         ]);
      }
      $validator = Validator::make($request->all(),[
          'name' => 'required',
          'slug' => 'required|unique:categories,slug,'.$category->id.',id',
          'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
      ]);


      if ($validator->passes()){


          $category->name = $request->name;
          $category->slug = $request->slug;
          $category->status = $request->status;
          $oldImage =$category->image;
          if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($oldImage) {
              Storage::disk('public')->delete($oldImage);
            }
            $imagePath = $request->file('image')->store('category_images', 'public');
            $category->image = $imagePath;
          }
          $category->save();


          $request->session()->flash('success','Category updated successfully');


          return response()->json([
            'status' => true,
            'message' => 'Category updated successfully'
          ]);


      } else {
          return response()->json([
            'status' => false,
            'errors' => $validator->errors()
          ]);
      }
  }
   public function destroy($categoryId, Request $request) {
      $category = Category::find($categoryId);
      if (empty($category)){
        $request->session()->flash('error','Category not found');
        return response()->json([
          'status' => true,
          'message' => 'Category not found'
        ]);
      }
       // Delete the old image if it exists
      if ($category->image) {
         Storage::disk('public')->delete($category->image);
      }


      // Delete the category record
      $category->delete();


      $request->session()->flash('success','Category deleted successfully');


      return response()->json([
        'status' => true,
        'message' => 'Category deleted successfully'
      ]);


   }




  }


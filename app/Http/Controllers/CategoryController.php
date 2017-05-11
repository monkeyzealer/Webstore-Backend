<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use File;

class CategoryController extends Controller
{
    public function __construct()
    {
      $this->middleware("jwt.auth", ["only" => ["storeCategory", "destroyCategory"]]);
    }
    public function home()
    {
      return File::get("index.html");
    }
    public function index()
    {
      //this makes it so the Categories show up in a order from newest to oldest
      $categories = Category::orderBy("id", "desc")->get();
      return Response::json($categories);
    }
    //stores the Categories in the database
    public function storeCategory(Request $request)
    {
      //makes these Required field
      $rules = [
        "category" => "required",
      ];

      $Validator = Validator::make(Purifier::clean($request->all()),$rules);
      //if validation fails it wil lcome up with a error that you need to fill out all fields
      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $user = Auth::user();
      if($user->roleID != 1)
      {
        return Response::json(["error" => "Your not authorized to do this"]);
      }

      //this makes it so it stores all the stuff in the fields
      $category = new Category;
      $category->category = $request->input("category");
      $category->save();

      return Response::json(["success" => "Category Was Successfully Added To Database."]);
    }
    //this is the function that will allow admin users to update the prduct
    public function updateCategory($id, Request $request)
    {
      $rules = [
        "category" => "required",
      ];

      $Validator = Validator::make(Purifier::clean($request->all()),$rules);//passes data
      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $user = Auth::user();
      if($user->roleID != 1)
      {
        return Response::json(["error" => "Your not authorized to do this"]);
      }

      $category = Category::find($id);
      $category->save();

      return Response::json(["success" => "Category Has Been Updated"]);
    }
    public function showCategory($id)
    {
      //finds Category id
      $category = Category::find($id);
      //returns everything about the category
      return Response::json($category);
    }
    public function destroyCategory($id)
    {
      $category = Category::find($id);
      $category->delete();
      return Response::json(["success" => "Deleted Category"]);
    }
}

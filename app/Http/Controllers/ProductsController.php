<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Product;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use File;

class ProductsController extends Controller
{
    public function __construct()
    {
      $this->middleware("jwt.auth", ["only" => ["storeProduct", "destoryProduct"]]);
    }
    public function index()
    {
      //this makes it so the products show up in a order from newest to oldest
      $products = Product::orderby("id","desc")->get();
      return Response::json($products);
    }
    //stores the Products in the database
    public function storeProduct(Request $request)
    {
      //makes these required fields
      $rules = [
        "product" => "required",
        "image" => "required",
        "description" => "required",
        "catagory" => "required",
        "stock" => "required",
        "price" => "required",
      ];

        $Validator = Validator::make(Purifer::clean($request->all()),$rules);//passes data
      //if validation fails it will come up with a error that you need to fill out all fields
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
      $product = new Product;
      $product->product = $request->input("product");
      $product->description = $request->input("description");
      $product->category = $request->input("category");
      $product->stock = $request->input("stock");
      $product->price = $request->input("price");
      $image = $request->file(:"image");
      $imageName = $image->getClientOriginalName();
      $image->move("storage/",$imageName);
      $product->image = $request->root()."/storage/".$imageName;
      $product->save();

      return Respone::json(["success" => "Product Was Successfully Uploaded."]);
    }
    //this is the function that will allow admin users to update the prduct
    public function updateProduct($id, Request $request)
    {
      $rules = [
        "product" => "required",
        "description" => "required",
        "catagory" => "required",
        "stock" => "required",
        "price" => "required",
      ];

      $Validator = Validator::make(Purifer::clean($request->all()),$rules);//passes data
      //if validation fails it will come up with a error that you need to fill out all fields
      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $user = Auth::user();
      if($user->roleID != 1)
      {
        return Response::json(["error" => "Your not authorized to do this"]);
      }

      $product = Product::find($id);
      $product->product = $request->input("product")
      $product->description = $request->input("description")
      $product->category = $request->input("category");
      $product->stock = $request->input("stock");
      $product->price = $request->input("price");
      $image = $request->file("image")
      if(!empty($image))
      {
        $imageName = $image->getClientOriginalName();
        $image->move("storage/",$imageName);
        $product->image = $request->root()."/storage/".$imageName;
      }
      $product->save();

      return Response::json(["success" => "Product Has Been Updated"]);
    }
    public function showProduct($id)
    {
      //finds product id
      $product = Product::find($id);
      //returns everything about the product
      return Response::json($product);
    }
    //deletes the product
    public function destoryProduct($id)
    {
      $product = Product::find($id);
      $product->delete();
      return Response::json(["success" => "Deleted Product"]);
    }
}

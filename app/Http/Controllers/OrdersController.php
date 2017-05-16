<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Order;
use App\Product;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use File;

class OrdersController extends Controller
{
    public function __construct()
    {
      $this->middleware("jwt.auth", ["only" => ["storeOrder", "destroyOrder", "updateOrder"]]);
    }
    public function index()
    {
      //this makes it so the orders show up in a order from newest to oldest
      $orders = Order::orderby("id","desc")->get();
      return Response::json($orders);
    }
    //stores the Orders in the database
    public function storeOrder(Request $request)
    {
      //makes these required fields
      $rules = [
        "productID" => "required",
        "userID" => "required",
        "amount" => "required",
        "comment" => "required",
        "totalPrice" => "required",
      ];
      $Validator = Validator::make(Purifier::clean($request->all()),$rules);//passes data

      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $product = Product::find($request->input("productID"));
      if(empty($product))
      {
        return Response::json(["error" => "Invalid Product"]);
      }

      if($product->availability == 0)
      {
      return Response::json(["success" => "Success"]);
      }

      //this makes it stores all the stuff in the fields
      $order = new Order;
      $order->userID = Auth::user()->id;
      $order->productID = $request->input("productID");
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("amount")*$product->price;
      $order->comment = $request->input("comment");
      $order->save();

      return Response::json(["success" => "Order Was Successfully Created"]);
    }

    //this allows user to update there order
    public function updateOrder($id, Request $request)
    {
      $rules = [
        "productID" => "required",
        "userID" => "required",
        "amount" => "required",
        "comment" => "required",
      ];

      $Validator = Validator::make(Purifier::clean($request->all()),$rules);//passes data

      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $product = Product::find($request->input("productID"));
      if(empty($product))
      {
        return Response::json(["error" => "Invalid Product"]);
      }

      if($product->availability == 0)
      {
      return Response::json(["success" => "Success"]);
      }

      $order = Order::find($id);
      $order->userID = Auth::user()->id;
      $order->productID = $request->input("productID");
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("amount")*$product->price;
      $order->comment = $request->input("comment");
      $order->save();

      return Response::json(["success" => "Order Has Been Updated"]);
    }
    public function showOrder($id)
    {
      //finds order id
      $order = Order::find($id);
      //returns everything about the order
      return Response::json($order);
    }
    //deletes the Order
    public function destroyOrder($id)
    {
      $order = Order::find($id);
      $user=Auth::user();
      if($user->roleID != 1 || $user->id != $order->userID)
    {
      return Response::json(["error" => "You are not authorized to do this!"]);
    }
      $order->delete();
      return Response::json(["success" => "Order Has Been Deleted"]);
    }
}

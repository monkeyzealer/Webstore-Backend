<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Order;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use File;

class OrdersController extends Controller
{
    public function __construct()
    {
      $this->middleware("jwt.auth", ["only" => ["storeOrder", "destoryOrder"]]);
    }
    public function index()
    {
      //this makes it so the orders show up in a order from newest to oldest
      $orders = Order::orderby("id","desc")->get();
      return Response::json($products);
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
      ];
      $Validator = Validator::make(Purifer::clean($request->all()),$rules);//passes data

      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      //this makes it stores all the stuff in the fields
      $order = new Order;
      $order->userID = Auth::user()->id;
      $order->productID = $request->input("productID");
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("totalPrice");
      $order->comment = $request->input("comment");
      $product->save();

      return Response::json(["success" => "Order Was Successfully Created"])
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

      $Validator = Validator::make(Purifer::clean($request->all()),$rules);//passes data

      if($Validator->fails())
      {
        return Response::json(["error" => "You need to fill out all fields"]);
      }

      $order = Order::find($id);
      $order->userID = Auth::user()->id;
      $order->productID = $request->input("productID");
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("totalPrice");
      $order->comment = $request->input("comment");
      $product->save();

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
    public function destoryOrder($id)
    {
      $order = Order::find($id);
      $order->delete();
      return Response::json(["success" => "Order Has Been Deleted"]);
    }
}

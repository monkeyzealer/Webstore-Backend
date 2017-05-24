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
      $this->middleware("jwt.auth", ["only" => ["index", "showUserOrders", "storeOrder", "destroyOrder", "updateOrder"]]);
    }
    public function index()
    {
      $user = Auth::user();
      //this makes it so the orders show up in a order from newest to oldest
      $orders = Order::join("users", "orders.userID", "=", "users.id")
                      ->join("products", "orders.productID", "=", "products.id")
                      ->orderby("orders.id","desc")
                      ->select("orders.id", "orders.amount", "orders.totalPrice", "orders.userID", "orders.productID", "orders.comment", "users.name", "products.product", "products.stock")
                      ->get();
        if($user->roleID != 1)
        {
          return Response::json(["error" => "not allowed"]);
        }

      return Response::json($orders);
    }
    //stores the Orders in the database
    public function storeOrder(Request $request)
    {
      //makes these required fields
      $rules = [
        "productID" => "required",
        "amount" => "required",
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

      if($product->stock == 0)
      {
      return Response::json(["error" => "unavailale"]);
      }

      //this makes it stores all the stuff in the fields
      $order = new Order;
      $order->userID = Auth::user()->id;
      $order->productID = $request->input("productID");
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("amount")*$product->price;
      $order->comment = $request->input("comment");
      $order->save();

      return Response::json(["success" => "Order Was Successfully Created", "total" => $order->totalPrice ]);
    }

    //this allows user to update there order
    public function updateOrder($id, Request $request)
    {
      $rules = [
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

      if($product->stock == 0)
      {
      return Response::json(["error" => "empty"]);
      }

      $order = Order::find($id);
      $order->userID = Auth::user()->id;
      $order->amount = $request->input("amount");
      $order->totalPrice = $request->input("amount")*$product->price;
      $order->comment = $request->input("comment");
      $order->save();

      return Response::json(["success" => "Order Has Been Updated", "total" => $order->totalPrice]);
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
    public function showUserOrders()
    {
      $user = Auth::user();
      $orders = Order::where("orders.userID", "=", $user->id)
                      ->join("users", "orders.userID", "=", "users.id")
                      ->join("products", "orders.productID", "=", "products.id")
                      ->orderby("orders.id","desc")
                      ->select("orders.id", "orders.amount", "orders.totalPrice", "orders.userID", "orders.productID", "orders.comment", "users.name", "products.product", "products.stock")
                      ->get();
      return Response::json($orders);
    }
}

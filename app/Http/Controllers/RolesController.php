<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Role;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use File;

class RolesController extends Controller
{
      public function __construct()
      {
        $this->middleware("jwt.auth", ["only" => ["storeRole", "destroyRole", "updateRole"]]);
      }
      public function index()
      {
        //this makes it so the roles show up in a order from newest to oldest
        $roles = Role::orderby("id","desc")->get();
        return Response::json($roles);
      }
      //stores the roles in the database
      public function storeRole(Request $request)
      {
        //makes these required fields
        $rules = [
          "roleName" => "required",
        ];

      $Validator = Validator::make(Purifier::clean($request->all()),$rules);//passes data
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

      $role = new Role;
      $role->roleName = $request->input("roleName");
      $role->save();
      return Response::json(["success" => "Role Was Successfully Added To Database."]);
    }
    public function updateRole($id, Request $request)
    {
      $rules = [
        "roleName" => "required",
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

      $role = Role::find($id);
      $role->roleName = $request->input("roleName");
      $role->save();

      return Response::json(["success" => "Role Has Been Updated"]);
    }
    public function showRole($id)
    {
      //finds role id
      $role = Role::find($id);
      //returns everything in roles database
      return Response::json($role);
    }
    public function destroyRole($id)
    {
      $role = Role::find($id);
      $role->delete();
      return Response::json(["success" => "Role Has Been Deleted"]);
    }
}

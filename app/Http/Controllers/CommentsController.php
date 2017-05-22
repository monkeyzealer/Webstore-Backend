<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Purifier;
use JWTAuth;
use Auth;
use App\Comment;
use App\product;
use Response;


class CommentsController extends Controller
{
  public function __construct(){
    $this->middleware("jwt.auth", ["only" => ["storeComment", "deleteComment"] ]);
  }
    public function index($id)
    {
      $comments = Comment::where("comments.productID", "=", $id)
        ->join("users", "comments.userID", "=", "users.id")
        ->select("comments.id", "comments.body", "comments.created_at", "users.name")
        ->orderBy("id", "desc")
        ->take(5)
        ->get();

        foreach($comments as $key => $comment)
        {
          $comment->commentDate = Carbon::createFromTimeStamp(strtotime($comment->created_at))->diffForHumans();
        }

        return Response::json($comments);
    }
    public function storeComment(Request $request)
    {
      $rules = [
        "commentBody" => "required",
        "productID" => "required",
      ];

      $Validator = Validator::make(Purifier::clean($request->all()),$rules);

      if($Validator->fails())
      {
        return Response::json(["error" => "you need to fill out all fields"]);
      }
      $user = Auth::user();
      $check = product::find($request->input("productID"));
      if(empty($check))
      {
        return Response::json(["error" => "product Not Found!"]);
      }
      $comment = new Comment;

      $comment->userID = $user->id;
      $comment->productID = $request->input("productID");
      $comment->body = $request->input("commentBody");
      $comment->save();

      $commentData = Comment::where("comments.id", "=", $comment->id)
        ->join("users", "comments.userID", "=", "users.id")
        ->select("comments.id", "comments.body", "comments.created_at", "users.name")
        ->first();
      $commentData->commentDate = Carbon::createFromTimeStamp(strtotime($commentData->created_at))->diffForHumans();

      return Response::json(["success" => "You did it!", "data" => $commentData]);
    }
    public function deleteComment($id)
    {
      $user = Auth::user();
      if($user->roleID != 1)
      {
        return Response::json(["error" => "Your not authorize to do this"]);
      }
      $comment = Comment::find($id);
      $comment->delete();
      return Response::json(["success" => "Deleted Comment"]);
    }
}

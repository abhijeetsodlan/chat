<?php

namespace App\Http\Controllers;

use App\Models\friends;
use Illuminate\Http\Request;
use App\Models\friendRequest;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
     public function sendFriendRequest(Request $request){
         $currentUser = Auth::user();
         $req_sender = $currentUser->id;
         $req_reciever = $request->req_reciever;
     
         $existingRequest = friendRequest::where('req_sender', $req_sender)
                                          ->where('req_reciever', $req_reciever)
                                          ->where('status', 'pending')
                                          ->first();
     
         if ($existingRequest) {
             return response()->json(['success' => false, 'message' => 'Request already sent.']);
         }
     
         $friend = new friendRequest();
         $friend->req_sender = $req_sender;
         $friend->req_reciever = $req_reciever;
         $friend->status = 'pending';
         $friend->save();
     
         return response()->json(['success' => true, 'message' => 'Friend request sent successfully.']);
     }
     
   public function showFriendRequest(){
        $currentUser = Auth::user();
        $recieverId = $currentUser->id;
        $totalRequests = DB::table('friend_requests')
                        ->where('req_reciever',$recieverId)
                        ->where('status','pending')
                        ->get();                   
   }

   public function acceptFriendRequest(Request $request){
    
     $user1 = $request->request_id;

     $currentUser = Auth::user();
     $user2 = $currentUser->id;

     $friends = new friends;
     $friends->user1 = $user1;
     $friends->user2 = $user2;
     $friends->save();

     friendRequest::where('req_sender', $user1)
                 ->where('req_reciever', $user2)
                 ->delete();
                 
     return response()->json(['message'=>'You Are Friends Now']);
   }

   public function rejectFriendRequest(Request $request){

        $sender = $request->request_id;
        $currentUser = Auth::user();
        $reciever = $currentUser->id;

        friendRequest::where('req_sender', $sender)
        ->where('req_reciever', $reciever)
        ->delete();

        return response()->json(['message'=>'success']);
   }
}

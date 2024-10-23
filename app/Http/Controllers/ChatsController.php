<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\chats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
 
    public function store(Request $request)
    {
        $message = new chats;
        $message->sender_id = $request->sender_id;
        $message->reciever_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();

        $chats = DB::table('chats')
                ->where(function ($query) use ($request) {
                    $query->where('sender_id', $request->sender_id)
                        ->where('reciever_id', $request->receiver_id);
                })
                ->orWhere(function ($query) use ($request) {
                    $query->where('sender_id', $request->receiver_id)
                        ->where('reciever_id', $request->sender_id);
                })
                ->get();

        return response()->json(['res' => 'Message saved']);
    }

   
    public function getMessages($receiver_id){

        $currentUserId = Auth::user()->id;

        $chats = Chats::where(function($query) use ($currentUserId, $receiver_id) {
            $query->where('sender_id', $currentUserId)
                ->where('reciever_id', $receiver_id);
        })->orWhere(function($query) use ($currentUserId, $receiver_id) {
            $query->where('sender_id', $receiver_id)
                ->where('reciever_id', $currentUserId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json(['chats' => $chats]);
    }
    public function deleteMessage($id)
    {
        $message = Chats::find($id);
        if (!$message) {
            return response()->json(['status' => 'error', 'message' => 'Message not found'], 404);
        }
        $currentUserId = Auth::user()->id;
        if($message->sender_id == $currentUserId){
            $message->delete();
            return response()->json(['status' => 'success', 'message' => 'Message deleted successfully']);
        }else{
            return response()->json(['status' => 'error', 'message' => 'You are not authorized to delete this message'], 404);
        }
    
    }
    
}

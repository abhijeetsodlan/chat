<?php

namespace App\Http\Controllers;

use App\Models\groups;
use App\Models\groupChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupsController extends Controller
{
   
    public function createGroup(Request $request){
        $currentUser = Auth::user();
        $group = new groups;

        if ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')->store('profiles', 'public'); 
            $group->profile = $profilePath; 
        }

        $group->name = $request->name;
        $group->description = $request->description;
        $group->admin = $currentUser->id; ;
        $members = $request->members;
        $members[] = $currentUser->id; 
        $members = array_map('strval', $members); // Convert all members to strings

        $group->members = json_encode($members);

        $group->save();
        return response()->json(['res'=>'grp created']);
    }

    public function myprofile(){
        return view('myprofile');
    }

    public function sendGroupMessage(Request $request){
        $message = new groupChat;
        $message->group_id = $request->group_id;
        $message->sender_id = $request->sender_id;
        $message->message = $request->groupMessage;
        $message->save();
        return response()->json(['message'=>'message saved']);

    }

    public function getGroupMessages($group_id){

        $currentUserId = Auth::user()->id;

        $chats = groupChat::where(function($query) use ($currentUserId, $group_id) {
            $query->where('group_id', $group_id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json(['chats' => $chats]);
    }

    
}

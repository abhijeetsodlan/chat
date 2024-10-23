<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\friends;
use Illuminate\Http\Request;
use App\Models\friendRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class userController extends Controller
{
        public function index(){

            if (Auth::check()) {
                $currentUser = Auth::user();
                $recieverId = $currentUser->id;
        
                //Fetch all pending friend requests
                $totalRequest = friendRequest::where('req_reciever', $recieverId)
                                            ->where('status', 'pending')
                                            ->get();
        
                //Initialize totalRequests array
                $totalRequests = [];
                foreach ($totalRequest as $item) {
                    $user = User::where('id', $item->req_sender)->first();
                    if ($user) {
                        $totalRequests[] = $user; 
                    }
                }
                $noRequestsMessage = "No request to show";
        

                // Fetch contacts (friends)
                $contacts = friends::where(function ($query) use ($recieverId) {
                    $query->where('user1', $recieverId)
                        ->orWhere('user2', $recieverId);
                })->get();
        
                $users = [];
        
                foreach ($contacts as $contact) {
                    if ($contact->user1 == $recieverId) {
                        $otherUser = User::where('id', $contact->user2)->first();
                    } elseif ($contact->user2 == $recieverId) {
                        $otherUser = User::where('id', $contact->user1)->first();
                    }
        
                    if ($otherUser) {
                        $users[] = $otherUser;
                    }
                }

                //get groups --> 
                $groups = DB::table('groups')
                ->whereRaw("JSON_CONTAINS(members, '\"$currentUser->id\"')")
                ->get();
            
            $allMemberIds = [];
            
            foreach ($groups as $group) {
                // Decode the JSON members field to get the member IDs
                $members = json_decode($group->members, true);
            
                if (is_array($members)) {
                    $allMemberIds = array_merge($allMemberIds, $members);
                }
            }
            
            // Step 2: Remove duplicate IDs (optional)
            $uniqueMemberIds = array_unique($allMemberIds);
            
            // Step 3: Fetch only the names of the users
            $userNames = DB::table('users')
                ->whereIn('id', $uniqueMemberIds)
                ->select('id', 'name')
                ->get();
        
                // Pass variables to the view
                return view('dashboard', compact('users', 'totalRequests', 'groups','noRequestsMessage','userNames'));
            } else {
                return redirect()->route('userlogin');
            }
        }

    public function userlogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            return view('userlogin'); 
        }
    }
    
    public function store(Request $request){
       
        $validatedData = $request->validate([
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional profile image validation
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Ensure confirmation
        ]);
    $filePath = null; 

    if ($request->hasFile('profile')) {
        $filePath = $request->file('profile')->store('profiles', 'public'); 
    }

    $user = new User();
    $user->profile = $filePath; 
    $user->username = $validatedData['username'];
    $user->email = $validatedData['email'];
    $user->password = Hash::make($validatedData['password']); 
    $user->save();

    return response()->json(['res' => 'Account created']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            session(['user' => Auth::user()]);
            return response()->json(['res' => 'Login successful', 'user' => Auth::user()]);
        }
        return response()->json(['res' => 'Invalid credentials'], 401);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('userlogin');
    }

    public function searchUser(Request $request){
    
        $searchTerm = $request->input('searchusers');
        $currentUser = Auth::user();
                                    
        $users = User::where('username', 'LIKE', '%' . $searchTerm . '%')
                       ->where('id', '!=', $currentUser->id)
                       ->get(['id', 'username', 'profile']); 

        return response()->json(['users' => $users]);
    }

    public function updateprofile(Request $request){
        $user = Auth::user();
        $user->username = $request->input('username');
        $user->email = $request->input('email');

        if ($request->hasFile('profile')) {
            
            if ($user->profile) {
                Storage::delete('public/' . $user->profile);
            }
            // Store new profile picture
            $profilePath = $request->file('profile')->store('profiles', 'public');
            $user->profile = $profilePath; 
        }
        $user->save();

        return response()->json(['message' => 'Profile Updated']);
    
    }
}

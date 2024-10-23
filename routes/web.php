<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\FriendRequestController;


Route::get('/',[userController::class,'index'])->name('dashboard');
Route::get('sidebar',[userController::class,'sidebar']);
Route::get('userlogin',[userController::class,'userlogin'])->name('userlogin');
Route::post('userRegister',[userController::class,'store'])->name('userRegister');
Route::post('userlogging',[userController::class,'login'])->name('userlogging');
Route::post('sendMessage',[ChatsController::class,'store'])->name('sendMessage');
Route::post('/logout', [userController::class, 'logout'])->name('logout');

Route::get('/getMessages/{receiver_id}', [ChatsController::class, 'getMessages']);
Route::delete('/deleteMessage/{id}', [ChatsController::class, 'deleteMessage'])->name('deleteMessage');

Route::post('searchUser',[userController::class,'searchUser'])->name('searchUser');

Route::post('sendFriendRequest',[FriendRequestController::class,'sendFriendRequest'])->name('sendFriendRequest');
Route::post('acceptFriendRequest',[FriendRequestController::class,'acceptFriendRequest'])->name('acceptFriendRequest');
Route::post('rejectFriendRequest',[FriendRequestController::class,'rejectFriendRequest'])->name('rejectFriendRequest');

Route::post('createGroup',[GroupsController::class,'createGroup'])->name('createGroup');
Route::post('sendGroupMessage',[GroupsController::class,'sendGroupMessage'])->name('sendGroupMessage');
Route::get('/getGroupMessages/{group_id}', [GroupsController::class, 'getGroupMessages']);

Route::get('myprofile',[GroupsController::class,'myprofile'])->name('myprofile');
Route::post('updateprofile', [userController::class, 'updateProfile'])->name('updateprofile');
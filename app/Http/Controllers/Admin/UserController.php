<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\User;use Illuminate\Http\Request;
class UserController extends Controller {public function index(){return view('admin.users.index',['users'=>User::latest()->paginate(20)]);}public function update(Request $r,User $user){$d=$r->validate(['role'=>'required|in:admin,editor,user','status'=>'required|in:active,blocked']);if($user->id===auth()->id()&&$d['status']==='blocked')return back()->withErrors(['user'=>'You cannot block your own account.']);$user->update($d);return back()->with('success','User updated.');}}

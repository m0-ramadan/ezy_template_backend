<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\ServiceRequest;use Illuminate\Http\Request;
class ServiceRequestController extends Controller {public function index(){return view('admin.requests.index',['requests'=>ServiceRequest::latest()->paginate(20)]);}public function update(Request $r,ServiceRequest $serviceRequest){$d=$r->validate(['status'=>'required|in:new,in_progress,completed,cancelled','admin_notes'=>'nullable|string|max:5000']);$serviceRequest->update($d);return back()->with('success','Request updated.');}}

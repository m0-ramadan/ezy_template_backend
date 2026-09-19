<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\ServiceRequest; use Illuminate\Http\Request;
class ServiceRequestController extends Controller { public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:120','email'=>'required|email','service'=>'required|string|max:120','message'=>'required|string|max:5000','budget'=>'nullable|string|max:100']);ServiceRequest::create($d);return response()->json(['message'=>'Request received.']);} }

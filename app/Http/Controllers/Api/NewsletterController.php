<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Models\NewsletterSubscriber; use Illuminate\Http\Request;
class NewsletterController extends Controller { public function store(Request $r){$d=$r->validate(['email'=>'required|email|max:255']);NewsletterSubscriber::updateOrCreate(['email'=>$d['email']],['status'=>'subscribed']);return response()->json(['message'=>'Subscribed successfully.']);} }

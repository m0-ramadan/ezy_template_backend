<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\NewsletterSubscriber;use Illuminate\Http\Request;
class NewsletterController extends Controller {public function index(){return view('admin.newsletter.index',['subscribers'=>NewsletterSubscriber::latest()->paginate(30)]);}public function update(Request $r,NewsletterSubscriber $subscriber){$d=$r->validate(['status'=>'required|in:subscribed,unsubscribed']);$subscriber->update($d);return back()->with('success','Subscriber updated.');}}

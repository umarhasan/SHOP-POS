<?php
namespace App\Http\Controllers;
use App\Models\Contact;use App\Models\ContactTransaction;use Illuminate\Http\Request;
class ContactController extends Controller
{
 public function index(Request $r){$contacts=Contact::when($r->type,fn($q)=>$q->whereIn('type',[$r->type,'both']))->when($r->q,fn($q)=>$q->where('name','like','%'.$r->q.'%')->orWhere('phone','like','%'.$r->q.'%'))->latest()->paginate(25)->withQueryString();return view('contacts.index',compact('contacts'));}
 public function store(Request $r){$d=$r->validate(['type'=>'required|in:customer,supplier,both','name'=>'required|string|max:190','phone'=>'nullable|string|max:40','email'=>'nullable|email','address'=>'nullable|string','opening_balance'=>'nullable|numeric']);$c=Contact::create($d);if((float)($d['opening_balance']??0)>0)ContactTransaction::create(['contact_id'=>$c->id,'reference_type'=>'opening_balance','direction'=>$c->type==='supplier'?'credit':'debit','amount'=>$d['opening_balance'],'description'=>'Opening balance','transacted_at'=>now(),'user_id'=>auth()->id()]);return back()->with('success','Contact saved.');}
}

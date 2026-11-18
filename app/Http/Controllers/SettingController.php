<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class SettingController extends Controller{public function index(){return view('settings.index',['settings'=>DB::table('settings')->pluck('value','key')]);}public function save(Request $r){$d=$r->validate(['shop_name'=>'nullable|string|max:190','phone'=>'nullable|string|max:40','address'=>'nullable|string','invoice_prefix'=>'required|string|max:20','currency'=>'required|string|max:10']);foreach($d as $k=>$v)DB::table('settings')->updateOrInsert(['key'=>$k],['value'=>$v,'updated_at'=>now(),'created_at'=>now()]);return back()->with('success','Settings saved.');}}

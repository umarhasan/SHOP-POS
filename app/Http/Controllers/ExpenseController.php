<?php
namespace App\Http\Controllers;
use App\Models\Expense;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class ExpenseController extends Controller
{
 public function index(){return view('expenses.index',['expenses'=>Expense::latest('expense_date')->paginate(25),'businessUnits'=>\App\Models\BusinessUnit::where('is_active',1)->get()]);}
 public function store(Request $r){$d=$r->validate(['business_unit_id'=>'nullable|exists:business_units,id','title'=>'required|string|max:190','category'=>'required|string|max:80','amount'=>'required|numeric|gt:0','expense_date'=>'required|date','payment_method'=>'required|string|max:40','notes'=>'nullable|string']);DB::transaction(function()use($d){$e=Expense::create($d+['user_id'=>auth()->id()]);DB::table('cash_transactions')->insert(['direction'=>'out','source_type'=>'expense','source_id'=>$e->id,'amount'=>$e->amount,'description'=>$e->title,'transacted_at'=>$e->expense_date.' '.now()->format('H:i:s'),'user_id'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);});return back()->with('success','Expense saved.');}
}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Product,Sale,Purchase,Expense};
class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $today = today();
        return view('dashboard.index',[
            'todaySales'=>Sale::whereDate('sold_at',$today)->sum('total'),
            'todayPurchases'=>Purchase::whereDate('purchased_at',$today)->sum('total'),
            'todayExpenses'=>Expense::whereDate('expense_date',$today)->sum('amount'),
            'products'=>Product::count(),
            'lowStock'=>DB::table('products')->join('stock_balances','products.id','=','stock_balances.product_id')->whereColumn('stock_balances.quantity','<=','products.alert_quantity')->count(),
            'recentSales'=>Sale::with('customer')->latest('sold_at')->limit(8)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\{Contact, Payment, ContactTransaction, Sale, Purchase};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    public function ledger(Contact $contact)
    {
        $rows = $contact->transactions()->latest('transacted_at')->paginate(50);
        return view('accounts.ledger', compact('contact', 'rows'));
    }
    public function payment(Request $r)
    {
        $d = $r->validate(['contact_id' => 'required|exists:contacts,id', 'direction' => 'required|in:in,out', 'amount' => 'required|numeric|gt:0', 'method' => 'required|string|max:40', 'paid_at' => 'required|date', 'notes' => 'nullable|string']);
        DB::transaction(function () use ($d) {
            $c = Contact::findOrFail($d['contact_id']);
            Payment::create(['reference_type' => 'contact_payment', 'reference_id' => $c->id, 'contact_id' => $c->id, 'direction' => $d['direction'], 'amount' => $d['amount'], 'method' => $d['method'], 'paid_at' => $d['paid_at'], 'notes' => $d['notes'] ?? null, 'user_id' => auth()->id()]);
            ContactTransaction::create(['contact_id' => $c->id, 'reference_type' => 'payment', 'reference_id' => null, 'direction' => $d['direction'] === 'in' ? 'credit' : 'debit', 'amount' => $d['amount'], 'description' => $d['notes'] ?? 'Account payment', 'transacted_at' => $d['paid_at'], 'user_id' => auth()->id()]);
            DB::table('cash_transactions')->insert(['direction' => $d['direction'] === 'in' ? 'in' : 'out', 'source_type' => 'contact_payment', 'source_id' => $c->id, 'amount' => $d['amount'], 'description' => 'Contact payment: ' . $c->name, 'transacted_at' => $d['paid_at'], 'user_id' => auth()->id(), 'created_at' => now(), 'updated_at' => now()]);
        });
        return back()->with('success', 'Payment recorded.');
    }
    public function cashbook(Request $r)
    {
        $rows = DB::table('cash_transactions')->when($r->from, fn($q) => $q->whereDate('transacted_at', '>=', $r->from))->when($r->to, fn($q) => $q->whereDate('transacted_at', '<=', $r->to))->latest('transacted_at')->paginate(50);
        $in = DB::table('cash_transactions')->where('direction', 'in')->sum('amount');
        $out = DB::table('cash_transactions')->where('direction', 'out')->sum('amount');
        return view('accounts.cashbook', compact('rows', 'in', 'out'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cash;
use App\Models\CashesBalances;
use App\Models\Currency;
use App\Models\Piggybank;
use App\Models\Transaction;
use App\Models\TransactionCategory;

class EditController extends Controller
{
    public function cash($id)
    {
        $item = Cash::where('id', $id)->where('user_id', Auth::id())->get();

        if ($item == null)
        {
            return abort(404);
        }

        return view('edit.cash', ['id' => $id]);
    }

    public function cashdata($id)
    {
        $item = Cash::where('user_id', Auth::id())->where('id', $id)->get();

        return response()->json(['item' => $item]);
    }

    public function savecash(Request $request, $id)
    {
        $item = Cash::where('id', $id)->where('user_id', Auth::id())->first();

        $current_balance = $item['current_balance'];

        $item->name = (string)$request->name;
        $item->current_balance = (double)$request->balance;
        $item->currency = (int)$request->currency;
        $item->active = (int)$request->active;
        $item->in_budget = (int)$request->in_budget;

        $item->save();

        $item2 = CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $id)->get();

        if ($item2 !== null)
        {
            CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $id)->update(['balance' => (double)$request->balance]);
        } else
        {
            $model2 = new CashesBalances;

            $model2->cash_id = (int)$id;
            $model2->balance = (double)$request->balance;
            $model2->date = \date('Y-m-d');

            $model2->save();
        }

        if ($request->balance != $current_balance)
        {
            $model3 = new Transaction;

            $tr_name = '';
            $tr_type = '';
            $tr_summ = '';
            if ($request->balance < $current_balance) {
                $tr_name = 'Expense';
                $tr_type = 'expense';
                $tr_summ = ($current_balance - $request->balance);
            }
            if ($request->balance > $current_balance) {
                $tr_name = 'Income';
                $tr_type = 'income';
                $tr_summ = ($request->balance - $current_balance);
            }

            $category = 'Automatic ' . $tr_name;

            $cat = TransactionCategory::where('user_id', Auth::id())->where('name', $category)->get();

            if ($cat == null)
            {
                $catmodel = new TransactionCategory;

                $catmodel->name = (string)$category;
                $catmodel->user_id = (int)Auth::id();

                $catmodel->save();
            }


            $model3->name = (string)$tr_name;
            $model3->type = (string)$tr_type;
            $model3->summ = (double)$tr_summ;
            $model3->date = \date('Y-m-d');
            $model3->category = (string)$category;
            $model3->currency = (int)$request->currency;
            $model3->from_cash = (int)$id;
            $model3->to_cash = 0;
            $model3->to_piggybank = 0;
            $model3->user_id = (int)Auth::id();

            $model3->save();
        }
    }

    public function activecash(Request $request, $id)
    {
        $item = Cash::where('id', $id)->where('user_id', Auth::id())->first();

        $item->active = (int)$request->active;

        $item->save();
    }

    public function budgetcash(Request $request, $id)
    {
        $item = Cash::where('id', $id)->where('user_id', Auth::id())->first();

        $item->in_budget = (int)$request->in_budget;

        $item->save();
    }

    public function currency($id)
    {
        $item = Currency::where('id', $id)->where('user_id', Auth::id())->get();

        if ($item == null)
        {
            return abort(404);
        }

        return view('edit.currency', ['id' => $id]);
    }

    public function currencydata($id)
    {
        $item = Currency::where('user_id', Auth::id())->where('id', $id)->get();

        $item[0]['symbol'] = \html_entity_decode($item[0]['symbol']);

        return response()->json(['item' => $item]);
    }

    public function savecurrency(Request $request, $id)
    {
        $item = Currency::where('id', $id)->where('user_id', Auth::id())->first();

        $item->name = (string)$request->name;
        $item->symbol = (string)$request->symbol;
        $item->active = (int)$request->active;

        $item->save();
    }

    public function activecurrency(Request $request, $id)
    {
        $item = Currency::where('id', $id)->where('user_id', Auth::id())->first();

        $item->active = (int)$request->active;

        $item->save();
    }

    public function piggybank($id)
    {
        $item = Piggybank::where('id', $id)->where('user_id', Auth::id())->get();

        if ($item == null)
        {
            return abort(404);
        }

        return view('edit.piggybank', ['id' => $id]);
    }

    public function piggybankdata($id)
    {
        $item = Piggybank::where('user_id', Auth::id())->where('id', $id)->get();

        return response()->json(['item' => $item]);
    }

    public function savepiggybank(Request $request, $id)
    {
        $item = Piggybank::where('id', $id)->where('user_id', Auth::id())->first();

        $item->name = (string)$request->name;
        $item->balance = (double)$request->balance;
        $item->target_summ = (double)$request->target_summ;
        $item->target_date = (string)$request->target_date;
        $item->currency = (int)$request->currency;
        $item->active = (int)$request->active;

        $item->save();
    }

    public function activepiggybank(Request $request, $id)
    {
        $item = Piggybank::where('id', $id)->where('user_id', Auth::id())->first();

        $item->active = (int)$request->active;

        $item->save();
    }
}

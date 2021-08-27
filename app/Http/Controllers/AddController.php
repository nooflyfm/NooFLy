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

class AddController extends Controller
{
    public function cash()
    {
        return view('add.cash');
    }

    public function createcash(Request $request)
    {
        $model = new Cash;

        $model->name = (string)$request->name;
        $model->start_balance = (double)$request->balance;
        $model->current_balance = (double)$request->balance;
        $model->currency = (int)$request->currency;
        $model->active = (int)$request->active;
        $model->in_budget = (int)$request->in_budget;
        $model->user_id = (int)Auth::id();

        $model->save();

        $model2 = new CashesBalances;

        $model2->cash_id = (int)$model->id;
        $model2->balance = (double)$request->balance;
        $model2->date = \date('Y-m-d');

        $model2->save();
    }

    public function currency()
    {
        return view('add.currency');
    }

    public function createcurrency(Request $request)
    {
        $model = new Currency;

        $model->name = (string)$request->name;
        $model->symbol = (string)\stripslashes($request->symbol);
        $model->active = (int)$request->active;
        $model->user_id = (int)Auth::id();

        $model->save();
    }

    public function piggybank()
    {
        return view('add.piggybank');
    }

    public function createpiggybank(Request $request)
    {
        $model = new Piggybank;

        $model->name = (string)$request->name;
        $model->balance = (double)$request->balance;
        $model->target_summ = (double)$request->target_summ;
        $model->target_date = (string)$request->target_date;
        $model->currency = (int)$request->currency;
        $model->active = (int)$request->active;
        $model->user_id = (int)Auth::id();

        $model->save();
    }

    public function transaction()
    {
        return view('add.transaction');
    }

    public function createtransaction(Request $request)
    {
        $model = new Transaction;

        $from_cash = Cash::where('user_id', Auth::id())->where('id', $request->from_cash)->first();
        $fc_balance = $from_cash['current_balance'];

        if ($request->type == 'income')
        {
            $new_fc_balance = ($fc_balance + (double)$request->summ);
        }
        if ($request->type == 'expense')
        {
            $new_fc_balance = ($fc_balance - (double)$request->summ);
        }
        if ($request->type == 'transfer')
        {
            $new_fc_balance = ($fc_balance - (double)$request->summ);
            
            $to_cash = Cash::where('user_id', Auth::id())->where('id', $request->to_cash)->first();
            
            $tc_balance = $to_cash['current_balance'];
            
            $new_tc_balance = ($tc_balance + (double)$request->summ);

            $to_cash->current_balance = (double)$new_tc_balance;

            $to_cash->save();

            $check_tc_balances = CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $request->to_cash)->first();

            if ($check_tc_balances !== null)
            {
                CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $request->to_cash)->update(['balance' => (double)$new_tc_balance]);
            } else {
                $to_cash_balances = new CashesBalances;

                $to_cash_balances->cash_id = (int)$request->to_cash;
                $to_cash_balances->balance = (double)$new_tc_balance;
                $to_cash_balances->date = \date('Y-m-d');

                $to_cash_balances->save();
            }
        }
        if ($request->type == 'piggybank')
        {
            $new_fc_balance = ($fc_balance - (double)$request->summ);
            
            $to_pbank = Piggybank::where('user_id', Auth::id())->where('id', $request->to_piggybank)->first();
            
            $tpb_balance = $to_pbank['balance'];
            
            $new_tpb_balance = ($tpb_balance + (double)$request->summ);

            $to_pbank->balance = (double)$new_tpb_balance;

            $to_pbank->save();
        }

        $from_cash->current_balance = (double)$new_fc_balance;

        $from_cash->save();

        $check_fc_balances = CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $request->from_cash)->first();

        if ($check_fc_balances !== null)
        {
            CashesBalances::where('date', date('Y-m-d'))->where('cash_id', $request->from_cash)->update(['balance' => (double)$new_fc_balance]);
        } else {

            $from_cash_balances = new CashesBalances;

            $from_cash_balances->cash_id = (int)$request->from_cash;
            $from_cash_balances->balance = (double)$new_fc_balance;
            $from_cash_balances->date = \date('Y-m-d');

            $from_cash_balances->save();
        }

        $cat = TransactionCategory::where('user_id', Auth::id())->where('name', $request->category)->first();

        if ($cat == null)
        {
            $catmodel = new TransactionCategory;

            $catmodel->name = (string)$request->category;
            $catmodel->user_id = (int)Auth::id();

            $catmodel->save();
        }

        $model->name = (string)$request->name;
        $model->type = (string)$request->type;
        $model->summ = (double)$request->summ;
        $model->date = (string)$request->date;
        $model->category = (string)$request->category;
        $model->currency = (int)$request->currency;
        $model->from_cash = (int)$request->from_cash;
        $model->to_cash = (int)$request->to_cash;
        $model->to_piggybank = (int)$request->to_piggybank;
        $model->user_id = (int)Auth::id();

        $model->save();
    }
}

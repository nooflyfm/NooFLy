<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Budget;
use App\Models\Cash;
use App\Models\Currency;

class BudgetController extends Controller
{
    public $cashes_summs = [];

    public function index()
    {
        $curr = Currency::where('user_id', Auth::id())->first();
        if ($curr == null)
        {
            return view('budget', ['currency' => '0']);
        }
        return redirect("/budget/{$curr['id']}");
    }

    public function budget($currency_id)
    {
        $curr = Currency::where('id', $currency_id)->where('user_id', Auth::id())->first();
        if ($curr == null)
        {
            return abort(404);
        }
        return view('budget', ['currency' => $currency_id]);
    }

    public function cashes(Request $request, $currency_id)
    {
        $items = Cash::where('user_id', Auth::id())->where('currency', $currency_id)->where('active', '1')->where('in_budget', '1')->get();

        $ritems = [];

        $summs = 0;

        $curr_symbol = '';

        foreach ($items as $item)
        {
            $summs += $item['current_balance'];
            $curr = Currency::where('user_id', Auth::id())->where('id', $item['currency'])->first();
            $item['curr_symbol'] = \html_entity_decode($curr['symbol']);
            $curr_symbol = $item['curr_symbol'];
            $ritems[] = $item;
        }

        return response()->json(['items' => $ritems, 'income_summ' => $summs, 'curr_symbol' => $curr_symbol]);
    }

    public function budgetdata(Request $request, $currency_id)
    {
        $items = Budget::where('user_id', Auth::id())->where('currency', $currency_id)->orderBy('summ')->get();

        $ritems = [];

        $summs = 0;

        foreach ($items as $item)
        {
            $summs += $item['summ'];
            $ritems[] = $item;
        }

        return response()->json(['items' => $ritems, 'expense_summ' => $summs]);
    }

    public function createcategory(Request $request, $currency_id)
    {
        $model = new Budget;

        $model->category = (string)$request->category;
        $model->summ = (double)$request->summ;
        $model->currency = (int)$currency_id;
        $model->user_id = (int)Auth::id();

        $model->save();
    }

    public function removecategory(Request $request, $currency_id, $id)
    {
        $item = Budget::where('user_id', Auth::id())->where('id', $id)->first();
        
        $item->delete();
    }
}

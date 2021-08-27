<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cash;
use App\Models\Currency;
use App\Models\Piggybank;
use App\Models\Transaction;
use App\Models\TransactionCategory;

class ListController extends Controller
{
    public function cashes()
    {
        return view('list.cashes');
    }

    public function cashesdata(Request $request)
    {
        $whereArr = ['user_id' => Auth::id()];
        if ($request->query('active') == '1')
        {
            $whereArr['active'] = '1';
        }
        if ($request->query('currency') != null)
        {
            $whereArr['currency'] = $request->query('currency');
        }
        
        $order_field = 'name';
        $order_type = 'ASC';
        if (!empty($request->query('sort')))
        {
            $order = explode('__', $request->query('sort'));
            $order_field = $order[0];
            $order_type = $order[1];
        }

        $items = Cash::where($whereArr)->orderBy($order_field, $order_type)->offset($request->query('offset'))->limit($request->query('limit'))->get();
        $total = Cash::where($whereArr)->count();

        $ritems = [];

        foreach ($items as $item)
        {
            $curr = Currency::where('user_id', Auth::id())->where('id', $item['currency'])->first();
            $item['curr_symbol'] = \html_entity_decode($curr['symbol']);
            $item['sort'] = $order_type . $order_field;
            $ritems[] = $item;
        }

        return response()->json(['items' => $ritems, 'total' => $total]);
    }

    public function currencies()
    {
        return view('list.currencies');
    }

    public function currenciesdata(Request $request, $onlyactive)
    {
        if ($onlyactive == '1')
        {
            $items = Currency::where('user_id', Auth::id())->where('active', '1')->get();
        } else {
            $items = Currency::where('user_id', Auth::id())->get();
        }

        $ritems = [];

        foreach ($items as $item)
        {
            $in_use = 0;
            $cashes = Cash::where('user_id', Auth::id())->where('currency', $item['id'])->first();
            if ($cashes !== null)
            {
                $in_use = 1;
            }
            $piggybanks = Piggybank::where('user_id', Auth::id())->where('currency', $item['id'])->first();
            if ($piggybanks !== null)
            {
                $in_use = 1;
            }
            $item['in_use'] = $in_use;
            $item['symbol'] = \html_entity_decode($item['symbol']);
            $ritems[] = $item;
        }

        $total = Currency::where('user_id', Auth::id())->count();

        return response()->json(['items' => $ritems, 'total' => $total]);
    }

    public function piggybanks()
    {
        return view('list.piggybanks');
    }

    public function piggybanksdata(Request $request)
    {
        $whereArr = ['user_id' => Auth::id()];
        if ($request->query('active') == '1')
        {
            $whereArr['active'] = '1';
        }
        if ($request->query('currency') != null)
        {
            $whereArr['currency'] = $request->query('currency');
        }

        $order_field = 'name';
        $order_type = 'ASC';
        if (!empty($request->query('sort')))
        {
            $order = explode('__', $request->query('sort'));
            $order_field = $order[0];
            $order_type = $order[1];
        }

        $items = Piggybank::where($whereArr)->orderBy($order_field, $order_type)->offset($request->query('offset'))->limit($request->query('limit'))->get();
        $total = Piggybank::where($whereArr)->count();

        $ritems = [];

        foreach ($items as $item)
        {
            $curr = Currency::where('user_id', Auth::id())->where('id', $item['currency'])->first();
            $item['curr_symbol'] = \html_entity_decode($curr['symbol']);
            $item['percent'] = \round(100 - ((($item['target_summ'] - $item['balance']) * 100) / $item['target_summ']));
            $date1 = \date_create(\date('Y-m-d'));
            $date2 = \date_create($item['target_date']);
            $interval = date_diff($date1, $date2);
            $item['days_left'] = (int)$interval->format('%a');
            $ritems[] = $item;
        }

        return response()->json(['items' => $ritems, 'total' => $total]);
    }

    public function transactions()
    {
        return view('list.transactions');
    }

    public function transactionsdata(Request $request)
    {
        $whereArr = ['user_id' => Auth::id()];

        $order_field = 'name';
        $order_type = 'ASC';
        if (!empty($request->query('sort')))
        {
            $order = explode('__', $request->query('sort'));
            $order_field = $order[0];
            $order_type = $order[1];
        }

        $items = Transaction::where($whereArr)->orderBy($order_field, $order_type)->offset($request->query('offset'))->limit($request->query('limit'))->get();
        $total = Transaction::where($whereArr)->count();

        $ritems = [];

        foreach ($items as $item)
        {
            $curr = Currency::where('user_id', Auth::id())->where('id', $item['currency'])->first();
            $item['curr_symbol'] = \html_entity_decode($curr['symbol']);
            $item['type'] = \ucfirst($item['type']);
            $from_cash = Cash::where('user_id', Auth::id())->where('id', $item['from_cash'])->first();
            $item['from_cash_name'] = ($from_cash != null) ? $from_cash['name'] : '-';
            $to_cash = Cash::where('user_id', Auth::id())->where('id', $item['to_cash'])->first();
            $item['to_cash_name'] = ($to_cash != null) ? $to_cash['name'] : '-';
            $to_piggybank = Piggybank::where('user_id', Auth::id())->where('id', $item['to_piggybank'])->first();
            $item['to_piggybank_name'] = ($to_piggybank != null) ? $to_piggybank['name'] : '-';
            $ritems[] = $item;
        }

        return response()->json(['items' => $ritems, 'total' => $total]);
    }

    public function transcategorydata(Request $request)
    {
        $items = TransactionCategory::where('user_id', Auth::id())->get();
        $total = TransactionCategory::where('user_id', Auth::id())->count();

        $ritems = [];

        return response()->json(['items' => $items, 'total' => $total]);
    }
}

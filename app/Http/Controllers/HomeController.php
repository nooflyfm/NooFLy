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

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public $colors = [
        '#ff0000' => 'rgba(255,0,0,0.5)', // red
        '#0026ff' => 'rgba(0,38,255,0.5)', // blue
        '#0ed300' => 'rgba(14,211,0,0.5)', // green
        '#fff600' => 'rgba(255,246,0,0.5)', // yellow
        '#00c7ff' => 'rgba(0,199,255,0.5)', // light blue
        '#00ff94' => 'rgba(0,255,148,0.5)', // cyan
        '#bf00ff' => 'rgba(191,0,255,0.5)', // violet
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function cashesbalances(Request $request)
    {
        $items = Currency::where('user_id', Auth::id())->where('active', '1')->get();

        $ritems = [];

        $balances_labels = [];
        $balances_data = [];

        $ritems['labels'] = [];
        $ritems['datasets'] = [];

        foreach ($items as $item)
        {
            $cashes = Cash::where('user_id', Auth::id())->where('active', '1')->where('currency', $item['id'])->get();
            if ($cashes !== null)
            {
                foreach ($cashes as $cash)
                {
                    $balances = CashesBalances::where('cash_id', $cash['id'])->orderby('date')->limit(30)->get();
                    if ($balances !== null)
                    {
                        foreach ($balances as $balance)
                        {
                            $ritems['labels'][] = $balance['date'];
                            $balances_data[$cash['id']][] = $balance['balance'];
                        }
                    }
                    $colors = array_rand($this->colors);
                    $ritems['datasets'][] = [
                        'label' => $cash['name'] . ' (' . \html_entity_decode($item['symbol']) . ')',
                        'backgroundColor' => $this->colors[$colors],
                        'borderColor' => $colors,
                        'pointBackgroundColor' => $this->colors[$colors],
                        'pointBorderColor' => $colors,
                        'data' => $balances_data[$cash['id']],
                    ];
                    $ritems['curr_symbol'][] = \html_entity_decode($item['symbol']);
                }
            }
        }
        $ritems['labels'] = \array_merge(\array_unique($ritems['labels']), array());

        return response()->json(['items' => $ritems]);
    }

    public function counts()
    {
        $whereArr1 = ['user_id' => Auth::id(), 'active' => '1'];
        $cashes = Cash::where($whereArr1)->count();

        $whereArr2 = ['user_id' => Auth::id(), 'active' => '1'];
        $piggybanks = Piggybank::where($whereArr2)->count();

        $whereArr3 = ['user_id' => Auth::id(), 'active' => '1'];
        $currencies = Currency::where($whereArr3)->count();

        $whereArr4 = ['user_id' => Auth::id()];
        $transactions = Transaction::where($whereArr4)->count();

        $items = [
            'cashes' => (int)$cashes,
            'piggybanks' => (int)$piggybanks,
            'currencies' => (int)$currencies,
            'transactions' => (int)$transactions,
        ];

        return response()->json(['items' => $items]);
    }

    public function cashesamounts()
    {
        $items = Currency::where('user_id', Auth::id())->where('active', '1')->get();

        $ritems = [];

        foreach ($items as $item)
        {
            $summ = Cash::where('user_id', Auth::id())->where('active', '1')->where('currency', $item['id'])->sum('current_balance');

            $ritems[] = [
                'currency' => $item['name'],
                'summ' => \html_entity_decode($item['symbol']) . (double)$summ
            ];
        }
        return response()->json(['items' => $ritems]);
    }

    public function transactionscategories()
    {
        $items = TransactionCategory::get();

        $ritems = [];

        $labels = [];
        $data = [];

        $ritems['labels'] = [];

        foreach ($items as $item)
        {
            $count = Transaction::where('user_id', Auth::id())->where('category', $item['name'])->count();

            $labels[] = $item['name'];

            $data[] = $count;
            
            $ritems['labels'] = \array_unique($labels);
        }

        $colors = array_rand($this->colors);

        $ritems['datasets'][] = [
            'label' => 'Transactions amount',
            'backgroundColor' => $this->colors[$colors],
            'borderColor' => $colors,
            'pointBackgroundColor' => $colors,
            'pointBorderColor' => $colors,
            'data' => (array)$data,
        ];

        $ritems['datasets'] = \array_merge($ritems['datasets'], array());

        return response()->json(['items' => $ritems]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cash;
use App\Models\CashesBalances;
use App\Models\Currency;
use App\Models\Piggybank;

class RemoveController extends Controller
{
    public function removecash(Request $request, $id)
    {
        $item = Cash::where('user_id', Auth::id())->where('id', $id)->first();
        
        $item->delete();

        CashesBalances::where('cash_id', $id)->delete();
    }

    public function removecurrency(Request $request, $id)
    {
        $item = Currency::where('user_id', Auth::id())->where('id', $id)->first();
        
        $item->delete();
    }

    public function removepiggybank(Request $request, $id)
    {
        $item = Piggybank::where('user_id', Auth::id())->where('id', $id)->first();
        
        $item->delete();
    }
}

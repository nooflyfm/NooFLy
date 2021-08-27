<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\BannedUsers;
use App\Models\Cash;
use App\Models\CashesBalances;
use App\Models\Currency;
use App\Models\Piggybank;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\Budget;

class AdminController extends Controller
{
    use RegistersUsers;

    public function index()
    {
        if (env('ADMIN_USER_ID') != Auth::id() || env('AS_SERVICE') != '1')
        {
            return abort('404');
        }
        return view('admin.index');
    }

    public function counts()
    {
        if (env('ADMIN_USER_ID') != Auth::id() || env('AS_SERVICE') != '1')
        {
            return false;
        }

        $cashes = Cash::count();

        $cashes_balances = CashesBalances::count();

        $piggybanks = Piggybank::count();

        $currencies = Currency::count();

        $transactions = Transaction::count();

        $users = User::count();

        $items = [
            'cashes' => (int)$cashes,
            'cashes_balances' => (int)$cashes_balances,
            'piggybanks' => (int)$piggybanks,
            'currencies' => (int)$currencies,
            'transactions' => (int)$transactions,
            'users' => (int)$users,
        ];

        return response()->json(['items' => $items]);
    }

    public function userslist(Request $request)
    {
        if (env('ADMIN_USER_ID') != Auth::id() || env('AS_SERVICE') != '1')
        {
            return false;
        }

        $order_field = 'id';
        $order_type = 'ASC';
        if (!empty($request->query('sort')))
        {
            $order = explode('__', $request->query('sort'));
            $order_field = $order[0];
            $order_type = $order[1];
        }

        $query = User::where('id', '!=', Auth::id())->where('id', '!=', env('ADMIN_USER_ID'))->orderBy($order_field, $order_type)->offset($request->query('offset'))->limit($request->query('limit'));

        if (!empty($request->query('name')))
        {
            $query->where('name', 'like', "%{$request->query('name')}%");
        }

        if (!empty($request->query('email')))
        {
            $query->where('email', 'like', "%{$request->query('email')}%");
        }

        $items = $query->get();

        $ritems = [];

        foreach ($items as $item)
        {
            $check_ban = BannedUsers::where('user_id', $item['id'])->where('is_banned', '1')->first();

            $item['is_banned'] = ($check_ban !== null) ? true : false;

            $where = ['user_id' => $item['id']];

            $item['cashes'] = Cash::where($where)->count();

            $item['piggybanks'] = Piggybank::where($where)->count();

            $item['currencies'] = Currency::where($where)->count();

            $item['transactions'] = Transaction::where($where)->count();

            $ritems[] = $item;
        }

        $total = User::where('id', '!=', Auth::id())->where('id', '!=', env('ADMIN_USER_ID'))->count();

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function toggleuserban(Request $request, $id)
    {
        if (env('DEMO_MODE') == '1')
        {
            return;
        }

        if (env('ADMIN_USER_ID') != Auth::id() || env('AS_SERVICE') != '1')
        {
            return false;
        }

        $check_ban = BannedUsers::where('user_id', $id)->first();

        if ($check_ban == null)
        {
            if ($request->status == '1')
            {
                $model = new BannedUsers;
                $model->user_id = (int)$id;
                $model->is_banned = '1';
                $model->save();
            }
        } else {
            $check_ban->is_banned = ($request->status == '1') ? '1' : '0';
            $check_ban->save();
        }
    }

    public function removeuser(Request $request, $id)
    {
        if (env('DEMO_MODE') == '1')
        {
            return;
        }

        if (env('ADMIN_USER_ID') != Auth::id() || env('AS_SERVICE') != '1')
        {
            return false;
        }

        Transaction::where('user_id', $id)->delete();

        TransactionCategory::where('user_id', $id)->delete();

        Piggybank::where('user_id', $id)->delete();

        Currency::where('user_id', $id)->delete();

        Budget::where('user_id', $id)->delete();

        $cashes = Cash::where('user_id', $id)->get();

        if ($cashes !== null)
        {
            foreach ($cashes as $cash)
            {
                Cash::where('id', $cash['id'])->delete();
                CashesBalances::where('cash_id', $cash['id'])->delete();
            }
        }

        BannedUsers::where('user_id', $id)->delete();
        
        User::destroy($id);
    }

    public $month_days = [
        '01' => '31',
        '02' => '28',
        '03' => '31',
        '04' => '30',
        '05' => '31',
        '06' => '30',
        '07' => '31',
        '08' => '31',
        '09' => '30',
        '10' => '31',
        '11' => '30',
        '12' => '31'
    ];

    function is_leap_year($year)
    {
        return \date('L', \strtotime("$year-01-01")) ? true : false;
    }

    protected function month_days($year, $month)
    {
        foreach ($this->month_days as $m => $d)
        {
            if ($month == $m)
            {
                if ($this->is_leap_year($year) && $month == '02')
                {
                    return '29';
                }
                return $d;
            }
        }
    }

    public function usersregsmonth(Request $request)
    {
        $ritems = [];

        $data = [];

        $ritems['labels'] = [];

        for ($i = 1; $i <=$this->month_days(\date('Y'), \date('m')); $i++)
        {
            $day = ($i < 10) ? '0' . $i : $i;
            $date = \date('Y-m') . '-' . $day;
            $ritems['labels'][] = $date;

            $data[] = User::where('created_at', 'like', "%{$date}%")->count();
        }

        $ritems['datasets'] = [
            'label' => 'Registrations amount',
            'backgroundColor' => 'rgba(0,199,255,0.5)',
            'borderColor' => '#00c7ff',
            'pointBackgroundColor' => 'rgba(0,199,255,0.5)',
            'pointBorderColor' => '#00c7ff',
            'data' => $data,
        ];

        $ritems['datasets'] = (object)$ritems['datasets'];

        return response()->json(['items' => $ritems]);
    }

    public function usersregsyear(Request $request)
    {
        $ritems = [];

        $data = [];

        $ritems['labels'] = [];

        for ($i = 1; $i <=12; $i++)
        {
            $month = ($i < 10) ? '0' . $i : $i;
            $date = \date('Y') . '-' . $month;
            $ritems['labels'][] = $date;

            $data[] = User::where('created_at', 'like', "%{$date}%")->count();
        }

        $ritems['datasets'] = [
            'label' => 'Registrations amount',
            'backgroundColor' => 'rgba(255,246,0,0.5)',
            'borderColor' => '#fff600',
            'pointBackgroundColor' => 'rgba(255,246,0,0.5)',
            'pointBorderColor' => '#fff600',
            'data' => $data,
        ];

        $ritems['datasets'] = (object)$ritems['datasets'];

        return response()->json(['items' => $ritems]);
    }

    // API Start

    public function createuser(Request $request)
    {
        if (env('DEMO_MODE') == '1')
        {
            return response()->json(['success' => false, 'error' => 'Demo mode is enabled'], 403);
        }

        if (env('AS_SERVICE') != '1')
        {
            return response()->json(['success' => false, 'error' => 'System does not works as a service'], 403);
        }

        if (empty($request->admin_email))
        {
            return response()->json(['success' => false, 'error' => 'admin_email need in POST'], 400);
        }

        if (empty($request->admin_password))
        {
            return response()->json(['success' => false, 'error' => 'admin_password need in POST'], 400);
        }

        if (empty($request->name))
        {
            return response()->json(['success' => false, 'error' => 'name need in POST'], 400);
        }

        if (empty($request->email))
        {
            return response()->json(['success' => false, 'error' => 'email need in POST'], 400);
        }

        if (empty($request->password))
        {
            return response()->json(['success' => false, 'error' => 'password need in POST'], 400);
        }

        $get_admin = User::where('email', $request->admin_email)->first();

        if ($get_admin == null || $get_admin['id'] != env('ADMIN_USER_ID') || !Hash::check($request->admin_password, $get_admin['password']))
        {
            return response()->json(['success' => false, 'error' => 'Error auth the admin'], 401);
        }

        $rules = [
            //'name' => 'required',
            'email'    => 'unique:users',
            //'password' => 'required',
        ];

        $input     = $request->only('name', 'email', 'password');
        
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'The email has already been taken'], 422);
        }

        $name     = $request->name;
        $email    = $request->email;
        $password = $request->password;
        $user     = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

        return response()->json(['success' => true, 'message' => 'User was registered successfully'], 200);
    }

    public function banuser(Request $request, $status_id)
    {
        if (env('DEMO_MODE') == '1')
        {
            return response()->json(['success' => false, 'error' => 'Demo mode is enabled'], 403);
        }

        if (env('AS_SERVICE') != '1')
        {
            return response()->json(['success' => false, 'error' => 'System does not works as a service'], 403);
        }

        if (empty($request->admin_email))
        {
            return response()->json(['success' => false, 'error' => 'admin_email need in POST'], 400);
        }

        if (empty($request->admin_password))
        {
            return response()->json(['success' => false, 'error' => 'admin_password need in POST'], 400);
        }

        if (empty($request->user_id))
        {
            return response()->json(['success' => false, 'error' => 'user_id need in POST'], 400);
        }

        $get_admin = User::where('email', $request->admin_email)->first();

        if ($get_admin == null || $get_admin['id'] != env('ADMIN_USER_ID') || !Hash::check($request->admin_password, $get_admin['password']))
        {
            return response()->json(['success' => false, 'error' => 'Error auth the admin'], 401);
        }

        $check_ban = BannedUsers::where('user_id', $request->user_id)->first();

        if ($check_ban == null)
        {
            if ($status_id == '1')
            {
                $model = new BannedUsers;
                $model->user_id = (int)$request->user_id;
                $model->is_banned = '1';
                $model->save();
            }
        } else {
            $check_ban->is_banned = ($status_id == '1') ? '1' : '0';
            $check_ban->save();
        }

        return response()->json(['success' => true, 'message' => 'User ban status was changed successfully'], 200);
    }

    // API End
}

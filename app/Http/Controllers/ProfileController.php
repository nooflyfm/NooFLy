<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('users.profile', ['user' => $user]);
    }

    public function save(Request $request)
    {
        if (env('DEMO_MODE') == '1')
        {
            return;
        }
        
        $item = User::find(Auth::id());

        $item->name = (string)$request->name;
        $item->email = (string)$request->email;
        if (!empty($request->password))
        {
            $item->password = Hash::make($request->password);
        }

        $item->save();
    }
}

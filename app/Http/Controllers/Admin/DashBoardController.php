<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Factory\Paypal\Paypal;

class DashBoardController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $paypal = new Paypal();
        return $paypal->DirectPayment();
        return view('admin.pages.dashboard');
    }
}

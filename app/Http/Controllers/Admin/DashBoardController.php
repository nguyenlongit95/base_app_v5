<?php

namespace App\Http\Controllers\Admin;

use App\Factory\NganLuong\NganLuong;
use App\Factory\Paygates\VNPAY\VNPAY;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
//        $paypal = new Paypal();
//        return $paypal->DirectPayment();


        return view('admin.pages.dashboard');
    }

    /**
     * Function do direct payment
     *
     * @param Request $request
     */
    public function doDirectPayment(Request $request)
    {
        $param = array();
        $param['txh_name'] = 'Nguyen Long';
        $param['txt_email'] = 'nguyenlongit95@gmail.com';
        $param['txt_phone'] = '0393803548';
        $param['price'] = '2000';
        $nganLuong = new NganLuong();
        $doPayment = $nganLuong->DirectPayment($param);
        return $doPayment;
    }

    /**
     * Function success payment
     *
     * @param Request $request
     */
    public function success(Request $request)
    {
        $param = $request->all();
        $nganluong = new NganLuong();
        $success = $nganluong->success($param);
        dd($success);
    }

    public function vnPay(Request $request)
    {
        $vnPay = new VNPAY();
        $param = array(); // vnp_OrderInfo, vnp_OrderType, vnp_Amount
        $param['vnp_OrderInfo'] = 'Test thanh toan tren VNPay';
        $param['vnp_OrderType'] = '100000'; // Thuc pham tieu dung
        $param['vnp_Amount'] = 10000;
        $payment = $vnPay->directPayment($param);
        dd($payment);
    }
}

<?php

namespace App\Factory\Paygates\NganLuong;

use App\Factory\Paygates\NganLuong\NL_Checkout;
use DB;

class NganLuong {

    private $NGANLUONG_URL = 'https://www.nganluong.vn/checkout.php';
    private $RECEIVER = 'demo@nganluong.vn';
    private $MERCHANT_ID = '36680';
    private $MERCHANT_PASS = 'matkhauketnoi';

    /**
     * NganLuong constructor.
     */
    public function __construct()
    {

    }

    /**
     * Function direct payment
     *
     * @param array $param
     */
    public function directPayment($param)
    {
        $receiver = $this->RECEIVER;
        //Mã đơn hàng
        $order_code = 'NL_' . time();
        //Khai báo url trả về
        $return_url = url('/admin/ngan-luong/success');
        // Link nut hủy đơn hàng
        $cancel_url = url('');
        $notify_url = url('');
        //Giá của cả giỏ hàng
        $txh_name = $param['txh_name'];
        $txt_email = $param['txt_email'];
        $txt_phone = $param['txt_phone'];
        $price = (int)$param['price'];
        //Thông tin giao dịch
        $transaction_info = "Thong tin giao dich";
        $currency = "vnd";
        $quantity = 1;
        $tax = 0;
        $discount = 0;
        $fee_cal = 0;
        $fee_shipping = 0;
        $order_description = "Thong tin don hang: " . $order_code;
        $buyer_info = $txh_name . "*|*" . $txt_email . "*|*" . $txt_phone;
        $affiliate_code = "";
        //Khai báo đối tượng của lớp NL_Checkout
        $nl = new NL_Checkout();
        $nl->nganluong_url = $this->NGANLUONG_URL;
        $nl->merchant_site_code = $this->MERCHANT_ID;
        $nl->secure_pass = $this->MERCHANT_PASS;
        //Tạo link thanh toán đến nganluong.vn
        $url = $nl->buildCheckoutUrlExpand($return_url, $receiver, $transaction_info, $order_code, $price, $currency,
            $quantity, $tax, $discount, $fee_cal, $fee_shipping, $order_description, $buyer_info, $affiliate_code);
        //$url= $nl->buildCheckoutUrl($return_url, $receiver, $transaction_info, $order_code, $price);

        //echo $url; die;
        if ($order_code != "") {
            //một số tham số lưu ý
            //&cancel_url=http://yourdomain.com --> Link bấm nút hủy giao dịch
            //&option_payment=bank_online --> Mặc định forcus vào phương thức Ngân Hàng
            $url .= '&cancel_url=' . $cancel_url . '&notify_url=' . $notify_url;
            //$url .='&option_payment=bank_online';

            echo '<meta http-equiv="refresh" content="0; url=' . $url . '" >';
            //&lang=en --> Ngôn ngữ hiển thị google translate
        }
    }

    /**
     * Function payment success has payment to Ngan Luong
     *
     * @param array $param
     * @return unknown|bool
     */
    public function success($param)
    {
        // Lấy các tham số để chuyển sang Ngânlượng thanh toán:
        $transaction_info = $param['transaction_info'];
        $order_code = $param['order_code'];
        $price = $param['price'];
        $payment_id = $param['payment_id'];
        $payment_type = $param['payment_type'];
        $error_text = $param['error_text'];
        $secure_code = $param['secure_code'];
        //Khai báo đối tượng của lớp NL_Checkout
        $nl= new NL_Checkout();
        $nl->merchant_site_code = $this->MERCHANT_ID;
        $nl->secure_pass = $this->MERCHANT_PASS;
        //Tạo link thanh toán đến nganluong.vn
        $checkpay= $nl->verifyPaymentUrl($transaction_info, $order_code, $price, $payment_id, $payment_type, $error_text, $secure_code);

        if ($checkpay) {
            // bạn viết code vào đây để cung cấp sản phẩm cho người mua
            return $checkpay;
        } else {
            return false;
        }
    }
}


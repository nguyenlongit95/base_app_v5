<?php

namespace App\Factory\Paygates\VNPAY;
use DB;

class VNPAY
{
    private $vnp_Url = null;
    private $vnp_Returnurl = null;
    private $vnp_TmnCode = "";      // Mã website tại VNPAY
    private $vnp_HashSecret = "";   // Chuỗi bí mật

    /**
     * VNPAY constructor.
     */
    public function __construct()
    {
        $this->vnp_Url = "http://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $this->vnp_Returnurl = "http://localhost/vnpay_php/vnpay_return.php";
        $this->vnp_TmnCode = "";
        $this->vnp_HashSecret = "";
    }

    /**
     * Function payment to VNPAY
     *
     * @param array $param: vnp_OrderInfo, vnp_OrderType, vnp_Amount
     * @return string
     */
    public function directPayment($param)
    {
        $vnp_OrderInfo = $param['vnp_OrderInfo'];
        $vnp_OrderType = $param['vnp_OrderType'];
        $vnp_Amount = $param['vnp_Amount'] * 100;

        $vnp_TxnRef = date('YmdHis');           // Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        $inputData = array(
            "vnp_Version" => "2.0.0",                   // Phiên bản api mà merchant kết nối. Phiên bản hiện tại là 2.0.0
            "vnp_Command" => "pay",                     // Mã API sử dụng, mã cho giao dịch thanh toán là "pay"; Alpha[1,16]
            "vnp_TmnCode" => $this->vnp_TmnCode,        // Mã website của merchant trên hệ thống của VNPAY. Ví dụ: 2QXUI4J4
            "vnp_Amount" => $vnp_Amount,                // Số tiền thanh toán. Số tiền không mang các ký tự phân tách thập phân, phần nghìn, ký tự tiền tệ. Để gửi số tiền thanh toán là 10,000 VND (mười nghìn VNĐ) thì merchant cần nhân thêm 100 lần (khử phần thập phân), sau đó gửi sang VNPAY là: 1000000
            "vnp_CreateDate" => date('YmdHis'), // Ví dụ: 20170829103111
            "vnp_CurrCode" => "VND",                    // Đơn vị tiền tệ sử dụng thanh toán. Hiện tại chỉ hỗ trợ VND
            "vnp_IpAddr" => $vnp_IpAddr,                // Địa chỉ IP của khách hàng thực hiện giao dịch. Ví dụ: 13.160.92.202
            "vnp_Locale" => $vnp_Locale,                // Ngôn ngữ giao diện hiển thị. Hiện tại hỗ trợ Tiếng Việt (vn), Tiếng Anh (en)
            "vnp_OrderInfo" => $vnp_OrderInfo,          // Thông tin mô tả nội dung thanh toán (Tiếng Việt, không dấu). Ví dụ: **Nap tien cho thue bao 0123456789. So tien 100,000 VND**
            "vnp_OrderType" => $vnp_OrderType,          // Mã danh mục hàng hóa. Mỗi hàng hóa sẽ thuộc một nhóm danh mục do VNPAY quy định. Xem thêm bảng Danh mục hàng hóa
            "vnp_ReturnUrl" => $this->vnp_Returnurl,    // URL thông báo kết quả giao dịch khi Khách hàng kết thúc thanh toán. Ví dụ: http://domain.vn/VnPayReturn
            "vnp_TxnRef" => $vnp_TxnRef,                // Mã tham chiếu của giao dịch tại hệ thống của merchant. Mã này là duy nhất đùng để phân biệt các đơn hàng gửi sang VNPAY. Không được trùng lặp trong ngày. Ví dụ: 23554
        );
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $this->vnp_Url . "?" . $query;
        if (isset($this->vnp_HashSecret)) {
            $vnpSecureHash = hash('sha256',$this->vnp_HashSecret . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Function get result data from vnpay
     *
     * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
     * Các bước thực hiện:
     * Kiểm tra checksum
     * Tìm giao dịch trong database
     * Kiểm tra tình trạng của giao dịch trước khi cập nhật
     * Cập nhật kết quả vào Database
     * Trả kết quả ghi nhận lại cho VNPAY
     * */
    public function success()
    {
        $inputData = array();
        $returnData = array();
        $data = $_REQUEST;
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . $key . "=" . $value;
            } else {
                $hashData = $hashData . $key . "=" . $value;
                $i = 1;
            }
        }
        $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
        $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
        $secureHash = hash('sha256',$this->vnp_HashSecret . $hashData);
        $Status = 0;
        $orderId = $inputData['vnp_TxnRef'];

        try {
            // Check Orderid
            // Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                // Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId
                // Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
                // Giả sử: $order = mysqli_fetch_assoc($result);
                $order = NULL;
                if ($order != NULL) {
                    if ($order["Status"] != NULL && $order["Status"] == 0) {
                        if ($inputData['vnp_ResponseCode'] == '00') {
                            $Status = 1;
                        } else {
                            $Status = 2;
                        }
                        // Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                        // Update database
                        //
                        //
                        // Trả kết quả về cho VNPAY: Website TMĐT ghi nhận yêu cầu thành công
                        $returnData['RspCode'] = '00';
                        $returnData['Message'] = 'Confirm Success';
                    } else {
                        $returnData['RspCode'] = '02';
                        $returnData['Message'] = 'Order already confirmed';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Chu ky khong hop le';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }

        // Trả lại VNPAY theo định dạng JSON
        return json_encode($returnData);
    }
}

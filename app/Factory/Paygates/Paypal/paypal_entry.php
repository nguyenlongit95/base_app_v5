<?php

namespace App\Factory\Paygates\Paypal;

use Illuminate\Support\Facades\Log;
use App\Factory\Paygates\Paypal\CallerService;

/*** Get required parameters from the web form for the request ***/

class paypal_entry {

    public $VERSION = '53.0';

    /**
     * function action payment
     *
     * @param array $param
     * @return bool
     */
    public function directPayment($param) {
        $paymentType = urlencode($param['paymentType']);
        $firstName = urlencode($param['fname']);
        $lastName = urlencode($param['lname']);
        $creditCardType = urlencode($param['creditCardType']);
        $creditCardNumber = urlencode($param['creditCardNumber']);
        $expDateMonth = urlencode($param['expDateMonth']);

        //Month must be padded with leading zero
        $padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);

        $expDateYear =urlencode( $param['expDateYear']);
        $cvv2Number = urlencode($param['cvv2Number']);
        $amount = urlencode($param['ftotal']);
        $currencyCode="USD";

        /**
         * Construct the request string that will be sent to PayPal.
         * The variable $nvpstr contains all the variables and is a
         * name value pair string with & as a delimiter
         */

        $nvpstr="&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber&EXPDATE=".$padDateMonth.$expDateYear."&CVV2=$cvv2Number&FIRSTNAME=$firstName&COUNTRYCODE=US&CURRENCYCODE=$currencyCode";

        /**
         * Make the API call to PayPal, using API signature.
         * The API response is stored in an associative array called $resArray
         */
        $callService = new CallerService();
        $resArray = $callService->hash_call("doDirectPayment",$nvpstr);

        /**
         * Display the API response back to the browser.
         * If the response from PayPal was a success, display the response parameters'
         * If the response was an error, display the errors received using APIError.php.
         */
        $ack = strtoupper($resArray["ACK"]);

        if($ack!="SUCCESS")  {
            // flag = 2 has Transaction Declined
            $_SESSION['reshash']=$resArray;
            Log::error($resArray);
            return false;
        }
        if($ack=="SUCCESS")  {
            // Transaction success
            $_SESSION['reshash']="SUCCESS";
            Log::error($resArray);
            return true;
        }
    }
}

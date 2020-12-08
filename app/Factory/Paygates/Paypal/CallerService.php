<?php

namespace App\Factory\Paygates\Paypal;
use DB;

/**
 * CallerService.php
 *
 * This file uses the constants.php to get parameters needed to make an API call and calls the server.if you want use your own credentials, you have to change the constants.php
 *
 * Called by TransactionDetails.php, ReviewOrder.php, DoDirectPaymentReceipt.php and DoExpressCheckoutPayment.php.
 */
class CallerService {

    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @methodName is name of API  method.
     * @nvpStr is nvp string.
     * returns an associtive array containing the response from the server.
     */
    private $API_USERNAME = null;
    private $API_PASSWORD = null;
    private $API_SIGNATURE = null;
    private $VERSION = null;
    private $API_ENDPOINT = 'https://api-3t.paypal.com/nvp';
    private $USE_PROXY = FALSE;
    private $PROXY_HOST = '127.0.0.1';
    private $PROXY_PORT = '808';

    /**
     * Define the PayPal URL. This is the URL that the buyer is first sent to to authorize payment with their paypal account change the URL depending if you are testing on the sandbox
     * or going to the live PayPal site
     * For the sandbox, the URL is    :  https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=
     * For the live site, the URL is  :  https://www.paypal.com/webscr&cmd=_express-checkout&token=
     */
    private $PAYPAL_URL = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=';
    private $USERACTION_FLAG = false;

    /**
     * construction function and generate config param
     *
     * CallerService constructor.
     */
    public function __construct()
    {
        $paypal = DB::table('paygates')->where('code', 'paypal')->first();
        if (!empty($paypal)) {
            $configs = json_decode($paypal->configs, true);
            $this->API_USERNAME = $configs['API_USERNAME'];
            $this->API_PASSWORD = $configs['API_PASSWORD'];
            $this->API_SIGNATURE = $configs['API_SIGNATURE'];
            $this->VERSION = $configs['VERSION'];
        }
    }

    /**
     * Function began call to api paypal
     *
     * @param String $methodName
     * @param String $nvpStr
     * @return array
     */
    function hash_call($methodName, $nvpStr)
    {
        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->API_ENDPOINT);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        //if USE_PROXY constant set to TRUE in, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT
        if ($this->USE_PROXY) {
            curl_setopt($ch, CURLOPT_PROXY, $this->PROXY_HOST . ":" . $this->PROXY_PORT);
        }

        //NVPRequest for submitting to server
        $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this->VERSION) . "&PWD=" . urlencode($this->API_PASSWORD) . "&USER=" . urlencode($this->API_USERNAME) . "&SIGNATURE=" . urlencode($this->API_SIGNATURE) . $nvpStr;

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec($ch);

        //convrting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;

        if (curl_errno($ch)) {
            // moving to display page to display curl errors
            $_SESSION['curl_error_no'] = curl_errno($ch);
            $_SESSION['curl_error_msg'] = curl_error($ch);
            return $nvpResArray;
        } else {
            //closing the curl service
            curl_close($ch);
        }

        return $nvpResArray;
    }

    /**
     * This function will take NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     * @nvpstr is NVPString.
     * @nvpArray is Associative Array.
     */
    function deformatNVP($nvpstr)
    {
        $intial = 0;
        $nvpArray = array();

        while (strlen($nvpstr)) {
            //postion of Key
            $keypos = strpos($nvpstr, '=');
            //position of value
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

            /*getting the Key and Value values and storing in a Associative Array*/
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            //decoding the respose
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }

        return $nvpArray;
    }

    /**
     * Purpose: Redirects to PayPal.com site.
     *
     * @param string $token: nvp_string.
     * @return null
     */
    function RedirectToPayPal($token)
    {
        // Redirect to paypal.com here
        // With useraction=commit user will see "Pay Now" on Paypal website and when user clicks "Pay Now" and returns to our website we can call DoExpressCheckoutPayment API without asking the user
        $payPalURL = $this->PAYPAL_URL. $token;
        if($_SESSION['EXPRESS_MARK'] == 'ECMark'){
            $payPalURL = $payPalURL. '&useraction=commit';
        } else {
            if($this->USERACTION_FLAG)
                $payPalURL = $payPalURL. '&useraction=commit';
        }
        header("Location:".$payPalURL);

        return null;
    }
}

<?php

namespace App\Factory\Paygates\Paypal;
use DB;

class Paypal
{
    private $user;
    private $password;
    private $signature;
    private $apiVersion;
    private $currency;

    public $version='64.0';

    public function  __construct()
    {
        $payGate = DB::table('paygates')->where('code', 'paypal')->where('configs', '!=', null)->first();
        if (empty($payGate)) {
            return false;
        }

        $configs = json_decode($payGate->configs, true);
        $this->user = $configs['user'];
        $this->password = $configs['password'];
        $this->signature = $configs['signature'];
        $this->currency = $configs['currency'];
        $this->apiVersion = '1.0';
    }

    /**
     * Function do direct payment
     *
     * @param array $param: number of card, end date, CVV code, First name, last name, amount
     * @return bool|string
     */
    public function directPayment($param)
    {
        $request_params = [
            'METHOD' => 'DoDirectPayment',
            'USER' => $this->user,
            'PWD' => $this->password,
            'SIGNATURE' => $this->signature,
            'VERSION' => '65.1',
            'PAYMENTACTION' => 'Sale',
            'IPADDRESS' => '255.255.255.255',
            'RETURNFMFDETAILS' => 0,
            'CREDITCARDTYPE' => 'VISA',
            'ACCT' => $param['ACCT'],
            'EXPDATE' => $param['EXPDATE'],
            'CVV2' => $param['CVV2'],
            'FIRSTNAME' => 'Nguyen',
            'LASTNAME' => 'Long',
            'STREET' => 'Thanh Cong',
            'CITY' => 'Ha Noi',
            'STATE' => '00',
            'COUNTRYCODE' => 'VN',
            'ZIP' => '10000',
            'AMT' => $param['AMT'],
            'CURRENCYCODE' => 'USD',
            'DESC' => 'Check '
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp?');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php

        $nvpstr="&PAYMENTACTION=".$request_params['PAYMENTACTION']."&AMT=".$request_params['AMT']."&CREDITCARDTYPE=".$request_params['CREDITCARDTYPE']."&ACCT=".$request_params['ACCT']."&EXPDATE=".$request_params['EXPDATE']."&CVV2=".$request_params['CVV2']."&FIRSTNAME=".$request_params['FIRSTNAME']."&COUNTRYCODE=GB&CURRENCYCODE=".$request_params['CURRENCYCODE']."";
        //NVPRequest for submitting to server
        $nvpreq="METHOD=".urlencode($request_params['METHOD'])."&VERSION=".urlencode('64.0')."&PWD=".urlencode($request_params['PWD'])."&USER=".urlencode($request_params['USER'])."&SIGNATURE=".urlencode($request_params['SIGNATURE']).$nvpstr;

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvpreq);

        //getting response from server
        $response = curl_exec($curl);

        return $response;
    }
}

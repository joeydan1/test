<?php
/*网银支付*/
namespace App\Providers;

class EbankPayReceive extends PayReceiveBase
{
    /*production*/
    const API_HOST = 'https://cashier.sandpay.com.cn/gateway/api';
    /*test*/
    //const API_HOST = 'https://61.129.71.103:8003/gateway/api';

    public function __construct($mode, $post)
    {
        parent::__construct();
        $this->mode = $mode;
        $this->params = EbankPayReceiveParams::loadParams($mode, $post);
    }


    public function execute()
    {
        if (isset($this->mode)){
            switch($this->mode){
                case 'query';
                    return $this->query();
                    break;
                case 'eBankPay';
                    return $this->orderPay();
                    break;
                case 'ebankPayNotify';
                    return $this->notify();
                    break;
            }
        }else{
            echo 'mdoe not set';
            return 'mode not set';
        }
    }


    public function query()
    {
        // step2: 私钥签名
        $sign = $this->sign($this->params, $this->priKey);

        // step3: 拼接post数据
        $post = array(
            'charset' => 'utf-8',
            'signType' => '01',
            'data' => json_encode($this->params),
            'sign' => $sign
        );

        // step4: post请求
        $result = $this->http_post_json(self::API_HOST . '/order/query', $post);
        $arr = $this->parse_result($result);

        //step5: 公钥验签
        try {
            $this->verify($arr['data'], $arr['sign'], $this->pubKey);
            return $arr['data'];
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $e->getMessage();
            //exit;
        }

    }

    public function orderPay()
    {
            // step2: 私钥签名
            $sign = $this->sign($this->params, $this->priKey);
            // step3: 拼接post数据
            $post = array(
                'charset' => 'utf-8',
                'signType' => '01',
                'data' => json_encode($this->params),
                'sign' => $sign
            );
            // step4: post请求
            try {
                $result = $this->http_post_json(self::API_HOST . '/order/pay', $post);
            }catch (\Exception $e) {
                return $e->getMessage();
                //exit;
            }

            $arr = $this->parse_result($result);

            //step5: 公钥验签
            // it will be done on base class constructor $pubkey = $this->loadX509Cert(PUB_KEY_PATH);
            //print_r($this->pubKey);

            try {
                $this->verify($arr['data'], $arr['sign'], $this->pubKey);
            } catch (\Exception $e) {
                return $e->getMessage();
                //exit;
            }

            // step6： 获取credential
            $data = json_decode($arr['data'], true);
            
            if ($data['head']['respCode'] == "000000") {
                $credential = $data['body']['credential'];
                return $credential;
            } else {
                //print_r($arr['data']);
                return $arr['data'];
            }
    }


    public function notify()
    {
        $data = stripslashes($this->params['data']); //支付数据
        $result = json_decode($data, true); //data数据
        
        try {
            $this->verify($data, $this->params['sign'], $this->pubKey);
            
            file_put_contents("temp/test_notifyUrl_log.txt", date("Y-m-d H:i:s") . "  " . "异步通知返回报文：" . $data . "\r\n", FILE_APPEND);

            return $data;
            //echo "respCode=000000";
        }catch(\Exception $e){
            return array('result' => False, 'message' => $e->getMessage()); 
        }
        

        /*
        if (verify($data, $this->params['sign'], $this->pubkey)) {
            //签名验证成功
            file_put_contents("temp/test_notifyUrl_log.txt", date("Y-m-d H:i:s") . "  " . "异步通知返回报文：" . $data . "\r\n",
                FILE_APPEND);
            echo "respCode=000000";
            exit;
        } else {
            //签名验证失败
            return "sign verification failed.";
            exit;
        }*/
    }

}
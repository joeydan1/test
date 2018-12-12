<?php
namespace App\Providers;
date_default_timezone_set("Asia/Shanghai");

class EbankPayReceiveParams
{

    public static function loadParams($mode, $post)
    {
        $extraObj = json_decode(stripslashes($post['extra']));
        $signObj = json_decode(stripslashes($post['sign']));

        switch($mode){
            case 'eBankQuery';
                return array(
                    'head' => array(
                        'version' => '1.0',
                        'method' => 'sandpay.trade.query',
                        'productId' => '00000007',
                        'accessType' => '1',
                        'mid' => $post['mid'],
                        'channelType' => '07',
                        'reqTime' => date('YmdHis', time()),
                    ),
                    'body' => array(
                        'orderCode' => $post['orderCode'], //订单号
                        'extend' => '',
                    )
                );
                break;

            case 'eBankPay';
                return array(
                    'head' => array(
                        'version' => '1.0',
                        'method' => 'sandpay.trade.pay',
                        'productId' => '00000007',
                        'accessType' => '1',
                        'mid' => $extraObj->{'memberId'},
                        'channelType' => '07',
                        'reqTime' => date('YmdHis', time())
                    ),
                    'body' => array(
                        'orderCode' => $post['orderCode'],
                        'totalAmount' => $post['totalAmount'],
                        'subject' => $post['subject'],
                        'body' => $post['body'],
                        //'txnTimeOut' => $post['txnTimeOut'],
                        'payMode' => $post['payMode'],
                        'payExtra' => array('payType' => $post['payType'], 'bankCode' => $post['bankCode']),
                        'clientIp' => $post['clientIp'],
                        'notifyUrl' => $post['notifyUrl'],
                        'frontUrl' => $post['frontUrl'],
                        'extend' => '',
                    )
                );
                break;

            case 'ebankPayNotify';
                return array(
                    'sign' => $post['sign'],
                    'signType' => $post['signType'],
                    'data' => $post['data'],
                    'charset' => $post['charset'],
                );
                break;
        }
    }
}

?>
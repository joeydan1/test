<?php
namespace App\Providers;

use Illuminate\Support\Facades\DB;
//use App\Providers;

class DBHandller
{
    function merchantRegister($data){
        if ($data['options'] == "on"){
            $data['agentName'] = null;
        }

        try{
            DB::table('myMerchant')->insert(
                [   'merchantName'  =>  $data['merchantName'],
                    'cellNo' => $data['cellNo'],
                    'verifyCellNo' => $data['verifyCellNo'],
                    'email' =>  $data['email'],
                    'agentName' =>  $data['agentName'],
                    'payKey' => $data['payKey'],
                    'memberId' => date("ymdHis") . rand(100,199),
                ]   
            );
            return array('result' => True);

        }catch(\Illuminate\Database\QueryException $e){
            return array('result' => False, 'message' => $e->getMessage());
        }
    }

    function sandpayEbankPayNotifyInsert($data){
        try{
            DB::table('sandpayEbankPayNotify')->insert(
                [   'mid'  =>  $data['mid'],
                    'orderCode' => $data['orderCode'],
                    'totalAmount' => $data['totalAmount'],
                    'orderStatus' =>  $data['orderStatus'],
                    'traceNo' =>  $data['traceNo'],
                    'buyerPayAmount' =>  $data['buyerPayAmount'],
                    'discAmount' =>  $data['discAmount'],
                    'payTime' =>  $data['payTime'],
                    'clearDate' =>  $data['clearDate'],
                ]   
            );
            return array('result' => True);

        }catch(\Illuminate\Database\QueryException $e){
            return array('result' => False, 'message' => $e->getMessage());
        }
    }

    function myEbankPayTradingRecordInsert($data){
        try{
            DB::table('myTradingRecord')->insert(
                [   'memberId'  =>  $data['memberId'],
                    'merchantName' => $data['merchantName'],
                    'mchOrderNo' => $data['mchOrderNo'],
                    'amount' =>  $data['amount'],
                    'tradeType' =>  $data['tradeType'],
                    'timePaid' =>  $data['timePaid'],
                ]   
            );
            return array('result' => True, 'message' => 'myEbankPayTradingRecordInsert sucessfully');

        }catch(\Illuminate\Database\QueryException $e){
            return array('result' => False, 'message' => $e->getMessage()); 
        }
    }


    function myPayKey($table, $memberId){

        try{
            $myPayKey = DB::table($table)->where('memberId', $memberId)->value('payKey');
            if (isset($myPayKey)){
                return array('result' => True, 'myPayKey' => $myPayKey);
            }else{
                return array('result' => False, 'message' => 'not found matched myPayKey..');
            }

        }catch(\Illuminate\Database\QueryException $e){
            return array('result' => False, 'message' => $e->getMessage()); 
        }
         
    }

    
   function sandpayMerchantRegister($data){

        try{
            DB::table('sandpayMerchant')->insert(
                [   'mid'  =>  $data['mid'],
                    'memberId' => $data['memberId'],
                    'pubKey' => $data['pubKey'],
                    'priKey' =>  $data['priKey'],
                ]   
            );
            return array('result' => True);
            
        }catch(\Illuminate\Database\QueryException $e){
            return array('result' => False, 'message' => $e->getMessage());
        }
    }
    
}


?>
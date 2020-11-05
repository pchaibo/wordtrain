<?php
namespace app\user\controller;
use think\Input;
use think\Controller;
use think\console\command\Help;
class Apiword extends Controller{
    public function userquestion(){
        $where = array();
        $where['status'] =0;
        $res = db('user_question')->where($where)->find();
        
        if(!$res){
            $res['code'] = 200;
            return  json($res);
        }
        
        $where = array();
        $where['id'] = $res['questions'];
        $where['id'] = explode(',', $res['questions']);
        $data = db('question')->where($where)->select();
        //print_r($data);
        foreach ($data as $k){
            $ask[]=$k['questionurl'];
            $answer[] = $k['answerurl'];
        }
        $resdata['questionid'] = $res['id'];
        $resdata['ask'] = $ask;
        $resdata['answer'] = $answer;
        $resdata['word_url'] = $res['word_url'];
        $resdata['code'] = 100;
        return json($resdata);
        
    }
    
    public function setquestion(){
         $id=input('get.id');
         $id = intval($id);
         if($id){
             $data['status']= 1;
             $where['id'] = $id;
             db('user_question')->where($where)->update($data);
             
             $resdata['msg'] = "ok";
             $resdata['id'] = $id;
             $resdata['code'] = 100;
             return json($resdata);
         }else{
             $resdata['msg'] = "error";
             $resdata['code'] = 200;
             return json($resdata);
         }
         
    }
    
    
    
    
}
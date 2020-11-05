<?php
namespace app\user\controller;
use think\Db;
class Selectedtopic extends Common{
    protected $uid;
    public function initialize()
    {
        parent::initialize();
        $this->uid=session('user.id');
        $this->assign('title','训练题目');
    }
    
    public function index(){
        if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('question')->alias('u')
                ->join(config('database.prefix').'questiontype ul','u.typeid = ul.id','left')
                ->field('u.*,ul.title')
                ->where('u.question|u.level','like',"%".$key."%")
                ->where('u.is_lock',0)
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            $userproblem = session('userproblem');
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
                //处理已经选中
                if(is_array($userproblem)){
                    foreach ($userproblem as $uk=>$userv){
                        if($list['data'][$k]['id']==$userv){
                            $list['data'][$k]['LAY_CHECKED'] = true;
                        }
                    }
                }
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
         $this->assign('nav',2);
        return $this->fetch();
    }
    //提交用户选择数据
    public function Selectedall(){
        if(request()->isPost()){
            $ids = input('post.ids');
            if(!is_array($ids)){
                $result['msg'] = '编辑资料失败!';
                $result['status'] = 0;
                return $result;
            }
            $str = implode(',',$ids);
            $data = array();
            $data['num'] = count($ids);
            $data['userid'] = $this->uid;
            $data['addtime'] = time();
            $data['word_url'] = $this->uid."_".$data['addtime'];
            $data['questions'] = $str;
            $res = db('user_question')->insert($data);
            if($res){
                session('userproblem',null);
                $result['msg'] = '添加成功!';
                $result['url'] = url('index');
                $result['code'] = 1;
            }else{
                $result['msg'] = '添加失败!';
                $result['code'] = 0;
            }
            
            return $result;
            
        }
    }
    
    //用户选择的
    public function userquestion(){
        if(request()->isPost()){
            $where = array();
            $where['userid'] = $this->uid;
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = db('user_question')->where($where)
                    ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                    ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }
        
        return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
            
        }
        $this->assign('nav',3);
        return $this->fetch();
    }
    
    //
    public function problemadd(){
        if(request()->isPost()){
            $id = intval(input('post.id'));
            $status = input('post.status');
            if(!$id){
                exit("id not");
            }
            if($status=="true"){
                $arr[] = $id;
                $oldarr = session('userproblem');
                if(!$oldarr){
                    session('userproblem',$arr);
                }else{
                    $adddata= array_unique(array_merge($oldarr,$arr));
                    session('userproblem',$adddata);
                }
                $arrTest = session('userproblem');
            }else if($status=="false"){
                //删除存在的
                $arrTest = session('userproblem');
                foreach ($arrTest as $k=>$v){
                    if($v==$id){
                        unset($arrTest[$k]);
                    }
                }
                $arr = array_values($arrTest);
               // print_r($status);
                session('userproblem',$arr);
            }
            
           
           
        } 
        //session('userproblem',null);
        // $arr =  session('userproblem');
       //  print_r($arr);
        
    }
    
    
    
}



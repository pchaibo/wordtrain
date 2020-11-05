<?php
namespace app\admin\controller;
use think\Db;
use think\facade\Request;
class Question extends Common
{
    public  function  questionlist(){
       if(request()->isPost()){
            $key=input('post.key');
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=db('question')->alias('u')
                ->join(config('database.prefix').'questiontype ul','u.typeid = ul.id','left')
                ->field('u.*,ul.title')
                ->where('u.question|u.level','like',"%".$key."%")
                ->order('u.id desc')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
        
    }
    //设置状态
    public function questionState(){
        $id=input('post.id');
        $is_lock=input('post.is_lock');
        if(db('question')->where('id='.$id)->update(['is_lock'=>$is_lock])!==false){
            return ['status'=>1,'msg'=>'设置成功!'];
        }else{
            return ['status'=>0,'msg'=>'设置失败!'];
        }
    }
    public function questionDel(){
        db('question')->delete(['id'=>input('id')]);
        return $result = ['code'=>1,'msg'=>'删除成功!'];
    }
    
    public function questionedit($id=''){
        $id =intval($id);
        if(request()->isPost()){
            $data = input('post.');
            $level =explode(':',$data['level']);
            $data['level'] = $level[1];
            $typeid =explode(':',$data['typeid']);
            $data['typeid'] = $typeid[1];
            unset($data['file']);
            if($data['question']==""){
                $result['msg'] = '请添加训练题目!';
                $result['code'] = 0;
            }
            $data['addtime'] = time();
            $res = db('question')->where(['id'=>$id])->update($data);
            if ($res!==false) {
                $result['msg'] = '编辑训练题成功!';
                $result['url'] = url('questionlist');
                $result['code'] = 1;
            } else {
                $result['msg'] = '编辑训练题失败!';
                $result['code'] = 0;
            }
            return $result;
        }else{
            $info = db('question')->where(['id'=>$id])->find();
            $this->assign('info',json_encode($info,true));
            $this->assign('title','编辑训练题');
            $questiontype=db('questiontype')->order('sort')->select();
            $this->assign('questiontype',json_encode($questiontype,true));
            $question_level=db('question_level')->select();
            $this->assign('question_level',json_encode($question_level,true));
            return $this->fetch('questionedit');
        }
        
    }
    public function questionadd(){
        if(request()->isPost()){
                $data = input('post.');
                $level =explode(':',$data['level']);
                $data['level'] = $level[1];
                $typeid =explode(':',$data['typeid']);
                $data['typeid'] = $typeid[1];
                unset($data['file']);
                if($data['question']==""){
                    $result['msg'] = '请添加训练题目!';
                    $result['code'] = 0;
                }
                $data['addtime'] = time();
                $res = db('question')->insert($data);
                if ($res!==false) {
                    $result['msg'] = '训练题目添加成功!';
                    $result['url'] = url('questionlist');
                    $result['code'] = 1;
                } else {
                    $result['msg'] = '训练题目添加失败!';
                    $result['code'] = 0;
                }
                return $result;
            }else{
                $this->assign('info','null');
                $this->assign('title','训练题增加');
                $questiontype=db('questiontype')->order('sort')->select();
                $this->assign('questiontype',json_encode($questiontype,true));
                $question_level=db('question_level')->select();
                $this->assign('question_level',json_encode($question_level,true));
                return $this->fetch('questionedit');
        }
    }
    
    
    //分类
    public function type(){
        if(request()->isPost()){
            $userLevel=db('questiontype');
            $list=$userLevel->order('sort')->select();
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return $this->fetch();
    }
    
    public function typeadd(){
        if(request()->isPost()){
            $data = input('post.');
            db('questiontype')->insert($data);
            $result['msg'] = '分类组添加成功!';
            $result['url'] = url('typeadd');
            $result['code'] = 1;
            return $result;
        }else{
            $this->assign('title',lang('add')."分类组");
            $this->assign('info','null');
            return $this->fetch('typeedit');
        }
    }
    
    public function typeedit(){
        if(request()->isPost()) {
                $data = input('post.');
                db('questiontype')->update($data);
                $result['msg'] = '分类组修改成功!';
                $result['url'] = url('type');
                $result['code'] = 1;
                return $result;
            }else{
                $map['id'] = input('param.id');
                $info = db('questiontype')->where($map)->find();
                $this->assign('title',lang('edit')."分类组");
                $this->assign('info',json_encode($info,true));
                return $this->fetch();
            }
    }
    
    
    public function typeDel(){
        $level_id=input('id');
        if (empty($level_id)){
            return ['code'=>0,'msg'=>'会员组ID不存在！'];
        }
        db('questiontype')->where(array('id'=>$level_id))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function typeOrder(){
        $userLevel=db('questiontype');
        $data = input('post.');
        $userLevel->update($data);
        $result['msg'] = '排序更新成功!';
        $result['url'] = url('userGroup');
        $result['code'] = 1;
        return $result;
    }
    
    
    
}
   
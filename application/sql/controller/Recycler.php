<?php


namespace app\sql\controller;


use think\Controller;
use think\facade\Session;

class Recycler extends Controller
{

    /**
     * 将回收站中的数据取出来
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function binList()
    {
        //判断有无session
        $ua = Session::get('login_info.user_agent');
        //基本的检验user-agent,最低级的
        if($ua!=$_SERVER['HTTP_USER_AGENT'])
        {
            //已登录，则直接进入管理端
            return $this->error('错误，请重新登录');
        }

        $data = db('recycler')->field('id,title,time,profile,class')
            ->paginate(6);
        $this->assign('tip','注意：！！！这是回收站！！！！');
        $this->assign('method','binlist');
        //输出博客数据，来进行更改


        $page = $data->render();
        $this->assign('data',$data);
        $this->assign('page',$page);
        return $this->fetch('admin@options/recycler_list');
    }

    /** 丢进回收箱，把其存进另一个表
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function restore($id)
    {
        $data = db('recycler')->field('title,profile,time,class,content')->where('id',$id)->select();
        $info = [

            'title'=>$data[0]["title"],
            'profile'=>$data[0]["profile"],
            'time'=>$data[0]["time"],
            'class'=>$data[0]["class"],
            'content'=>$data[0]["content"],
        ];

        $if_insert = db('blog')->insert($info);
        if($if_insert)
        {
            //插入博客，就把原来的删除掉
            $if_delete = db('recycler')->where('id',$id)->delete();
            if($if_delete)
            {
                return json('还原成功',200);
            }
            else
            {
                return json('还原失败',404);
            }
        }
        else
        {
            return json('失败，可能是网络原因，建议稍候重试，或联系开发者',404);
        }
        return json('失败，可能是网络原因，建议稍候重试，或联系开发者',404);

    }

    /** 彻底删除并储存
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function realDelete($id)
    {
        $data = db('recycler')->field('title,profile,time,class,content')->where('id',$id)->select();
        $info = [
            'title'=>$data[0]["title"],
            'profile'=>$data[0]["profile"],
            'time'=>$data[0]["time"],
            'class'=>$data[0]["class"],
            'content'=>$data[0]["content"],
        ];
        //把上面取出的信息，由数组序列化为字符串
        $info_str = '<--start-->'.serialize($info).'<--end-->';
        //
        $file = file_put_contents("../common/rush.txt",$info_str,FILE_APPEND);

        if(filesize('../common/rush.txt')>20971520)
        {
            return json('错误！common/rush.txt中存储的垃圾已超过20M, 请去清理再选择彻底删除',404);
        }
       //
        if($file)
        {
            //彻底删除数据
            $delete = db('recycler')->where('id',$id)->delete();
            if(!$delete)
            {
                return json('彻底删除失败,请重试',404);
            }
            return json('彻底删除成功',200);
        }
        return json('彻底删除失败,请重试',404);

    }
}
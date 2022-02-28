<?php


namespace app\sql\controller;

use think\Db;
use think\Controller;
use think\Exception;
use think\Request;

ini_set('date.timezone','PRC');
//设置时区
class Blog extends Controller
{
    /**
     * @return \think\response\Json
     * note : 接口——输出所有的博客json数据
     */
    public function getBlog()
    {
        $data = db('blog')->field('id,title,profile,time')->select();
        return json($data);
    }
    /**
     * 将博客保存到草稿箱
     * @param Request $request
     * @return string
     *
     * @param $content 文章内容
     * @param $title 标题
     * @param $class 分类
     */
    public function saveBlog(Request $request)
    {
        $content = $request->param('content');
        //博客正文
        $title = $request->param('title');
        //获取自己设置的博客标题
        $class = $request->param('class');
        //获取自己设置的博客分类，为私密，公开
        $profile = $request->param('profile');
        //博客前96个字节的简介
        $time = date('Y-m-d H:i:s');
        //获取当前时间，也就是博客上传时间

        $data = [
            'title'=>$title,
            'content'=>$content,
            'time'=>$time,
            'class'=>$class,
            'profile'=>$profile
        ];
        // 有问题的话，就开启这个try_catch来查看原因，下面这个sql语句很容易出错
        //  发行版不能使用，防止被看到报错信息
        //   try {
        $if_insert = db('draft')->insert($data);
        //  }
        // catch (\Exception $e) {
        // 异常捕获
        //      return json($e->getMessage());

        // }
        //判断插入是否成功
        if($if_insert)
        {
            return '保存成功，可以在“后台管理”》“草稿箱” 查看';
        }
        else
        {
            return '保存失败，请重试！';
        }
    }

    /**
     * 插入博客的接口，将所有博客插入数据库中
     * @param Request $request
     * @return string
     *
     * @param $content 文章内容
     * @param $title 标题
     * @param $class 分类
     */
    public function writeBlog(Request $request)
    {
        $content = $request->param('content');
        //博客正文
        $title = $request->param('title');
        //获取自己设置的博客标题
        $class = $request->param('class');
        //获取自己设置的博客分类，为私密，公开
        $profile = $request->param('profile');
        //博客前96个字节的简介
        $time = date('Y-m-d H:i:s');
        //获取当前时间，也就是博客上传时间
        $data = [
            'title'=>$title,
            'content'=>$content,
            'time'=>$time,
            'class'=>$class,
            'profile'=>$profile
        ];
    // 有问题的话，就开启这个try_catch来查看原因，下面这个sql语句很容易出错
    //  发行版不能使用，防止被看到报错信息
     //   try {
             $if_insert = db('blog')->insert($data);
             //  }
        // catch (\Exception $e) {
        // 异常捕获
        //      return json($e->getMessage());
        // }
        //判断插入是否成功
        if($if_insert)
        {
            return '发布成功，去首页看看吧！';
        }
        else
        {
            return '发布失败，请重试！';
        }
    }

    /**
     * @param Request $request
     * @return string
     * @param $content 文章内容
     * @param $title 标题
     * @param $class 分类
     * @param $id 编号
     * note : 更新博客内容
     */
    public function updateBlog(Request $request)
    {
        $id = $request->param('id');
        //获取ID，传入ID才能更新
        $content = $request->param('content');
        //博客正文
        $title = $request->param('title');
        //获取自己设置的博客标题
        $class = $request->param('class');
        //获取自己设置的博客分类，为私密，公开
        $profile = $request->param('profile');
        //更改的话，建议不改时间？还是加一个最新修改时间？
//        $time = date('Y-m-d h:i:s', time());
        //获取当前时间，也就是博客上传时间
        //这里可以根据传来的数值，在不同的数据库作更新
        $data =[
            'content'=>$content,
            'title'=>$title,
            'class'=>$class,
            'profile'=>$profile
        ];
        //$下面这个包含$content字段，因为文章内容太长了


        $if_update = db('blog')->where('id', $id)->update($data);

        //直接更新
        //判断更新是否成功
        if($if_update>0)
        {
            return '修改成功';
         
        }
        else
        {
            return '修改失败，可能是网络原因，或者是内容未更改';
        }
    }

    /** 删除博客，并存入回收站表
     * @param $id
     * @return int|string
     */
    public function deleteBlog($id)
    {
        //错误提示
        $errMsg = '删除失败，可能是网络原因，建议稍候重试，或联系开发者';
        //先取出数据
        $data = db('blog')->field('title,profile,time,class,content')->where('id',$id)->select();
        $info = [

            'title'=>$data[0]["title"],
            'profile'=>$data[0]["profile"],
            'time'=>$data[0]["time"],
            'class'=>$data[0]["class"],
            'content'=>$data[0]["content"],
        ];

        $if_insert = db('recycler')->insert($info);
        if($if_insert)
        {
            //插入草稿箱，就把原来的删除掉
            $if_delete = db('blog')->where('id',$id)->delete();
            if($if_delete)
            {
                return json('删除成功！可以在后台‘回收站’查看',200);
            }
            else
            {
                return json($errMsg,404);
            }
        }
        else
        {
            return json($errMsg,404);
        }
        return json($errMsg,404);
    }

}
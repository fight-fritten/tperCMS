<?php
namespace app\index\controller;

use think\Controller;
use think\facade\Session;


class Index extends Controller
{
  
        
    /**
     * @return mixed
     * @throws \think\exception\DbException
     * note:显示首页的主要内容
     */
    public function view()
    {

        //从blog表中读取所有数据  分页：5
        $list = db('blog')->field('id,title,profile,time,class')
            ->order('time DESC')->paginate(5);
        
        
        //渲染分页组
        $page = $list->render();
        //模板渲染 blog数据和分页组
       

        //获取公共变量，设置备案号
        $xml=simplexml_load_file("../common/common.xml");
        //备案号$benan
        $beian = $xml->text->beian;
        
        
        //！！这里sql重复了，但是tp5暂时不太会操作，先这样吧，
        //有时间再优化
        //读取blog表 title , id
        $latter = db('blog')->field('title,id')
            ->order('time DESC')->limit(5)->select();
        
        
        $this->assign('list', $list);
        $this->assign('latter',$latter);
        $this->assign('beian',$beian);
        $this->assign('page',$page);
        
        //返回视图
        return $this->fetch('index');
    }

    /** 显示博客，相当于查看更多
     * @param $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     *
     */
    public function showBlog($id)
    {
            //获取公共变量，设置备案号
            $xml=simplexml_load_file("../common/common.xml");
            $beian = $xml->text->beian;
            
            
            //读取blog表数据
            $latter = db('blog')->field('title,id')->order('time DESC')->paginate(5);
            
            
            //读取具体内容
            $list = db('blog')->field('title,content,time,class')->where('id',$id)->select();
            

            //渲染分页组
            $page = $latter->render();
            
            
           
            //判断设置，以及文章是否为个人，否则则要求登录
            if($xml->config->ifLock=='true')
            {
                if ($list[0]['class'] == '个人')
                {
                    if (Session::get('login_info.user_agent') != $_SERVER['HTTP_USER_AGENT'])
                    {
                        return $this->error('个人文章只有管理员登录后，才可以查看');
                    }
                }
            }
            
            $this->assign('beian',$beian);
            $this->assign('page',$page);
            $this->assign('data',$list);
            $this->assign('latter',$latter);
            
            //返回showblog页面
            return $this->fetch('showBlog');

    }

    /**
     * 暂时这么写，通过分类显示博客
     * @param $class
     * @return mixed
     */
    public function showByClass($class)
    {
        //获取公共变量，设置备案号
        $xml=simplexml_load_file("../common/common.xml");
        $beian = $xml->text->beian;
        

        $list = db('blog')->field('id,title,profile,time,class')->where('class',$class)
            ->order('time DESC')->paginate(5);
        // 获取分页显示

        $page = $list->render();
        //渲染分页按钮

        //渲染最近文章
        $latter = db('blog')->field('title,id')
            ->order('time DESC')->limit(5)->select();
        
        
        $this->assign('latter',$latter);
        $this->assign('page',$page);
        $this->assign('list', $list);
        $this->assign('beian',$beian);
      

        //返回视图
        return $this->fetch('showBlogbyclass');
    }
}

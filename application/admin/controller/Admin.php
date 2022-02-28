<?php


namespace app\admin\controller;


use think\Controller;
use think\Request;
use think\facade\Session;


class admin extends Controller
{

    /**登录
     * @return mixed
     */
    public function login()
    {
        //判断有无session
        $ua = Session::get('login_info.user_agent');
        //基本的检验user-agent,最低级的
        if($ua==$_SERVER['HTTP_USER_AGENT'])
        {
            //已登录，则直接进入管理端
            return $this->fetch('admin');
        }
        else
        {
            //未登录，则要求登录
            return $this->fetch('admin_login');
        }
    }

    /**
     * 清除session, 相当于登出
     */
    public function loginOut()
    {
        //清空session
        $if = Session::clear();
        //返回首页
        return $this->success('退出登录成功！','index/index/view','','1');
    }
    /**
     * 显示admin管理端的首页
     * @return mixed|string
     */
    public function view()
    {
        //判断有无session
        $ua = Session::get('login_info.user_agent');
        $ip = Session::get('login_info.remote_ip');
        //基本的检验user-agent,最低级的
        if($ua==$_SERVER['HTTP_USER_AGENT']&&$ip==$_SERVER['REMOTE_ADDR'])
        {
            return $this->fetch('admin');
        }
        else
        {
            return '请重新登录';
        }
    }
    /**
     * 返回视图，---写博客
     */
    public function newBlog()
    {
        //判断有无session
        $ua = Session::get('login_info.user_agent');
        $ip = Session::get('login_info.remote_ip');
        //基本的检验user-agent,最低级的
        if($ua==$_SERVER['HTTP_USER_AGENT']&&$ip==$_SERVER['REMOTE_ADDR'])
        {
            return $this->fetch('blog/ch_blog');
        }
        else
        {
            return '请重新登录';
        }
    }
    
    /**
     * 设置前端的布局,写不动了，艹
     */
//    public function setlayout()
//    {
//        return $this->fetch('set/set_layout');
//    }


    /**
     * 根据列表来编辑博客
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @param $method 
     */
    public function editBlog()
    {
        //判断有无session
        $ua = Session::get('login_info.user_agent');
        //基本的检验user-agent,最低级的
        if($ua!=$_SERVER['HTTP_USER_AGENT'])
        {
            //已登录，则直接进入管理端
            return $this->error('错误，请重新登录');
        }

            //时间正序，查询blog表中所有数据，并分页
            $data = db('blog')->field('id,title,time,profile,class')->order('time DESC')
                ->paginate(6);
                
            //向前端输出，提示和类型
            $this->assign('tip','这是修改首页博客列表中的内容：');
            $this->assign('method','blog');
            //输出博客数据，来进行更改

            //渲染分页的模块
            $page = $data->render();
            
            //模块渲染 blog数据 和 分页模块
            $this->assign('data',$data);
            $this->assign('page',$page);
            
            //返回视图
        return $this->fetch('options/blog_list');
    }

    /**
     * 进入页面来对指定博客进行更改
     *  @param $id
     */
    public function pageEditBlog($id)
    {

        //判断有无session
        $ua = Session::get('login_info.user_agent');
        //基本的检验user-agent,最低级的
        if($ua!=$_SERVER['HTTP_USER_AGENT'])
        {
            //未登录，则直接报错
            return $this->error('错误，请重新登录');
        }

            //输入blog进入修改博客内容
            $data = db('blog')->field('id,title,time,content,class')->order('time DESC')
                ->where('id', $id)->select();
            
            //返回method 和 class 到前端    
            $this->assign('method','blog');
            $this->assign('class',$data[0]["class"]);                                               

            //返回id 和 blog数据 到前端
            $this->assign('id',$id);
            $this->assign('data',$data);
        
        //返回视图
        return $this->fetch('blog/edit_blog');
       
    }

    /**
     * 检验输入的登录用户名和密码是否符合规范
     * @param Request $request
     * post ：$username $passwd
     *
     */
    public function loginCheck(Request $request)
    {
        //获取input表单传进来的
        $username = input('post.username');
        $passwd = input('post.passwd');

        //简单过滤
        $this->validCheck($username);
        $this->validCheck($passwd);

        //先注释掉：本来想在后台首页显示博客列表的
        // $data = db('blog')->field('id,title,time,profile,class')
        //     ->paginate(6);
        // //输出博客数据，来进行更改
        // $page = $data->render();
        // $this->assign('data',$data);
        // $this->assign('page',$page);
        //
        //查询数据库，是否有用户名
        $query = db('admin')->where(["username"=>$username,
            "passwd"=>$passwd])->select();
        //如果查询到了
        if($query)
        {
            //存入session,获取用于验证的消息
            $login_info = [
                'username'=>$username,
                'time'=>date('Y-m-d H:i:s'),
                'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
                'remote_ip'=>$_SERVER['REMOTE_ADDR']

            ];
            
            //存入session，$login_info
            Session::set('login_info',$login_info);

            //重定向到后台管理端页面
            $this->success('登录成功！！','admin/admin/view','','1');
        }
        else
        {
            //错误
            $this->error('错误：用户名或密码错误！');
        }

    }

    /**
     * @param $demo
     * note:基本过滤，作限制，简单防止注入, 允许了英文句号
     */
    public function validCheck($demo)
    {
        //检查长度
        $len = strlen($demo);
        //用户名或密码长度应该在5到20之间
        if($len<5||$len>20)
        {
            $this->error('错误：用户名或密码长度应该在5到20之间');
        }
        //过滤特殊字符，防止sql注入
        if(preg_match("/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\/|\;|\'|\`|\-|\=|\\\|\|/",$demo))
        {
            $this->error('错误：请勿使用非法符号，只允许使用数字，英文，和句号!');
        }
    }
}
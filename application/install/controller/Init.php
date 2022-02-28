<?php


namespace app\install\controller;


use think\Controller;
use think\Db;
use think\facade\Session;
use think\Request;
use think\Exception;

class Init extends  Controller
{
    /**第二步
     */
    public function step2()
    {
        //检测，上一步是否已配置
        if(Session::get('db_info.ifconfig')=='true')
        {
            return $this->fetch('install/init');
        }
        else
        {
            //重定向，错误哦
            $this->error('错误：请先完成上一步骤的设置！');
        }
    }

    /** 判断登录密码是否符合规范，并存入session
     * @param Request $request ($username, $passwd)
     * @return string
     */
    public function checkPasswd(Request $request)
    {
        //获得用户名，密码
        $username = input('username');
        $passwd = input('passwd');
        
        //用户名长度限制
        if(strlen($username)>15||strlen($username)<3)
        {
            return '用户名长度必须在3-15之间';

        }
        //密码长度限制
        if(strlen($passwd)>15||strlen($passwd)<5)
        {
            return '密码长度必须在5-15之间';
        }
        //防止敏感字符，.除外
        if(preg_match("/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\/|\;|\'|\`|\-|\=|\\\|\|/",$username.$passwd))
        {
            return '错误：请勿使用非法符号，只允许使用数字，英文，和句号!';
        }
        //必须同时有。。。
        if(!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)/', $passwd))
        {
            return '密码必须同时有数字和字母';
        }
        //全部符合
        $login_passwd =[
            'username' => $username,
            'passwd'   => $passwd
        ];
        //存入session
        Session::set('login_passwd',$login_passwd);
        return '成功';

    }

    /**
     * 建立数据库，1）执行sql文件 2）更改config/database.php中的内容
     * @return mixed
     */
    public function buildSql()
    {
        //获得在session中的数据，但是禁用cookie可能会有麻烦
       $username = Session::get('login_passwd.username');
       $passwd = Session::get('login_passwd.passwd');
        // 检测连接
        try {
            $db_msg = [
                'type' => 'mysql',
                'hostname' => Session::get('db_info.hostname'),
                'database' => Session::get('db_info.database'),
                'username' => Session::get('db_info.username'),
                'password' => Session::get('db_info.passwd'),
                'hostport' => Session::get('db_info.hostport'),
                'charset' => 'utf8',
            ];
            //看是否能执行
            $db_conn = Db::connect($db_msg)
                ->query('show databases');
            
            //如果能
            if ($db_conn) {
                //执行sql语句
                //创建表
                Db::connect($db_msg)->execute('DROP TABLE IF EXISTS `admin`;');
                Db::connect($db_msg)->execute('CREATE TABLE admin(`username` varchar(40) COLLATE utf8_bin NOT NULL,`passwd` varchar(40) COLLATE utf8_bin NOT NULL) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
                //                Db::connect($db_msg)->execute('insert into blog(title) values (\'cao\');');
                //插入设置的管理员登录密码
                Db::connect($db_msg)->execute('INSERT INTO admin(username,passwd) VALUES(\''.$username.'\',\''.$passwd.'\')');
                //创建博客表
                Db::connect($db_msg)->execute('DROP TABLE IF EXISTS `blog`;');
                Db::connect($db_msg)->execute('CREATE TABLE `blog` (`id` int(15) unsigned zerofill NOT NULL AUTO_INCREMENT,`content` longtext COLLATE utf8_bin,`title` varchar(40) COLLATE utf8_bin DEFAULT NULL,`profile` longtext COLLATE utf8_bin COMMENT \'简介\',`time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT \'\r\n上传时间\',`class` varchar(40) COLLATE utf8_bin DEFAULT NULL COMMENT \'文章分类\',`label` varchar(40) COLLATE utf8_bin DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
                //创建回收站表
                //这里格式实在不想改了
                Db::connect($db_msg)->execute('DROP TABLE IF EXISTS `recycler`;');
                Db::connect($db_msg)->execute('CREATE TABLE `recycler` (
  `id` int(15) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `content` longtext COLLATE utf8_bin,
  `title` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `profile` longtext COLLATE utf8_bin COMMENT \'简介\',
  `time` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT \'\r\n上传时间\',
  `class` varchar(40) COLLATE utf8_bin DEFAULT NULL COMMENT \'文章分类\',
  `label` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
                //这是将之前生成好的数据写入 config/database.php
                $config="<?php
                return [
                    // 数据库类型
                    'type'            => 'mysql',
                    // 服务器地址
                    'hostname'        => '".Session::get('db_info.hostname')."',
                    // 数据库名
                    'database'        => '".Session::get('db_info.database')."',
                    // 用户名
                    'username'        => '".Session::get('db_info.username')."',
                    // 密码
                    'password'        => '".Session::get('db_info.passwd')."',
                    // 端口
                    'hostport'        => '".Session::get('db_info.hostport')."',
                    // 连接dsn
                    'dsn'             => '',
                    // 数据库连接参数
                    'params'          => [],
                    // 数据库编码默认采用utf8
                    'charset'         => 'utf8',
                    // 数据库表前缀
                    'prefix'          => '',
                    // 数据库调试模式
                    'debug'           => true,
                    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
                    'deploy'          => 0,
                    // 数据库读写是否分离 主从式有效
                    'rw_separate'     => false,
                    // 读写分离后 主服务器数量
                    'master_num'      => 1,
                    // 指定从服务器序号
                    'slave_no'        => '',
                    // 自动读取主库数据
                    'read_master'     => false,
                    // 是否严格检查字段是否存在
                    'fields_strict'   => true,
                    // 数据集返回类型
                    'resultset_type'  => 'array',
                    // 自动写入时间戳字段
                    'auto_timestamp'  => false,
                    // 时间字段取出后的默认时间格式
                    'datetime_format' => 'Y-m-d H:i:s',
                    // 是否需要进行SQL性能分析
                    'sql_explain'     => true,
                    // Builder类
                    'builder'         => '',
                    // Query类
                    'query'           => '\\think\\db\\Query',
                    // 是否需要断线重连
                    'break_reconnect' => true,
                    // 断线标识字符串
                    'break_match_str' => [],
                ];
";
                $db_info = file_put_contents('../config/database.php',$config);
                //把公共文件中的数值调对，防止访问路由后重置
                
                //向配置文件中，设置ifinit来判断是否初始化
                $xml=simplexml_load_file("../common/common.xml");
                $xml->ifinit = iconv('ISO-8859-1','utf-8','true');
                $xml->saveXML('../common/common.xml');
                //做一堆检验看是否成功
                //
                //把session清了
                Session::clear();
                //
                return $this->fetch('install/toindex');
                //return '成功连接';
            }
        } catch (\Exception $e) {
            return json($e->getMessage());
            //先跳过去
//            return $this->fetch('install/toindex');

        }
    }

}


<?php


namespace app\install\controller;

use think\Request;
use think\Controller;
use think\Exception;
use think\Db;
use think\facade\Session;

class Install extends Controller
{
    /**
     * 第一步，直接下载，数据库创表
     */
    public function step1()
    {
            //取出xml文件中的设置的公共变量
            $xml=simplexml_load_file("../common/common.xml");
            $ifInit = $xml->ifinit;
            //根据公共变量来使已部署之后，不能再部署
            if ($ifInit=='true')
            {
                $this->error('数据库已初始化，请勿重复尝试');
            }
            else
                //改成任意的值都可以重置，但建议不要随意重置
            {
                return $this->fetch('install');
            }

//        修改并保存xml数据，记录一下怎么弄
//        $xml->a = iconv('ISO-8859-1','utf-8','PHP程序员必备工具');
//        $xml->saveXML('../common/common.xml');

    }

    /**检测数据库是否存在
     * @param Request $request
     * @return \think\response\Json
     */
    public function checkSqlvalid(Request $request)
    {
        
        //获取post来的值
        $host = $request->post('host');
        $port = $request->post('port');
        $username = $request->post('username');
        $passwd = $request->post('passwd');
        $database = $request->post('database');
        //判断是否有空
        if(empty($host)||empty($port)||empty($username)||empty($passwd)||empty($database))
        {
            return json('错误！请把表单填写完整',404);
        }
        // 检测连接
        try {
            $db_conn = Db::connect([
                'type'        => 'mysql',
                'hostname'    => $host,
                'database'    => $database,
                'username'    => $username,
                'password'    => $passwd,
                'hostport'    => $port,
                'charset'     => 'utf8',
            ])
            ->query('show databases');
            //如果连上了
            if($db_conn)
            {
                //在session中保存确定数据库信息，并去step2
                $db_info = [
                    'hostname'    => $host,
                    'database'    => $database,
                    'username'    => $username,
                    'passwd'    => $passwd,
                    'hostport'    => $port,
                    'ifconfig'    =>'true'
                ];
                //设置session
                Session::set('db_info',$db_info);
                return json('设置成功',200);
            }
        } //艹，这里写了两遍一样的话，有时间在来改吧！！！
        catch (\Exception $e)
        {
            return json('错误：无法连接到数据库，请确认这个数据库可用，或检查输入后重试。或检查网络连接。',404);
        }
        return json('错误：无法连接到数据库，请确认这个数据库可用，或检查输入后重试。或检查网络连接',404);
    }

}
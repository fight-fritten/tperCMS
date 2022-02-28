<?php


namespace app\sql\controller;


use think\Controller;

class Layout extends Controller
{
    /** 更改备案号
     * @param $beian
     * @return \think\response\Json
     */
    public function changeBeian($beian)
    {
        //这个地方，暂时没用

        //获取公共变量，读取备案号
        $xml=simplexml_load_file("../common/common.xml");
        $beian_xml = $xml->text->beian;

        //更改xml文件中的备案号，记得设置好编码
        $beian_xml = iconv('ISO-8859-1','utf-8',$beian);

        //判断更改是否成功
        $ifchange = $xml->saveXML('../common/common.xml');

        //回显
        if($ifchange)
        {
            return json('设置成功，备案号为：'.$beian,200);
        }
        else
        {
            return json('设置错误，请重试：'.$beian,404);
        }
    }

}
<?php


namespace app\upload\controller;


use think\Controller;
use think\Request;

class Img extends Controller
{
    /** 富文本上传图片接口，暂时不用该模块
     * @param Request $request
     * @return \think\response\Json
     */
    public function richtext()
    {
        $file = request()->file('file');
        $imgs = [];
        $errors = [];
        if(!$file)
        {
            $msg['errno'] = 1;
            $msg['data'] = $imgs;
            return json($msg);
        }
        
            //移动图片到/public/uploads/ 目录下
            $info = $file->move( '../public/uploads/editor');
            if($info){
              // 成功上传 图片地址加入到数组
                $path = "../../../../uploads/editor/".$info->getSaveName();
                //array_push($imgs,$path);
            }else{
                //否则就报错
                array_push($errors,$file->getError());
              
            }
           
        //输出wangEditor规定的返回数据
        if(!$errors){
            $msg['errno'] = 0;
            $msg['data'][0] = $path;
            return json($msg);
        }else{
            $msg['errno'] = 1;
            $msg['data'] = $imgs;
            $msg['msg'] = "上传出错";
            return json($msg);
        }
        
        
    }
}
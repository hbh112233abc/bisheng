<?php
namespace bingher\bisheng;


class BishengController
{
    protected $bisheng;
    function __construct()
    {
        $this->bisheng = new Bisheng(['api_key'=>'2725ab915cbffcc76b3b9e8fdb9096dc','service_uri'=>'http://192.168.170.128']);
    }
    /**
     * http://www.efileyun.cn.new/bisheng/edit
     * @return [type] [description]
     */
    public function edit(string $docId = '',string $userId = '')
    {
        return $this->bisheng->edit($docId,$userId);
    }

    /**
     * http://www.efileyun.cn.new/bisheng/view
     * @return [type] [description]
     */
    public function view(string $docId = '',string $userId = '')
    {
        return $this->bisheng->view($docId,$userId);
    }

    /**
     * getFile:callUrl返回文件信息用户信息
     * http://www.efileyun.cn.new/bisheng/getFile/docId/123456789/userId/hbh112233abc
     * @return [type] [description]
     */
    public function getFile()
    {
        $docId = Request::param('docId','');
        $userId = Request::param('userId','');
        Log::debug("docId:{$docId},userId:{$userId}");
        if (empty($docId)) {
            return json(['error'=>'docId empty']);
        }
        if (empty($userId)) {
            return json(['error'=>'userId empty']);
        }
        $docInfo = [
            'docId' => $docId, //文档的ID
            'title' => "测试文档", //文件的标题，用于编辑时在编辑器上显示当前文件的标题，需包含加上文件扩展名
            'fetchUrl' => "http://192.168.102.220/static/files/test.docx", //该文件的获取链接。当毕升Office打开该文件时，会通过这个链接去获取文件。该链接不要进行编码
            'callback' => url('home/Bisheng/save',[],false,true)->build(), //API模式下可以通过改参数指定文档的回调地址，也可以不传递该参数而通过配置文件统一配置回存地址
        ];
        $doc = new Doc($docInfo);
        Log::debug($doc);

        $user = new User();
        $user->uid($userId)->nickName('hbh')->avatar("http://192.168.102.220/static/home/images/apply.png")->authWrite();
        Log::debug($user);
        return json([
            'doc' => $doc,
            'user' => $user,
        ]);
    }

    /**
     * 回传保存文件
     * @return [type] [description]
     */
    public function save()
    {
        return $this->bisheng->save();
    }
}
<?php
namespace bingher\bisheng;

use think\facade\Request;
use think\facade\Url;
use think\facade\Config;
use think\facade\Log;
class Bisheng
{
    // protected $apiKey = '2f6b0ebb11781440342f93b8f90688ac24bfc47c9cfafb7e507f956ef0e7c84d';
    // protected $serverUri = 'http://192.168.102.165/apps/editor';
    protected $apiKey = '2725ab915cbffcc76b3b9e8fdb9096dc';
    protected $serverUri = 'http://192.168.170.128';

    function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = Config::get('bisheng');
        }
        if (empty($config)) {
            throw new \Exception('请设置配置信息');
        }
        if (empty($config['api_key'])) {
            throw new \Exception('未设置api_key!');
        }
        if (empty($config['server_uri'])) {
            throw new \Exception('未设置server_uri!');
        }
        $this->apiKey = $config['api_key'];
        $this->server_uri = $config['server_uri'];
        if (!empty($config['opts'])) {
            $this->opts = $config['opts'];
        }
    }

    /**
     * http://www.efileyun.cn.new/home/bisheng/edit
     * @return [type] [description]
     */
    public function edit(string $docId = '',string $userId = '')
    {
        $callUrl = self::makeCallUrl($docId,$userId);
        $sign = $this->makeSign($callUrl);
        $serverUrl = $this->serverUri.'/apps/editor/openEditor?callURL='.$callUrl.'&sign='.$sign;
        return redirect($serverUrl);
    }

    /**
     * http://www.efileyun.cn.new/home/bisheng/view
     * @return [type] [description]
     */
    public function view(string $docId = '',string $userId = '')
    {
        $callUrl = self::makeCallUrl($docId,$userId);
        $sign = $this->makeSign($callUrl);
        $serverUrl = $this->serverUri.'/apps/editor/openPreview?callURL='.$callUrl.'&sign='.$sign;
        return redirect($serverUrl);
    }

    /**
     * 回传保存文件
     * @return [type] [description]
     */
    public function save()
    {
        $post = Request::post();
        Log::debug($post);
        $docId = Request::post('docId','');
        $action = Request::post('action','');
        $data = Request::post('data',[]);
        if ($action == 'saveBack') {
            $this->saveBack($data);
        }
    }

    /**
     * 回调文件处理,继承后可以自定义
     * @param  array  $data 编辑端回调数据data部分
     * @return [type]       [description]
     */
    public function saveBack(array $data = [])
    {
        if ($data['unchanged']) {
            //文件未更改
            return $this->unchanged($data);
        }
        return $this->changed($data);
    }

    /**
     * 文件回传未更改,默认仅记录日志信息,可以自定义操作
     * @param  array  $data 编辑端回调数据data部分
     * @return [type]       [description]
     */
    public function unchanged(array $data = [])
    {
        Log::info($data);
    }

    /**
     * 文件回传已更改,下载文件到本地
     * @param  array  $data 编辑端回调数据data部分
     * @return [type]       [description]
     */
    public function changed(array $data = [])
    {
        $docId = $data['docId'];
        $docUrl = $this->serverUri.$data['docURL'];
        $savePath = './static/files/';
        $ext = explode('.',explode('?',$docUrl)[0]);
        $ext = end($ext);
        $filename = $docId.'.'.$ext;
        Doc::download($docUrl,$savePath,$filename);
        $filePath = $savePath.$filename;
        return $filePath;
    }


    /**
     * 生成签名
     * @param  string $url 链接
     * @return string      签名字符串
     */
    public function makeSign(string $url = '')
    {
        return hash_hmac('md5', $url, $this->apiKey);
    }

    /**
     * 生成获取文件地址
     * @param  string $docId  文件id
     * @param  string $userId 用户id
     * @return string         base64编码的获取文件地址
     */
    static public function makeCallUrl(string $docId = '',string $userId = '')
    {
        $url = Request::domain().'/bisheng/getFile/docId/'.$docId.'/userId/'.$userId;
        // dump($url->build());
        return base64_encode($url);
    }
}
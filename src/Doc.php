<?php
namespace bingher\bisheng;

class Doc
{
    public $docId = ''; //文档的ID
    public $title = "文档标题"; //文件的标题，用于编辑时在编辑器上显示当前文件的标题，需包含加上文件扩展名
    public $mime_type = ''; //毕升Office支持文件类型的mime_type:https://bishengoffice.com/apps/blog/posts/mime_type.html
    public $fetchUrl = ""; //该文件的获取链接。当毕升Office打开该文件时，会通过这个链接去获取文件。该链接不要进行编码
    public $thumbnail = ""; //该文件的缩略图，目前只有视频文件会用该值在视频播放钱进行展示，其他文件可以为空
    public $pdf_viewer = false; //该值true时，Office文件将使用pdf模式进行预览。默认为false，将使用Office预览
    public $fromApi = true; //API模式下必须为true
    public $callback = ''; //API模式下可以通过改参数指定文档的回调地址，也可以不传递该参数而通过配置文件统一配置回存地址

    function __constructor(array $doc = [])
    {
        return $this->create($doc);
    }

    /**
     * 根据数组赋值
     * @param  array  $doc 用户信息
     * @return $this
     */
    public function create(array $doc = [])
    {
        if (empty($doc)) {
            return $this;
        }
        !isset($doc['docId']) ?: $this->docId = $doc['docId'];
        !isset($doc['title']) ?: $this->title = $doc['title'];
        !isset($doc['mime_type']) ?: $this->mime_type = $doc['mime_type'];
        !isset($doc['fetchUrl']) ?: $this->fetchUrl = $doc['fetchUrl'];
        !isset($doc['thumbnail']) ?: $this->thumbnail = $doc['thumbnail'];
        !isset($doc['callback']) ?: $this->callback = $doc['callback'];
        return $this;
    }

    /**
     * 属性设置
     * @param  string $funName 属性名
     * @param  array  $args    设置值
     * @return $this
     */
    public function __call(string $funName,array $args)
    {
        if (!isset($this->$funName)) {
            return $this;
        }
        $this->$funName = $args[0];
        return $this;
    }

    /**
     * 取出对象
     * @param bool $toArray 是否转成数组,默认不转
     * @return $this
     */
    public function fetch(bool $toArray = false)
    {
        list($ext,$this->mime_type) = self::mimeType($this->fetchUrl);
        if (strrpos($this->title,$ext) != (strlen($this->title)-strlen($ext))) {
            $this->title = $this->title.'.'.$ext;
        }
        if ($toArray) {
            return json_decode(json_encode($this),true);
        }
        return $this;
    }

    /**
     * 文件类型
     * //毕升Office支持文件类型的mime_type:https://bishengoffice.com/apps/blog/posts/mime_type.html
     * @param  string $subfix 文件后缀,支持文件路径,自动截取后缀
     * @return string         文件类型mime_type
     */
    static public function mimeType(string $subfix='')
    {
        $ext = $subfix;
        if (strpos($subfix,'.') !== false) {
            $ext = explode('.',$subfix);
            $ext = end($ext);
        }
        $mimeList = [
            "doc" =>  "application/msword",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "xls" =>  "application/vnd.ms-excel",
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "ppt" =>  "application/vnd.ms-powerpoint",
            "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        ];
        return [$ext,$mimeList[$ext]];
    }

    /**
     * 下载文件
     * @param  string      $url      文件链接
     * @param  string      $save_dir 保存位置
     * @param  string      $filename 文件名
     * @param  int|integer $type     0:readfile方法获取,1:curl获取
     * @return array                ['file_name'=>xxx,'save_path'=>yyy]
     */
    static public function download(string $url, string $save_dir = '', string $filename = '', int $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename
        );
    }
}
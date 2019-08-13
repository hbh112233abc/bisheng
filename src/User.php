<?php
namespace bingher\bisheng;

class User
{
    public $uid = '';
    public $oid = '';
    public $nickName = '';
    public $avatar = '';
    public $privilege = [];
    public $opts;

    function __construct(array $user = [])
    {
        if (!empty($user)) {
            $this->create($user);
        }
    }

    /**
     * 根据数组赋值
     * @param  array  $user 用户信息
     * @return $this
     */
    public function create(array $user = [])
    {
        if (empty($user)) {
            return $this;
        }
        !isset($user['uid']) ?: $this->uid = $user['uid'];
        !isset($user['oid']) ?: $this->oid = $user['oid'];
        !isset($user['nickName']) ?: $this->nickName = $user['nickName'];
        !isset($user['avatar']) ?: $this->avatar = $user['avatar'];
        !isset($user['privileges']) ?: $this->privileges = $user['privileges'];
        !isset($user['opts']) ?: $this->opts = $user['opts'];
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
     * 设置读写权限
     * @param  string $op 读写标识:读:r,写:w|rw,其他:只读
     * @return $this
     */
    public function auth(string $op = 'r')
    {
        if ($op == 'r') {
            $this->privilege = [
                'FILE_READ', //可读，用户可以预览该文件
                'FILE_DOWNLOAD', //可下载，用户可下载该文件
                'FILE_PRINT',  //可打印，用户可以打印该文件 如果你需要在在预览模式隐藏编辑以及下载按钮，可以不传递FILE_WRITE以及FILE_DOWNLOAD
            ];
        } elseif ($op == 'w' || $op == 'rw') {
            $this->privilege = [
                'FILE_READ', //可读，用户可以预览该文件
                'FILE_WRITE', //可编辑，用户可以编辑该文件
                'FILE_DOWNLOAD', //可下载，用户可下载该文件
                'FILE_PRINT',  //可打印，用户可以打印该文件 如果你需要在在预览模式隐藏编辑以及下载按钮，可以不传递FILE_WRITE以及FILE_DOWNLOAD
            ];
        } else {
            $this->privilege = [
                'FILE_READ', //可读，用户可以预览该文件
            ];
        }
        return $this;
    }

    /**
     * 设置读权限
     * @return $this
     */
    public function authRead()
    {
        return $this->auth('r');
    }

    /**
     * 设置写权限
     * @return $this
     */
    public function authWrite()
    {
        return $this->auth('w');
    }

    /**
     * 取出对象
     * @param bool $toArray 是否转成数组,默认不转
     * @return $this
     */
    public function fetch(bool $toArray = false)
    {
        if (empty($this->uid) || empty($this->nickName) || empty($this->privilege)) {
            throw new \Exception('user error');
        }
        if (empty($this->oid)) {
            $this->oid = $this->uid;
        }
        if (empty($this->opts)) {
            $this->opts = new \stdClass();
        }
        if ($toArray) {
            return json_decode(json_encode($this),true);
        }
        return $this;
    }

}
<?php
require(__DIR__.'/src/User.php');
require(__DIR__.'/src/Doc.php');

$user = new \bingher\bisheng\User();
// $user->uid('hbh112233abc')->nickName('huang')->avatar('http://www.efileyun.com/favicon.ico')->authWrite();
$user->create([
    'uid' => 'hbh112233abc',
    'nickName' => 'huang',
    'avatar' => 'http://www.efileyun.com/favicon.ico',
])->authRead();
var_dump($user);
$arr = $user->toArray();
var_dump($arr);

$doc = new \bingher\bisheng\Doc();
$doc->docId('123456');
var_dump($doc);
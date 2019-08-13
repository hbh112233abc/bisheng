<?php

namespace bingher\bisheng\facade;

use think\Facade;

/**
 * Class User
 * @package bingher\bisheng\facade
 * @mixin \bingher\bisheng\User
 */
class User extends Facade
{
    protected static function getFacadeClass()
    {
        return \bingher\bisheng\User::class;
    }
}

<?php

namespace bingher\bisheng\facade;

use think\Facade;

/**
 * Class Doc
 * @package bingher\bisheng\facade
 * @mixin \bingher\bisheng\Doc
 */
class Doc extends Facade
{
    protected static function getFacadeClass()
    {
        return \bingher\bisheng\Doc::class;
    }
}

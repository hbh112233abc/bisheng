<?php
namespace bingher\bisheng;

use think\Route;
use think\Service;

class BishengService extends Service
{
    public function boot(Route $route)
    {
        $route->get('bisheng/edit/[:docId]', "\\bingher\\bisheng\\BishengController@edit");
        $route->get('bisheng/view/[:docId]', "\\bingher\\bisheng\\BishengController@view");
        $route->get('bisheng/getFile/[:docId]/[:userId]', "\\bingher\\bisheng\\BishengController@getFile");
        $route->get('bisheng/save', "\\bingher\\bisheng\\BishengController@save");
    }
}

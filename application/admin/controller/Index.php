<?php
namespace app\admin\controller;

class Index extends Base
{
    public function Index()
    {
        echo '这是后台首页';
        return $this->fetch();
    }
}

<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------




return [
    'index' => 'index/index/index',
    'upload' => ['index/article/upload', ['method' => 'post']],
    'login' => ['index/index/login', ['method' => 'post']],
    'publish' => ['index/article/publish', ['method' => 'post']],
    'getArticle'=>'index/article/getArticle',
    'getArticleList'=>'index/article/getArticleList',
    "removeArticle"=>"index/article/removeArticle",
    "getStu"=>["index/master/getStu", ['method' => 'post']],
    "getStuGrade"=>["index/master/getStuGrade", ['method' => 'post']]
];

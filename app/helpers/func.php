<?php

//将当前请求的路由名称转换为CSS类名称
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function make_excerpt($content,$length = 200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/',' ',strip_tags($content)));
    return str_limit($excerpt,$length);
}

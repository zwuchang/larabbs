<?php
/**
 * Created by PhpStorm.
 * User: yzx
 * Date: 2017-12-08
 * Time: 16:06
 */

namespace App\Handlers;

class ImageUploadHandler
{
    protected $allowed_ext = ['png','jpg','gif','jpeg'];

    public function save($file,$folder,$file_prefix)
    {
        //存储路径如：uploads/images/avatars/201712/08/
        $folder_name = "uploads/images/$folder/" . date('Ym',time()) . '/' . date('d',time()) . '/';

        //文件物理路径
        $upload_path = public_path() . '/' . $folder_name;

        //获取后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        //拼接文件名
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        //不是允许上传后缀，终止操作
        if (! in_array($extension,$this->allowed_ext)) {
            return false;
        }

        $folder_name = rtrim($folder_name,'/');
        $path = rtrim(config('app.url'),'/') . "/$folder_name/$filename";
        //移动文件
        $file->move($upload_path,$filename);

        return [
            'path'  =>  $path
        ];

    }
}
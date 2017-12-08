<?php
/**
 * Created by PhpStorm.
 * User: yzx
 * Date: 2017-12-08
 * Time: 16:06
 */

namespace App\Handlers;
use Image;

class ImageUploadHandler
{
    protected $allowed_ext = ['png','jpg','gif','jpeg'];

    /**
     * @param UploadedFile $file 文件对象
     * @param string $folder 保存路径
     * @param string $file_prefix 文件前缀
     * @param bool|int 是否限制大小
     * @return array|bool
     */
    public function save($file,$folder,$file_prefix,$max_width = false)
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

        //如果限制了图片宽度，进行裁剪
        if ($max_width && $extension != 'gif') {
            $this->reduceSize($upload_path . '/' . $filename,$max_width);
        }


        return [
            'path'  =>  $path
        ];

    }

    public function reduceSize($file_path,$max_width)
    {
        $image = Image::make($file_path);

        //调整大小
        $image->resize($max_width,null,function($constraint){
            //设定宽度是$max_width,高度等比绽放
            $constraint->aspectRatio();

            //防止截图时图片尺寸变大
            $constraint->upsize();

        });

        $image->save();
    }
}
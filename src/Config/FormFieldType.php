<?php

namespace App\Config;

enum FormFieldType: int
{
    case String = 1;  // 文本（长度 255）
    case Radio = 2;  // 单选
    case Checkbox = 3;  // 多选
    case Text = 4;  // 长文本
    case Image = 5;  // 图片
}

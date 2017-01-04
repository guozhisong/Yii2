<?php
namespace common\models;

use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'txt, jpg, gif', 'mimeTypes' => 'image/jpeg, image/png'],
        ];
    }


}
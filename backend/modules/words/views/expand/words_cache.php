<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\words\models\ExpandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '生成缓存';
$this->params['breadcrumbs'][] = $this->title;
?>
<div style="width: 200; margin: 0 auto;">
    <a href="<?=\yii\helpers\Url::to(['/words/expand/set-cache'])?>">点击生成缓存</a>
</div>


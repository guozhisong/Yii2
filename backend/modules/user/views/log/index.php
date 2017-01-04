<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

/* @var $this yii\web\View */

$this->title = '日志管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
</div>
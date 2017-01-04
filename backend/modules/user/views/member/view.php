<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Member;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确认要删除吗？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'nickname',
            'email:email',
            'type',
            ['label'=>'状态', 'value'=>Member::getStatus()[$model->status]],
			['label'=>'创建时间', 'value'=>date('Y-m-d', $model->created_at)],
			['label'=>'创建时间', 'value'=>date('Y-m-d', $model->updated_at)],
        ],
    ]) ?>

</div>

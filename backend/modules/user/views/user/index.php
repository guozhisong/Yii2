<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'username',
                'label' => '用户名',
            ],
            [
                'attribute' => 'email',
                'label' => '邮箱',
            ],
            [
                'attribute' => 'roles',
                'label' => '角色',
                'value' => function ($model) {
                    return implode(',', $model->getRoleNames());
                }
            ],
            [
                'attribute' => 'status',
                'label' => '状态',
                'filter' => Html::activeDropDownList($searchModel, 'status', User::getStatus(), ['class' => 'form-control']),
                'value' => function ($model) {
                    return User::getStatus()[$model->status];
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => '创建时间',
                'value' => function ($model) {
                    return date('Y-m-d', $model->created_at);
                }
            ],
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update} {delete}'],
        ],
    ]); ?>
</div>

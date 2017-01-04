<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Member;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\user\models\MemberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <!--<?= Html::a('Create Member', ['create'], ['class' => 'btn btn-success']) ?>-->
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
                'attribute' => 'nickname',
                'label' => '昵称',
            ],
            [
                'attribute' => 'email',
                'label' => '邮箱',
            ],
            [
                'attribute' => 'type',
                'label' => '类型',
            ],
            [
                'attribute' => 'status',
                'label' => '状态',
                'filter' => Html::activeDropDownList($searchModel, 'status', Member::getStatus(), ['class' => 'form-control']),
                'value' => function ($model) {
                    return Member::getStatus()[$model->status];
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => '创建时间',
                'value' => function ($model) {
                    return date('Y-m-d', $model->created_at);
                }
            ],
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}'],
        ],
    ]); ?>
</div>

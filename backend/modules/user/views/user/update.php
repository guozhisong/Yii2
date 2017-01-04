<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use backend\modules\user\models\RoleForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\UserForm */

$this->title = '更新用户: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'status')->DropDownList(User::getStatus()) ?>

    <?= $form->field($model, 'roles')->checkboxList(RoleForm::getRoles()) ?>

    <div class="form-group">
        <?= Html::submitButton('更新', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>

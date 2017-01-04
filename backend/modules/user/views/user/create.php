<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use backend\modules\user\models\RoleForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\user\models\UserForm */

$this->title = '创建用户';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'username')->textInput() ?>

        <?= $form->field($model, 'email')->textInput() ?>

        <?= $form->field($model, 'password')->textInput() ?>

        <?= $form->field($model, 'roles')->checkboxList(RoleForm::getRoles()) ?>

        <div class="form-group">
            <?= Html::submitButton('创建', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

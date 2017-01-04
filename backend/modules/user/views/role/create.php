<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

$this->title = (Yii::$app->controller->action->id == 'create') ? '创建角色' : '修改角色';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $form=ActiveForm::begin()?>
        <table width="100%" class="lr10" align="center">
            <tr>
                <td width="80" align="center">角色名称:</td>
                <td align="center">
                    <?= $form->field($model,'name')->textInput(['maxlength'=>true])->label('')?>
                </td>
            </tr>
            <tr>
                <td width="80" align="center">角色描述:</td> 
                <td align="center">
                    <?= $form->field($model,'description')->label('')->textarea(['rows'=>3])?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <?=Html::submitButton((Yii::$app->controller->action->id == 'create') ? '新建' : '修改', ['class'=>'btn btn-primary'])?>
                </td>
            </tr>
        </table>
    <?php ActiveForm::end()?>

</div>
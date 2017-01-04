<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\words\models\Expand */

$this->title = 'Create Expand';
$this->params['breadcrumbs'][] = ['label' => 'Expands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cateList' => $cateList,
    ]) ?>

</div>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\widgets\ActiveForm;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\words\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .box1{
        position: absolute;
        top: 100px;
        left: 430px;
        width: 300px;
        min-height: 300px;
        background: #eee;
        display: none;
        z-index: 100;
    }
    .expand, .thesuarus, .load{
        display: block;
        width: 200px;
        height: 70px;
        line-height: 70px;
        text-align: center;
        margin: 0 auto;
        font-size: 30px;
        color: #337AB7;
        cursor: pointer;
        padding-top: 30px;
    }
    .box1 i{
        position: absolute;
        left: 282px;
        top: 3px;
    }
    .box1 i:hover{
        cursor: pointer;
    }
    .shade{
        position:fixed;
        opacity: 0.6;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 99;
        background-color: #999;
        display: none;
    }
    #file{
        position: absolute;
        top: 117px;
        left: 520px;
        display: none;
    }
    #submit{
        position: absolute;
        top: 117px;
        left: 750px;
        display: none;
    }
</style>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('添加分类', ['create'], ['class' => 'btn btn-success']) ?>

        <?php $form = ActiveForm::begin([
            'action' => \yii\helpers\Url::to(['/words/expand/load-category-words']),
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data', 'class' => 'form']
        ]); ?>
        <input type="file" name="file" id="file" value="">
        <input type="submit" id="submit" value="确定导入">
        <input type="hidden" class="cid" name="cid" value="">
        <?php ActiveForm::end(); ?>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'pid',
            'name',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php: Y-m-d'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<div class="shade"></div>

<div class="box1">
    <i>X</i>
    <span class="expand" cid="">导出扩展词</span>
    <span class="thesuarus" cid="">导出同义词</span>
    <span class="load">全部导入</span>
</div>

<?php
$expand_url = Url::to(['/words/category/export-current-category-expand']);
$thesuarus_url = Url::to(['/words/category/export-current-category-thesuarus']);
$script = <<<EOT
    //导出当前分类下的所有扩展词
    $('tr').each(function (index) {
        var cid = $(this).find('td').eq(1).html();
        $(this).find('td').eq(3).css({'cursor': 'pointer', 'color': 'blue'});
        $(this).find('td').eq(3).click(function () {
            $('.expand').attr('cid', cid);
            $('.thesuarus').attr('cid', cid);
            $('.cid').val(cid);
            $('.box1').show();
            $('.shade').show();
            popup($('.box1'));
        });
    });

    //导出该分类扩展词
    $('.expand').on('click', function () {
        var cid = $(this).attr('cid');
        location.href = "$expand_url&cid="+cid;
        $('.box1').hide();
        $('.shade').hide();
    });

    //导出该分类同义词
    $('.thesuarus').on('click', function () {
        var cid = $(this).attr('cid');
        location.href = "$thesuarus_url&cid="+cid;
        $('.box1').hide();
        $('.shade').hide();
    });

    //全部导入
    $('.load').on('click', function () {
        $('#file').click();
        $('#file').show();
        $('#submit').show();
        $('.box1').hide();
        $('.shade').hide();
    });

    $('.box1 i').on('click', function () {
        $('.box1').hide();
        $('.shade').hide();
    });


    /*弹窗位置设置*/
    function popup(popupName){
        var _scrollHeight = $(document).scrollTop(),//获取当前窗口距离页面顶部高度
            _windowHeight = $(window).height(),//获取当前窗口高度
            _windowWidth = $(window).width(),//获取当前窗口宽度
            _popupHeight = popupName.height(),//获取弹出层高度
            _popupWeight = popupName.width();//获取弹出层宽度
        _posiTop = (_windowHeight - _popupHeight - 200)/2 + _scrollHeight;
        _posiLeft = (_windowWidth - _popupWeight)/2;
        popupName.css({"left": _posiLeft + "px","top":_posiTop + "px"});//设置position
    }

EOT;

$this->registerJs($script, \yii\web\View::POS_END);
?>
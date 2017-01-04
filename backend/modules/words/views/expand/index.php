<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\widgets\ActiveForm;
use \backend\modules\words\models\Category;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\words\models\ExpandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Expands';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    h2{
        width: 600px;
        text-align: center;
    }
    .box, .box2{
        z-index: 100;
        position: absolute;
        top: 100px;
        left: 300px;
        width: 600px;
        min-height: 400px;
        background: #eee;
    }
    .box2 h2{
        margin-bottom: 50px;
    }
    .search{
        float:left;
        margin: 0 20px 0 100px;
        width: 300px;
        height: 35px;
    }
    .load, .load2{
        list-style: none;
        width: 600px;
        height: auto;
    }
    .load li, .load2 li{
        float: left;
        width: 122px;
        height: 30px;
        line-height: 30px;
        text-align: center;
        margin: 3px 5px;
        background-color: #ccc;
    }
    li:hover{
        cursor: pointer;
    }
    div.button, div.button2{
        width: 220px;
        height:80px;
        margin: 0 auto;
    }
    .clear{
        clear: both;
    }
    .save, .cancel, .save2, .cancel2{
        float: left;
        display: block;
        width: 60px;
        height: 35px;
        line-height: 35px;
        font-size: 20px;
        text-align: center;
        color: #fff;
        margin: 20px;
        cursor: pointer;
    }
    .save, .save2{
        background-color: #337AB7;
    }
    .cancel, .cancel2{
        background-color: #E50D31;
    }
    .box1, .box3, .box4{
        z-index: 100;
        position: absolute;
        top: 150px;
        left: 430px;
        width: 300px;
        min-height: 200px;
        background: #eee;
    }
    .add, .remove, .expand, .thesuarus, .expand1, .thesuarus1{
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
    .box1 i, .box3 i, .box4 i{
        position: absolute;
        left: 282px;
        top: 3px;
    }
    .box1 i:hover, .box3 i:hover, .box4 i:hover{
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
    .loading{
        margin-left: 200px;
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
<div class="expand-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加扩展词', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('筛选导出', 'javascript:void(0)', ['class' => 'export']) ?>
        <?= Html::a('全部导出', 'javascript:void(0)', ['class' => 'exportAll']) ?>
        <?= Html::a('全部导入', 'javascript:void(0)', ['class' => 'loading']) ?>

        <?php $form = ActiveForm::begin([
            'action' => \yii\helpers\Url::to(['load-words']),
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <input type="file" name="file" id="file" value="">
        <input type="submit" id="submit" value="确定导入">
        <?php ActiveForm::end(); ?>

    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'grid-view',
            'style' => 'overflow:auto',
            'id' => 'grid'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'headerOptions' => ['width' => '20']
            ],
            'id',
            [
                'label' => '分类名',
                'attribute' => 'cid',
                'filter' => Html::activeDropDownList($searchModel, 'cid', ['' => '不限'] + Category::getCateList() + [0 => '其他'], ['class' => 'form-control']),
                'value' => function($model) {
                    if ($model->cid) {
                        $str = Category::getCateList()[$model->cid];
                    } else {
                        $str = '其他';
                    }
                    return $str;
                },
                'headerOptions' => ['width' => '100']
            ],
            'name',
            [
                'label' => '同义词名称',
                'attribute' => 'thesaurus_ids',
                'value' => function($model) {
                    $str = '';
                    if (!$model->thesaurus_ids) {
                        return '点击可添加同义词';
                    }
                    foreach(explode(',', $model->thesaurus_ids) as $val) {
                        if (!empty($val)) {
                            $str .= Yii::$app->wordsCache->getKV()[$val] . ',';
                        }
                    }
                    return trim($str, ',');
                },
                'headerOptions' => ['width' => '100']
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php: Y-m-d'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php: Y-m-d'],
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<div class="shade"></div>

<div class="box1" style="display: none;">
    <i>X</i>
    <span class="add" current-id="">添加同义词</span>
    <span class="remove" current-id="">移除同义词</span>
</div>

<div class="box" style="display: none;">
    <h2>添加同义词</h2>
    <input class="search" type="text" name="search" value="" placeholder="请输入扩展词">
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn1 btn-primary']) ?>
    </div>
    <ul class="load"></ul>
    <div class="button">
        <span class="save" current-id="">确定</span>
        <span class="cancel">取消</span>
    </div>
</div>

<div class="box2" style="display: none;">
    <h2>当前已添加的同义词</h2>
    <ul class="load2"></ul>
    <div class="button2">
        <span class="save2" current-id="">确定</span>
        <span class="cancel2">取消</span>
    </div>
</div>

<div class="box3" style="display: none;">
    <i>X</i>
    <span class="expand" ids="">导出扩展词</span>
    <span class="thesuarus" ids="">导出同义词</span>
</div>

<div class="box4" style="display: none;">
    <i>X</i>
    <span class="expand1">导出扩展词</span>
    <span class="thesuarus1">导出同义词</span>
</div>

<?php
$expand_url = \yii\helpers\Url::to(['/words/expand/filter-export-expand']);
$thesuarus_url = \yii\helpers\Url::to(['/words/expand/filter-export-thesuarus']);
$expand_all_url = \yii\helpers\Url::to(['/words/expand/export-all-expand']);
$thesuarus_all_url = \yii\helpers\Url::to(['/words/expand/export-all-thesuarus']);
$script = <<<EOT
    //全部导入
    $('.loading').on('click', function () {
        $('#file').click();
        $('#file').show();
        $('#submit').show();
    });

    //显示导出列表
    $('.export').on('click', function () {
        var ids = $("#grid").yiiGridView("getSelectedRows");
        $('.expand').attr('ids', ids);
        $('.thesuarus').attr('ids', ids);
        $('.box3').show();
        $('.shade').show();
        popup($('.box3'));
    });

    //筛选导出扩展词
    $('.expand').on('click', function () {
        var ids = $(this).attr('ids');
        location.href = "$expand_url&ids="+ids;
        $('.box3').hide();
        $('.shade').hide();
    });

    //筛选导出同义词
    $('.thesuarus').on('click', function () {
        var ids = $(this).attr('ids');
        location.href = "$thesuarus_url&ids="+ids;
        $('.box3').hide();
        $('.shade').hide();
    });

    //显示导出列表
    $('.exportAll').on('click', function () {
        $('.box4').show();
        $('.shade').show();
        popup($('.box4'));
    });

    //导出所有扩展词
    $('.expand1').on('click', function () {
        var ids = $(this).attr('ids');
        location.href = "$expand_all_url";
        $('.box4').hide();
        $('.shade').hide();
    });

    //导出所有同义词
    $('.thesuarus1').on('click', function () {
        var ids = $(this).attr('ids');
        location.href = "$thesuarus_all_url";
        $('.box4').hide();
        $('.shade').hide();
    });



    //点击设定当前行
    $('tr').each(function (index) {
        _this = $(this);
        _this.find('td').eq(4).css({'cursor': 'pointer', 'color': 'blue'});
        if (index > 1) {
            _this.find('td').eq(4).click(function () {
            //定义全局变量
            this_td = $(this);
            _current_id = this_td.parent().find('td:nth-child(2)').html();
            console.log($('.header').html());
            $('iframe').prop('scrolling', 'no');
            $('.box1').show();
            $('.shade').show();
            popup($('.box1'));
            $('.save').attr('current-id', _current_id);
            $('.save2').attr('current-id', _current_id);
            $('.add').attr('current-id', _current_id);
            $('.remove').attr('current-id', _current_id);
        });
        }
    });

    //添加同义词进入搜索添加页面
    $('.add').on('click', function () {
        $('.box1').hide();
        $('.box').show();
        $('.shade').show();
        popup($('.box'));
    });

    //点击搜索，获取搜索的值并展示
    $('.btn1').click(function () {
        var search_val = $('.search').val();
        $.get('index.php?r=words/expand/ajax-search', {'ExpandSearch[name]': search_val, 'ExpandSearch[id]': _current_id}, function (data) {
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += "<li class='li' data-id=" + data[i]['id'] +">" + data[i]['name'] + "</li>";
            }
            html += "<div class='clear'></div>";
            $('.load').append(html);
        }, 'json');
    });

    //移除同义词，显示移除页面并获取当前同义词列表
    $('.remove').on('click', function () {console.log(this_td.text());
        var current_id = $(this).attr('current-id');
        if (this_td.text() === '点击可添加同义词') {
            $('.box1').hide();
            $('.shade').hide();
            alert('当前没有可移除的同义词!');
            return ;
        }
        $.get('index.php?r=words/expand/ajax-get-thesaurus', {'ExpandSearch[id]': current_id}, function (data) {
            var html = '';
            for (var key in data) {
                html += "<li data-id=" + key +">" + data[key] + "</li>";
            }
            html += "<div class='clear'></div>";
            $('.load2').html('');
            $('.load2').append(html);
        }, 'json');
        $('.box1').hide();
        $('.box2').show();
        $('.shade').show();
        popup($('.box2'));
    });

    //点击选中，再点击取消
    $('.load, .load2').on('click', 'li', function () {
        if ($(this).hasClass('select')) {
            $(this).removeClass('select');
            $(this).css('background-color', '#ccc');
        } else {
            $(this).addClass('select');
            $(this).css('background-color', '#337AB7');
        }
    });

    //添加同义词保存
    $('.save').on('click', function () {
        var current_id = $(this).attr('current-id'),
            ids = '';
        $('.li').each(function () {
            if ($(this).hasClass('select')) {
                var id = $(this).attr('data-id');
                ids += id + ',';
            }
        });
        $.get('index.php?r=words/expand/ajax-add-thesaurus', {'current_id': current_id, 'ids': ids}, function (data) {
            if (data['status']) {
                this_td.text(data['names']);
                $('.box').hide();
                $('.shade').hide();
            } else {
                alert('添加失败!');
            }
        }, 'json');
    });

    //移除同义词保存
    $('.save2').on('click', function () {
        var current_id = $(this).attr('current-id'),
            ids = '';
        $('.load2 li').each(function () {
            if ($(this).hasClass('select')) {
                var id = $(this).attr('data-id');
                ids += id + ',';
            }
        });
        $.get('index.php?r=words/expand/ajax-remove-thesaurus', {'current_id': current_id, 'ids': ids}, function (data) {
            if (data['status']) {
                this_td.text(data['names']);
                $('.box2').hide();
                $('.shade').hide();
            } else {
                alert('移除失败!');
            }
        }, 'json');
    });

    //添加同义词弹窗隐藏
    $('.cancel').on('click', function () {
        $('.box').hide();
        $('.shade').hide();
    });

    //导出弹窗隐藏
    $('.box1 i').on('click', function () {
        $('.box1').hide();
        $('.shade').hide();
    });

    //导出弹窗隐藏
    $('.box3 i').on('click', function () {
        $('.box3').hide();
        $('.shade').hide();
    });

    //导出弹窗隐藏
    $('.box4 i').on('click', function () {
        $('.box4').hide();
        $('.shade').hide();
    });

    //移除同义词弹窗隐藏
    $('.cancel2').on('click', function () {
        $('.box2').hide();
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
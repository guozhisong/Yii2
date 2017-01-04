<?php
use yii\helpers\Html;
use yii\helpers\Url;
//use app\assets\AppAsset;
//use yii\widgets\ActiveForm;
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>后台管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?= Html::cssFile('@web_backend_css/dpl-min.css') ?>
    <?= Html::cssFile('@web_backend_css/bui-min.css') ?>
    <?= Html::cssFile('@web_backend_css/main-min.css') ?>
    <?= Html::jsFile('@web_backend_js/jquery-1.8.1.min.js') ?>
    <?= Html::jsFile('@web_backend_js/bui-min.js') ?>
    <?= Html::jsFile('@web_backend_js/main-min.js') ?>
    <?= Html::jsFile('@web_backend_js/config-min.js') ?>
</head>
<body>
    <div class="header">
        <div class="dl-title">
            <!--<img src="/chinapost/Public/assets/img/top.png">-->
        </div>
        
        <div class="dl-log">
            欢迎您，<span class="dl-log-user" id="<?= Yii::$app->user->getId() ?>"><?= Yii::$app->user->identity->username ?>(<?= Yii::$app->user->identity->username ?>)</span>   <span class="glyphicon glyphicon-envelope"></span>  <span class="badge" id="msgnum"><?php if(Yii::$app->session->has('msg')): ?> <?= Yii::$app->session->get('msg') ?><?php else: ?>0<?php endif ?></span>  <a href="<?= Url::to(['site/logout']) ?>" title="退出系统" class="dl-log-quit">[退出]</a>
        </div>
        <div class="dl-log"><a href='/' target="_blank" title="网站首页" class="dl-log-quit">网站首页</a></div>
    </div>
    <div class="content">
        <div class="dl-main-nav">
            <div class="dl-inform"><div class="dl-inform-title"><s class="dl-inform-icon dl-up"></s></div></div>
            <ul id="J_Nav"  class="nav-list ks-clear">
                <li class="nav-item dl-selected"><div class="nav-item-inner nav-home">系统管理</div></li>       
                <li class="nav-item dl-selected"><div class="nav-item-inner nav-user">用户管理</div></li>
                <li class="nav-item dl-selected"><div class="nav-item-inner nav-monitor">词库管理</div></li>
                <li class="nav-item dl-selected"><div class="nav-item-inner nav-supplier">预留2</div></li>
                <li class="nav-item dl-selected"><div class="nav-item-inner nav-order">预留3</div></li>
            </ul>
        </div>
        <ul id="J_NavContent" class="dl-tab-conten">

        </ul>
    </div>

    <script>
        BUI.use('common/main',function(){
            var config = [
                {id:'1',menu:[
                    {text:'系统管理',items:[
                        {id:'101',text:'网站设置',href:"<?= Yii::$app->urlManager->createUrl('system/config/site') ?>"}
                    ]},
                    {text:'类别管理',items:[
                        {id:'111',text:'地区管理',href:"<?= Yii::$app->urlManager->createUrl('system/area/index') ?>"}
                    ]}
                ]},
                {id:'2',menu:[
                    {text:'后台用户',items:[
                        {id:'201',text:'用户管理',href:"<?= Yii::$app->urlManager->createUrl('user/user/index') ?>"},
                        {id:'202',text:'角色管理',href:"<?= Yii::$app->urlManager->createUrl('user/role/index') ?>"},
                        {id:'203',text:'节点管理',href:"<?= Yii::$app->urlManager->createUrl('user/node/index') ?>"}
                    ]},
                    {text:'前台用户',items:[
                        {id:'211',text:'会员管理',href:"<?= Yii::$app->urlManager->createUrl('user/member/index') ?>"}
                    ]},
                    {text:'日志管理',items:[
                        {id:'221',text:'日志管理',href:"<?= Yii::$app->urlManager->createUrl('user/log/index') ?>"}
                    ]},
                ]},
                {id:'3',menu:[
                    {text:'分类管理',items:[
                        {id:'301',text:'添加分类',href:"<?= Yii::$app->urlManager->createUrl('words/category/create') ?>"},
                        {id:'302',text:'分类列表',href:"<?= Yii::$app->urlManager->createUrl('words/category/index') ?>"}
                    ]},
                    {text:'主词管理',items:[
                        {id:'311',text:'添加主词',href:"<?= Yii::$app->urlManager->createUrl('words/expand/create') ?>"},
                        {id:'312',text:'主词列表',href:"<?= Yii::$app->urlManager->createUrl('words/expand/index') ?>"}
                    ]},
                    {text:'缓存管理',items:[
                        {id:'321',text:'生成缓存',href:"<?= Yii::$app->urlManager->createUrl('words/expand/cache') ?>"}
                    ]}
                ]},
                {id:'4',menu:[
                    {text:'预留2',items:[
                        {id:'401',text:'预留2',href:"#"},
                        {id:'402',text:'预留2',href:"#"}
                    ]}
                ]},
                {id:'5',menu:[
                    {text:'预留3',items:[
                        {id:'501',text:'预留3',href:"#"},
                        {id:'502',text:'预留3',href:"#"}
                    ]}
                ]}
            ];
            new PageUtil.MainPage({
                modulesConfig : config
            });
        });
    </script>
</body>
</html>
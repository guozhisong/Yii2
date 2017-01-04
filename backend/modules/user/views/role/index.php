<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

/* @var $this yii\web\View */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建角色', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
                <th align="center">角色名称</th>
                <th align="center">角色描述</th>
                <th align="center">操 作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($roles as $v){?>
            <tr style="height:40px;">
                <td align="center"><?= $v->name ?></td>
                <td align="center"><?= $v->description ?></td>
                <td align="center">
                        
                <?php  
                    $user = User::find()->where(['id'=>Yii::$app->user->id])->one();
                    //超级管理员才有删除权限$user['role']为1时是超级管理员，
                    //管理员表中需要加入两个字段roleid和role，roleid是不通过Rbac分配的等级，role则是Rbac分配的等级角色
                    if($user['username']=='admin001'):
                ?>
                <a href="<?= Url::to(['/user/role/update', 'name'=>$v->name]) ?>">修改</a>
                &nbsp;
                <a href="<?= Url::to(['/user/role/rolenode', 'name'=>$v->name]) ?>">权限</a>
                &nbsp;
                <?= Html::a('删除',['/user/role/delete', 'name'=>$v->name],[
                    'data' => [
                        'confirm' => '确认删除吗？',
                        'method' => 'post',
                    ]
                ])?>
                <?php else:?>
                <h3><font color="red">不可修改,若要修改请联系管理员</font></h3>
                <?php endif;?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>

</div>
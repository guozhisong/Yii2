<?php

namespace backend\modules\user\controllers;

use Yii;
use backend\controllers\BackendBaseController;
use backend\modules\user\models\NodeForm;

/**
 * Node controller for the `user` module
 */
class NodeController extends BackendBaseController
{
    public $layout = '/admin';
    
    /**
     * 节点列表
     * @return string
     */
    public function actionIndex()
    {
        $authManager = Yii::$app->authManager;
        $nodes = $authManager->getPermissions();
        
        return $this->render('index',[
            'nodes'=>$nodes
        ]);
    }

    /**
     * 创建节点
     * @return string
     */
    public function actionCreate()
    {
        //节点表单
        $model = new NodeForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //操作日志
            //$content = '创建了'.$model['name'].'节点';
            //$operate = Operate::add_operate($content);
            //return $this->_message('节点添加成功','/setting/nodeindex');
            return $this->redirect(['index']);
        } else {
            return $this->render('create',[
                'model'=>$model
            ]);
        }
    }

    //修改节点
    public function actionUpdate($name)
    {
        $authManager = Yii::$app->authManager;
        //是否有下级
        $child = $authManager->getChildren($name);
        //如果有下级不允许修改
        if ($child) {
            //return $this->_message('节点有子节点,不能修改','/setting/nodeindex');
            throw new \yii\web\MethodNotAllowedHttpException('节点有子节点，不能修改！');
        }
        //获取节点
        $node = $authManager->getPermission($name);
        if(!$node) return false;
                
        //节点表单
        $model = new NodeForm();
        //节点名称
        $model->name = $node->name;
        //节点描述
        $model->description = $node->description;
                
        if ($model->load(Yii::$app->request->post()) && $model->update($name)) {
            //操作日志
            //$content = '修改了节点名'.$name.'为'.$model['name'].'节点';
            //$operate = Operate::add_operate($content);
            //return $this->_message('节点修改成功','/setting/nodeindex');
            return $this->redirect(['index']);
        } else {
            return $this->render('create',[
                'model'=>$model
            ]);
        }
    }

    //删除节点
    public function actionDelete($name)
    {
        $authManager = Yii::$app->authManager;
        //是否有子节点
        $child = $authManager->getChildren($name);
        //有子节点不能删除
        if ($child) {
            //return $this->_message('节点有子节点,不能删除','/setting/nodeindex');
            throw new \yii\web\MethodNotAllowedHttpException('节点有子节点，不能删除！');
        }
        //获取节点
        $node = $authManager->getPermission($name);

        if (!$node) return false;

        if ($authManager->remove($node)) {
            //操作日志
            //$content = '删除了'.$name.'节点';
            //$operate = Operate::add_operate($content);
            //return $this->_message('节点删除成功','/setting/nodeindex');
            return $this->redirect(['index']);
        } else {
            //return $$this->_message('节点删除失败','/setting/nodeindex');
            return $this->redirect(['index']);
        }
    }


}
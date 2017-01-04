<?php

namespace backend\modules\user\controllers;

use Yii;
use backend\controllers\BackendBaseController;
use backend\modules\user\models\RoleForm;

/**
 * Role controller for the `user` module
 */
class RoleController extends BackendBaseController
{
    public $layout = '/admin';
    
    /**
     * 角色列表
     * @return string
     */
    public function actionIndex()
    {
        $authManager = Yii::$app->authManager;
        $roles = $authManager->getRoles();
        
        return $this->render('index',[
            'roles'=>$roles
        ]);
    }

    /**
     * 创建角色
     * @return string
     */
    public function actionCreate()
    {
        //角色表单
        $model = new RoleForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //操作日志
            //$content = '创建了'.$model['name'].'角色';
            //$operate = Operate::add_operate($content);
            //return $this->_message('角色添加成功','/setting/roleindex');
            return $this->redirect(['index']);
        } else {
            return $this->render('create',[
                'model'=>$model
            ]);
        }
    }

    /**
     * 修改角色
     * @return string
     */
    public function actionUpdate($name)
    {
        $authManager = Yii::$app->authManager;
        //是否有下级
        $child = $authManager->getChildren($name);
        //如果有下级不允许修改
        if ($child) {
            //return $this->_message('节点有子角色,不能修改','/setting/roleindex');
            throw new \yii\web\MethodNotAllowedHttpException('角色已被赋予权限，不能修改！');
        }
        //获取角色
        $role = $authManager->getRole($name);
        if(!$role) return false;
                
        //角色表单
        $model = new RoleForm();
        //角色名称
        $model->name = $role->name;
        //角色描述
        $model->description = $role->description;
                
        if ($model->load(Yii::$app->request->post()) && $model->update($name)) {
            //操作日志
            //$content = '修改了角色名'.$name.'为'.$model['name'].'角色';
            //$operate = Operate::add_operate($content);
            //return $this->_message('角色修改成功','/setting/roleindex');
            return $this->redirect(['index']);
        } else {
            return $this->render('create',[
                'model'=>$model
            ]);
        }
    }

    /**
     * 删除角色
     * @return string
     */
    public function actionDelete($name)
    {
        $authManager = Yii::$app->authManager;
        //是否有子角色
        $child = $authManager->getChildren($name);
        //有子角色不能删除
        if ($child) {
            //return $this->_message('节点有子角色,不能删除','/setting/roleindex');
            throw new \yii\web\MethodNotAllowedHttpException('角色已被赋予权限，不能删除！');
        }
        //获取角色
        $role = $authManager->getRole($name);

        if (!$role) return false;

        if ($authManager->remove($role)) {
            //操作日志
            //$content = '删除了'.$name.'角色';
            //$operate = Operate::add_operate($content);
            //return $this->_message('角色删除成功','/setting/roleindex');
            return $this->redirect(['index']);
        } else {
            //return $$this->_message('角色删除失败','/setting/roleindex');
            return $this->redirect(['index']);
        }
    }

    /**
     * 为角色赋予权限
     * @return string
     */
    public function actionRolenode($name)
    {
        $authManager = Yii::$app->authManager;
        $role = $authManager->getRole($name);
        if (!$role) {
            throw new \yii\web\NotFoundHttpException('抱歉，角色未找到！');
        }
        if (Yii::$app->request->isPost) {
            $nodes = Yii::$app->request->post('node');
            $authManager->removeChildren($role);

            if (!empty($nodes) && is_array($nodes)) {
                foreach ($nodes as $v) {
                    $node = $authManager->getPermission($v);
                    if (!$node) continue;
                    $authManager->addChild($role, $node);
                }
            }

            return $this->redirect(['index']);
        }
        $roleNodes = $authManager->getPermissionsByRole($name);
        $roleNodes = array_keys($roleNodes);
        $nodes = $authManager->getPermissions();
            
        return $this->render('rolenode',[
            'nodes'=>$nodes,
            'roleNodes'=>$roleNodes,
            'name'=>$name
        ]);
    }

}
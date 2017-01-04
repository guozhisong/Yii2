<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
/**
 * BackendBase controller
 */
class BackendBaseController extends Controller
{
    public static $notValidatePermissions = ['app-backend_site_login'];
    public $layout  = '/admin';
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    public function actionError()
    { 
        if (\Yii::$app->exception !== null) {
            return $this->render('error', ['exception' => \Yii::$app->exception]);
        }
    }
   
    public function beforeAction($action)
    {
        if (empty(Yii::$app->user->id)) {
            $this->redirect(['/site/login']);
            return false;
        }

        /*
        if (Yii::$app->user->identity->group_type != USER_GROUP_TYPE_ADMIN) {            
            Yii::$app->controller->redirect(['/site/login']);
            throw new \yii\web\NotFoundHttpException('账号类型不是管理员帐号');
            return false;
        }
        */

        $moduleId     = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $actionId     = Yii::$app->controller->action->id;

        $currIndex    = $moduleId . PERMISSIONS_SEPARATOR . $controllerId . PERMISSIONS_SEPARATOR . $actionId;
        $roles        = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

        if (in_array($currIndex, static::$notValidatePermissions)) {
            return true;
        }

        if (!empty($roles) && $roleName = array_keys($roles)[0]) {
            if ($roleName == ADMINISTRATOR) {
                return true;
            }
        }

        if ($roles == ADMINISTRATOR) {
            return true;
        }
        
        if (Yii::$app->user->can($currIndex)) {
            return true;
        } else {
            header("Content-type:text/html;charset=utf-8");
            throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
            return false;
        }
    }

}
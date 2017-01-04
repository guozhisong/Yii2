<?php

namespace backend\modules\user\controllers;

use yii\web\Controller;

/**
 * Default controller for the `user` module
 */
class LogController extends Controller
{
    public $layout = '/admin';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

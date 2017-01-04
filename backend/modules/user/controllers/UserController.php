<?php

namespace backend\modules\user\controllers;

use Yii;
use yii\filters\VerbFilter;
use backend\controllers\BackendBaseController;
use backend\modules\user\models\UserForm;
use backend\modules\user\models\UserSearch;
use common\models\User;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BackendBaseController
{
    public $layout = '/admin';
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => UserForm::findOne($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $formModel = new UserForm();
        $formModel->setScenario('create');

        if ($formModel->load(Yii::$app->request->post()) && ($model = $formModel->save())) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $formModel,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $formModel = UserForm::findOne($id);
        if ($formModel === null) {
            throw new \yii\web\NotFoundHttpException('抱歉，页面不存在！');
        }
        $formModel->setScenario('update');

        if ($formModel->load(Yii::$app->request->post()) && ($model = $formModel->update())) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $formModel,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        UserForm::findOne($id)->delete();

        return $this->redirect(['index']);
    }
}
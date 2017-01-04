<?php

namespace backend\modules\words\controllers;

use backend\modules\words\models\Category;
use common\models\UploadForm;
use Yii;
use backend\modules\words\models\Expand;
use backend\modules\words\models\ExpandSearch;
use backend\controllers\BackendBaseController;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ExpandController implements the CRUD actions for Expand model.
 */
class ExpandController extends BackendBaseController
{
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
     * Lists all Expand models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExpandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //搜索需要添加的同义词
    public function actionAjaxSearch()
    {
        $searchModel = new ExpandSearch();
        $list = $searchModel->ajaxSearch(Yii::$app->request->queryParams);

        echo json_encode($list);
    }

    //添加同义词
    public function actionAjaxAddThesaurus()
    {
        $params = Yii::$app->request->get();
        $searchModel = new Expand();
        $result = $searchModel->ajaxAddThesaurus($params);

        echo json_encode($result);
    }

    //获取当前的的同义词
    public function actionAjaxGetThesaurus()
    {
        $searchModel = new ExpandSearch();

        Yii::$app->wordsCache->setKV();
        $list = $searchModel->ajaxGetThesaurus(Yii::$app->request->queryParams);
        $arr = [];
        foreach (explode(',', $list['thesaurus_ids']) as $val) {
            if (!empty(Yii::$app->wordsCache->getKV())) {
                $arr[$val] = Yii::$app->wordsCache->getKV()[$val];
            }
        }

        echo json_encode($arr);
    }

    //移除同义词
    public function actionAjaxRemoveThesaurus()
    {
        $params = Yii::$app->request->get();
        $searchModel = new Expand();
        $result = $searchModel->ajaxRemoveThesaurus($params);

        echo json_encode($result);
    }

    //筛选导出扩展词
    public function actionFilterExportExpand()
    {
        $params = Yii::$app->request->get();
        $result = Expand::filterExportExpand($params);
        static::export($result, 'filter_export_expand');
    }

    //筛选导出同义词
    public function actionFilterExportThesuarus()
    {
        $params = Yii::$app->request->get();
        $result = Expand::filterExportThesuarus($params);
        static::export($result, 'filter_export_thesuarus');
    }

    //导出所有扩展词
    public function actionExportAllExpand()
    {
        $result = Expand::exportAllExpand();
        static::export($result, 'export_all_expand');
    }

    //导出所有同义词
    public function actionExportAllThesuarus()
    {
        $result = Expand::exportAllThesuarus();
        static::export($result, 'export_all_thesuarus');
    }

    //自定义导出
    public static function export($str, $filename)
    {
        header( "Content-type: application/txt; charset=utf-8");
        header( "Content-Disposition: attachment; filename=$filename.txt");
        echo $str;
    }

    //导入词库(不分类)
    public function actionLoadWords()
    {
        $filepath = Yii::getAlias('@backend') . '/web/load_words.txt';
        $upload_result = $this->uploadFile($_FILES, $filepath);
        if ($upload_result) {
            $this->readLineExpandText($filepath, 'load_error.log');
            Yii::$app->getSession()->setFlash('success', '导入完成！');
            $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('error', '上传失败！');
            $this->redirect(['index']);
        }
    }

    //导入词库(分类)
    public function actionLoadCategoryWords()
    {
        $cid = Yii::$app->request->post('cid');
        $filepath = Yii::getAlias('@backend') . '/web/load_category_words.txt';
        $upload_result = $this->uploadFile($_FILES, $filepath);
        if ($upload_result) {
            $this->readLineExpandText($filepath, 'load_category_error.log', $cid);
            Yii::$app->getSession()->setFlash('success', '导入完成！');
            $this->redirect(['index']);
        } else {
            Yii::$app->getSession()->setFlash('error', '上传失败！');
            $this->redirect(['index']);
        }
    }

    //上传文件
    public function uploadFile($file, $filepath)
    {
        $type = ['application/octet-stream', 'text/plain'];
        if (in_array($file["file"]["type"], $type) && ($file["file"]["size"] < 1024 * 1024)) {
            if ($file["file"]["error"] > 0) {
                throw new BadRequestHttpException($file["file"]["error"]);
            } else {
                return move_uploaded_file($file["file"]["tmp_name"], $filepath);
            }
        }
        else {
            throw new BadRequestHttpException('非法的文件，只能上传 txt 文件且小于10M!');
        }
    }

    //逐行读取扩展词文本内容,并入库
    public function readLineExpandText($filepath, $log_filename, $cid = 0)
    {
        set_time_limit(0);
        if (file_exists($filepath)) {
            $fp = fopen($filepath, 'r');
//            $data_arr = [];
//            $name_arr = [];
//            $count = 0;
            while (!feof($fp)) {
                $str = fgets($fp);
                $str = trim($str, "\r\n");
                //去除BOM头
                $str = $this->checkBOM($str);
                if ($str != '') {
                    $arr = explode(',', $str);
                    $model = new Expand();
                    $model->name = $arr[0];
                    $model->cid = $cid;
                    //判断主词是否存在
                    $objData = Expand::findOne(['name' => $arr[0]]);
                    if ($objData) {
                        unset($arr[0]);
                        if (!empty($arr)) {
                            $newThesaurus = $this->createThesuarus($arr, $log_filename);
                            if ($newThesaurus) {
                                //获取最终的同义词id字符串
                                $objData->thesaurus_ids .= ',' . $newThesaurus;
                                $objData->thesaurus_ids = trim($objData->thesaurus_ids, ',');
                                if (!$objData->save()) {
                                    $save_path = Yii::getAlias('@log') . '/' . date('Ymd') . '/' . $log_filename;
                                    $this->createLogDir($save_path);
                                    static::writeErrors($objData->getErrors(), $save_path);
                                }
                            }

//                            //将已添加的同义词id字符串转换成name字符串，并组装成数组
//                            $thesaurus_names_old = [];
//                            if ($objData->thesaurus_ids) {
//                                Yii::$app->wordsCache->setKV();
//                                foreach (explode(',', $objData->thesaurus_ids) as $val) {
//                                    $thesaurus_names_old[] = Yii::$app->wordsCache->getKV()[$val];
//                                }
//                                $objData->thesaurus_ids .= ',';
//                            }
//                            //获取新的同义词
//                            //将新的同义词已经添加过的去除掉
//                            $diffData = array_diff($arr, $thesaurus_names_old);
//                            if (!empty($diffData)) {
//                                //获取最终的同义词id字符串
//                                $objData->thesaurus_ids .= $this->createThesuarus($diffData, $log_filename);
//
//                                if (!$objData->save()) {
//                                    $save_path = Yii::getAlias('@log') . '/' . date('Ymd') . '/' . $log_filename;
//                                    $this->createLogDir($save_path);
//                                    static::writeErrors($objData->getErrors(), $save_path);
//                                }
//                            }
                        } else {
                            if (!$model->save()) {
                                $save_path = Yii::getAlias('@log') . '/' . date('Ymd') . '/' . $log_filename;
                                $this->createLogDir($save_path);
                                static::writeErrors($model->getErrors(), $save_path);
                            }
                        }

                    } else {
//                        $ids = '';
//                        $first = $arr[0];
                        //如果数组长度大于1，则说明有同义词
                        if (count($arr) > 1) {
                            //获取新的同义词
                            unset($arr[0]);
                            //获取最终的同义词id字符串
                            $model->thesaurus_ids = $this->createThesuarus($arr, $log_filename);
//                            $ids = $this->createThesuarus($arr, $log_filename);
                        }
//                        if (!in_array($first, $name_arr)) {
//                            $name_arr[] = $first;
//                            $data_arr[] = [$first, $cid, $ids];
//                            $count++;
//                            if ($count >= 1000) {
//                                Yii::$app->db->createCommand()->batchInsert(Expand::tableName(), ['name', 'cid', 'thesaurus_ids'], $data_arr)->execute();
//                                unset($data_arr);
//                                $count = 0;
//                                $name_arr = [];
//                            }
//                        }
                        if (!$model->save()) {
                            $save_path = Yii::getAlias('@log') . '/' . date('Ymd') . '/' . $log_filename;
                            $this->createLogDir($save_path);
                            static::writeErrors($model->getErrors(), $save_path);
                        }
                    }
                }
            }
//            if (!empty($data_arr)) {
////                var_dump($data_arr);echo '<br><br><br><br><br><br><br><br><br><br>';
//                Yii::$app->db->createCommand()->batchInsert(Expand::tableName(), ['name', 'cid', 'thesaurus_ids'], $data_arr)->execute();
//            }
        }
    }

    //如果同义词词库中不存在，则创建并获取id，如果存在直接获取id
    public function createThesuarus($arr, $log_filename)
    {
        $thesuarus_ids = '';
        foreach ($arr as $item) {
            $objData = Expand::findOne(['name' => $item]);
            if (!$objData) {
                $model = new Expand();
                $model->name = $item;
                $model->cid = 0;
                if (!$model->save()) {
                    $save_path = Yii::getAlias('@log') . '/' . date('Ymd') . '/' . $log_filename;
                    $this->createLogDir($save_path);
                    static::writeErrors($model->getErrors(), $save_path);
                } else {
                    $thesuarus_ids .= $model->attributes['id'] . ',';
                }
            } else {
                $thesuarus_ids .= $objData->id . ',';
            }
        }

        return trim($thesuarus_ids, ',');
    }

    public function checkBOM($contents)
    {
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
             return substr($contents, 3);
        }

        return $contents;
    }

    //递归创建日志目录，并写入错误信息
    public function createLogDir($save_path)
    {
        if (!is_dir(dirname($save_path))) {
            @mkdir(dirname($save_path), 0777, true);
        }
    }

    //写入日志
    public static function writeErrors($errors, $save_path)
    {
        $error_info = '';
        foreach ($errors as $val) {
            $error_info .= $val[0];
        }
        $error_info = $error_info. "\r\n";
        file_put_contents($save_path, $error_info, FILE_APPEND);
    }

    public function actionCache()
    {
        return $this->render('words_cache');
    }

    //生成词库缓存
    public function actionSetCache()
    {
        Yii::$app->wordsCache->setKV();
        Yii::$app->getSession()->setFlash('success', '缓存生成完成！');
        $this->redirect(['cache']);
    }

    /**
     * Displays a single Expand model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Expand model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Expand();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            //获取分类
            $cateList = Category::getCateList();

            return $this->render('create', [
                'model' => $model,
                'cateList' => $cateList,
            ]);
        }
    }

    /**
     * Updates an existing Expand model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $cateList = Category::getCateList();
            return $this->render('update', [
                'model' => $model,
                'cateList' => $cateList,
            ]);
        }
    }

    /**
     * Deletes an existing Expand model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Expand model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Expand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expand::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

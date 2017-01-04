<?php

namespace backend\modules\words\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "word_expand".
 *
 * @property string $id
 * @property string $cid
 * @property string $name
 * @property string $thesaurus
 * @property string $created_at
 * @property string $updated_at
 */
class Expand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'word_expand';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['thesaurus_ids'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['cid', 'name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cid' => '分类ID',
            'name' => '扩展词名称',
            'thesaurus_ids' => '同义词ID',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function ajaxRemoveThesaurus($params)
    {
        Yii::$app->wordsCache->setKV();
        $obj = static::findOne($params['current_id']);
        $obj->thesaurus_ids = implode(',', array_diff(explode(',', $obj->thesaurus_ids), explode(',', trim($params['ids'], ','))));
        $arr = [];
        if ($obj->thesaurus_ids) {
            foreach (explode(',', $obj->thesaurus_ids) as $val) {
                if (!empty(Yii::$app->wordsCache->getKV())) {
                    $arr[$val] = Yii::$app->wordsCache->getKV()[$val];
                }
            }
        }
        $names = empty($arr) ? '点击可添加同义词' : implode(',', $arr);
        if ($obj->update()) {
            return ['status' => true, 'ids' => $obj->thesaurus_ids, 'names' => $names];
        } else {
            return ['status' => false];
        }
    }

    public function ajaxAddThesaurus($params)
    {
        Yii::$app->wordsCache->setKV();
        $obj = static::findOne($params['current_id']);
        $obj->thesaurus_ids .= ',' . $params['ids'];
        $obj->thesaurus_ids = static::distinctData(trim($obj->thesaurus_ids, ','));
        $arr = [];
        foreach (explode(',', $obj->thesaurus_ids) as $val) {
            if (!empty(Yii::$app->wordsCache->getKV())) {
                $arr[$val] = Yii::$app->wordsCache->getKV()[$val];
            }
        }

        if ($obj->update()) {
            return ['status' => true, 'ids' => $obj->thesaurus_ids, 'names' => implode(',', $arr)];
        } else {
            return ['status' => false];
        }
    }

    //筛选导出扩展词
    public static function filterExportExpand($params)
    {
        Yii::$app->wordsCache->setKV();
        return static::getNameById($params['ids'], "\r\n");
    }

    //筛选导出同义词
    public static function filterExportThesuarus($params)
    {
        Yii::$app->wordsCache->setKV();
        $ids = $params['ids'];
        $list = static::find()->select(['ids' => "CONCAT(id, ',', thesaurus_ids)"])->where("id in ($ids)")->orderBy('id asc')->asArray()->all();

        return static::getNamesByIds($list);
    }

    //导出所有的扩展词
    public static function exportAllExpand()
    {
        $list = static::find()->select('name')->orderBy('id asc')->asArray()->all();
        if (!empty($list)) return implode("\r\n", array_column($list, 'name'));
    }

    //导出所有的同义词
    public static function exportAllThesuarus()
    {
        Yii::$app->wordsCache->setKV();
        $list = static::find()->select(['ids' => "CONCAT(id, ',', thesaurus_ids)"])->orderBy('id asc')->asArray()->all();
        return static::getNamesByIds($list);
    }

    //导出当前分类的扩展词
    public static function exportCurrentCategoryExpand($cid)
    {
        $list = static::find()->select('name')->where(['cid' => $cid['cid']])->orderBy('id asc')->asArray()->all();
        if (!empty($list)) return implode("\r\n", array_column($list, 'name'));
    }

    //导出当前分类的同义词
    public static function exportCurrentCategoryThesuarus($cid)
    {
        $list = static::find()->select(['ids' => "CONCAT(id, ',', thesaurus_ids)"])->where(['cid' => $cid['cid']])->orderBy('id asc')->asArray()->all();
        return static::getNamesByIds($list);
    }

    //将同义词列表中的ID转为中文
    public static function getNamesByIds($list)
    {
        $str = '';
        if (!empty($list)) {
            foreach ($list as $item) {
                $str .= static::getNameById($item['ids'], ',') . "\r\n";
            }
        }

        return $str;
    }

    //去除重复的内容
    public static function distinctData($str)
    {
        $arr = explode(',', $str);
        $arr = array_unique($arr);

        return implode(',', $arr);
    }

    //通过ID获取NAME值，并拼接成字符串
    public static function getNameById($ids, $sign)
    {
        $str = '';
        if (!empty($ids)) {
            foreach (explode(',', $ids) as $val) {
                if (!empty($val)) {
                    $str .= Yii::$app->wordsCache->getKV()[(int)$val] . $sign;
                }
            }
        }

        return trim($str, ',');
    }

}

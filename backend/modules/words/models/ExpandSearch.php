<?php

namespace backend\modules\words\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\words\models\Expand;

/**
 * ExpandSearch represents the model behind the search form about `backend\modules\words\models\Expand`.
 */
class ExpandSearch extends Expand
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cid', 'created_at', 'updated_at'], 'integer'],
            [['name', 'thesaurus_ids'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Expand::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
//            'sort' => [
//                'defaultOrder' => [
//                    'created_at' => SORT_DESC,
//                ]
//            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cid' => $this->cid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'thesaurus_ids', $this->thesaurus_ids]);

        return $dataProvider;
    }

    //搜索需要添加的同义词
    public function ajaxSearch($params)
    {
        $this->load($params);
        $list = static::find()->select(['ids' => "CONCAT(id, ',', thesaurus_ids)"])->where(['id' => $this->id])->asArray()->one();
        $ids = trim($list['ids'], ',');

        return Expand::find()->select('id, name')->where(['like', 'name', $this->name])->andWhere("id not in ($ids)")->asArray()->all();
    }

    //获取当前的同义词
    public function ajaxGetThesaurus($params)
    {
        $this->load($params);

        return Expand::find()->select('thesaurus_ids')->where(['id' => $this->id])->asArray()->one();
    }


}

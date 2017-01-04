<?php
        
namespace backend\modules\user\models;
        
use Yii;
use yii\base\Model;
use yii\helpers\Html;
        
class NodeForm extends Model
{
    public $name;
    public $description;
        
    public function rules()
    {
        return [
            [['name'],'string','max'=>20],
            [['name','description'],'required'],
            ['description','filter','filter'=>function($value){
                return Html::encode($value);
            }],
        ];
    }
            
    //自动设置 created_at updated_at
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }
        
        
    public function attributeLabels()
    {
        return [
            'name'=>'节点名称',
            'description'=>'节点描述',
        ];
    }
        
    public function save()
    {
        if ($this->validate()) {
            $authManager = Yii::$app->authManager;
            $node = $authManager->createPermission($this->name);
            $node->description = $this->description;
            $authManager->add($node);
            
            return true;
        } else {
            return false;
        }
    }
        
    public function update($name)
    {
        if ($this->validate()) {
            $authManager = Yii::$app->authManager;
            $node = $authManager->getPermission($name);
            if(!$node) return false;
            $authManager->remove($node);
        
            $node = $authManager->createPermission($this->name);
            $node->description = $this->description;
            $authManager->add($node);
            
            return true;
        }
        return false;
    }
        
}
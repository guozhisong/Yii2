<?php
namespace backend\modules\user\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * User form
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $roles;
    private $_isNewRecord = true;
    private $_id;
    private $_created_at;
    private $_updated_at;

    private static $_user = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim', 'on' => ['create', 'update']],
            ['username', 'required', 'on' => ['create', 'update']],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '用户名已存在！', 'on' => ['create']],
            ['username', 'string', 'min' => 2, 'max' => 255, 'on' => ['create', 'update']],

            ['email', 'trim', 'on' => ['create', 'update']],
            ['email', 'required', 'on' => ['create', 'update']],
            ['email', 'email', 'on' => ['create', 'update']],
            ['email', 'string', 'max' => 255, 'on' => ['create', 'update']],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => '邮箱已存在！', 'on' => ['create']],

            ['password', 'required', 'on' => ['create']],
            ['password', 'string', 'min' => 6, 'on' => ['create']],

            ['status', 'integer', 'on' => ['update']],

            ['roles', 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['username', 'email', 'password', 'roles'],
            'update' => ['username', 'email', 'status', 'roles'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'email'    => '邮箱',
            'password' => '密码',
            'status'   => '状态',
            'roles'    => '角色',
        ];
    }

    /**
     * Save user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save();

            if (!empty($this->roles) && is_array($this->roles)) {
                $authManager = Yii::$app->authManager;
                $authManager->revokeAll($user->id);

                foreach ($this->roles as $roleName) {
                    $role = $authManager->getRole($roleName);
                    if (!$role) {
                        continue;
                    }
                    $authManager->assign($role, $user->id);
                }

                $transaction->commit();
                return $user;
            } else {
                $transaction->rollBack();
                return null;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return null;
        }  
    }

    /**
     * Update user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function update()
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = self::_getUser($this->id);
            //$user->username = $this->username;
            //$user->email    = $this->email;
            $user->status   = $this->status;
            $user->save();

            if (!empty($this->roles) && is_array($this->roles)) {
                $authManager = Yii::$app->authManager;
                $authManager->revokeAll($user->id);

                foreach ($this->roles as $roleName) {
                    $role = $authManager->getRole($roleName);
                    if (!$role) {
                        continue;
                    }
                    $authManager->assign($role, $user->id);
                }

                $transaction->commit();
                return $user;
            } else {
                $transaction->rollBack();
                return null;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * Delete user.
     *
     * @return true|false
     */
    public function delete()
    {
        if (!$this->id) {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user = self::_getUser($this->id);
            $user->delete();

            $authManager = Yii::$app->authManager;
            $authManager->revokeAll($user->id);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @return private _isNewRecord
     */
    public function getIsNewRecord()
    {
        return $this->_isNewRecord;
    }
    
    /**
     * @return private _id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return private _created_at
     */
    public function getCreated_at()
    {
        return $this->_created_at;
    }

    /**
     * @return private _updated_at
     */
    public function getUpdated_at()
    {
        return $this->_updated_at;
    }

    /**
     * Find user.
     *
     * @return model
     */
    public static function findOne($id)
    {
        $user = self::_getUser($id);
        
        $model = new self();
        
        $model->username     = $user->username;
        $model->email        = $user->email;
        $model->status       = $user->status;
        $model->roles        = $user->getRoleNames();
        $model->_id          = $user->id;
        $model->_created_at  = $user->created_at;
        $model->_updated_at  = $user->updated_at;
        $model->_isNewRecord = false;

        return ($model->_id > 0) ? $model : null;
    }

    /**
     * Finds user by [[id]]
     *
     * @return User|null
     */
    private static function _getUser($id)
    {
        if (self::$_user === null) {
            self::$_user = User::find()->where(['id' => $id])->with('roles')->one();
        }

        return self::$_user;
    }

}
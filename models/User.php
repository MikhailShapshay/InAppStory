<?php

namespace app\models;

use yii\base\Exception;
use yii\base\Security;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    private $id;
    public string $password; // Виртуальное свойство для хранения пароля
    //private string $password_hash;
    //private string $auth_key;
    //private int $created_at;
    //private int $updated_at;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->password = '';
    }

    public static function tableName(): string
    {
        return 'user';
    }

    // Правила валидации и метки атрибутов (если нужно)
    public function rules(): array
    {
        return [
            [['username', 'email', 'password'], 'required', 'on' => 'create'],
            [['username', 'email'], 'required', 'on' => 'update'],
            [['username', 'email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            ['password', 'string', 'min' => 6],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'auth_key' => 'Api-key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['api_key' => $token]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findByUsername($username): ?User
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @throws Exception
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            // Генерация хеша пароля, если поле пароля не пустое
            if (!empty($this->password)) {
                $this->setPassword($this->password);
            }
            // Генерация auth_key, если он не задан
            if ($this->isNewRecord) {
                $this->generateAuthKey();
                $this->created_at = time();
            }
            $this->updated_at = time();
            return true;
        }
        return false;
    }
}

<?php

namespace app\models;

use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

class User extends ActiveRecord implements IdentityInterface
{
    public string $password; // Виртуальное свойство для хранения пароля

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->password = '';
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * @return string[]
     */
    public function fields(): array
    {
        return [
            'id',
            'username',
            'email',
            'auth_key',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return string[]
     */
    public function extraFields(): array
    {
        return [
            'promo_code',
        ];
    }

    /**
     * Правила валидации и метки атрибутов (если нужно)
     *
     * @return array
     */
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

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'password' => 'Пароль',
            'auth_key' => 'Api-key',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата редактирования',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPromoCode(): ActiveQuery
    {
        return $this->hasOne(PromoCode::class, ['user_id' => 'id']);
    }

    /**
     * @param $token
     * @param $type
     * @return User|IdentityInterface|null
     */
    public static function findIdentityByAccessToken($token, $type = null): User|IdentityInterface|null
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * @param $id
     * @return User|IdentityInterface|null
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne($id);
    }

    /**
     * @param $username
     * @return User|null
     */
    public static function findByUsername($username): ?User
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @return int|mixed|string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * @param $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @throws Exception
     */
    public function setPassword($password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @throws Exception
     */
    public function generateAuthKey(): void
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

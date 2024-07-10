<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
	public static function tableName()
	{
		return 'user';
	}

	public function rules()
	{
		return [
			[['username', 'email', 'password'], 'required'],
			[['username', 'email'], 'string', 'max' => 255],
			[['email'], 'email'],
			[['username'], 'unique'],
			[['email'], 'unique'],
		];
	}

	public function attributeLabels()
	{
		return [
			'username' => 'Username',
			'email' => 'Email',
			'password' => 'Password',
		];
	}

	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findOne(['api_key' => $token]);
	}

	public static function findIdentity($id) {
		// TODO: Implement findIdentity() method.
	}

	public function getId() {
		// TODO: Implement getId() method.
	}

	public function getAuthKey() {
		// TODO: Implement getAuthKey() method.
	}

	public function validateAuthKey($authKey) {
		// TODO: Implement validateAuthKey() method.
	}
}

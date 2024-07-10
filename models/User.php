<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
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
}

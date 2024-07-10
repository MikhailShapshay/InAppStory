<?php

namespace app\models;

use yii\db\ActiveRecord;

class PromoCode extends ActiveRecord
{
	public static function tableName()
	{
		return 'promo_code';
	}

	public function rules()
	{
		return [
			[['code', 'is_used'], 'required'],
			[['code'], 'string', 'max' => 255],
			[['code'], 'unique'],
			[['is_used'], 'boolean'],
		];
	}

	public function attributeLabels()
	{
		return [
			'code' => 'Promo Code',
			'is_used' => 'Is Used',
		];
	}
}

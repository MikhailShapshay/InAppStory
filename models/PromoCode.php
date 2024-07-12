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
            // Добавляем правило на уникальность поля 'code'
            ['code', 'unique'],
            [['code'], 'required'],
            [['code'], 'string', 'max' => 255],
            [['is_used'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Промо-код',
            'is_used' => 'Использован',
        ];
    }

    // Найти доступный для использования промокод
    public static function findAvailablePromoCode()
    {
        return static::find()
            ->where(['is_used' => false])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    // Пометить промокод как использованный
    public function markAsUsed()
    {
        $this->is_used = true;
        return $this->save(false); // Сохраняем без валидации
    }

    public function validateUniqueCode($attribute, $params)
    {
        // Проверяем уникальность кода только если он изменился
        if (!$this->isNewRecord) {
            $existingRecord = static::findOne(['code' => $this->$attribute]);
            if ($existingRecord !== null && $existingRecord->id != $this->id) {
                $this->addError($attribute, 'Промо-код должен быть уникальным.');
            }
        }
    }
}

<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

class PromoCode extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'promo_code';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            // Добавляем правило на уникальность поля 'code'
            ['code', 'unique'],
            [['code'], 'required'],
            [['code'], 'string', 'max' => 255],
            [['is_used'], 'boolean'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'code' => 'Промокод',
            'is_used' => 'Использован',
        ];
    }

    /**
     * Найти доступный для использования промокод
     *
     * @return PromoCode|array|ActiveRecord|null
     */
    public static function findAvailablePromoCode(): PromoCode|array|ActiveRecord|null
    {
        return static::find()
            ->where(['is_used' => false])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    /**
     * Пометить промокод как использованный
     *
     * @param $user_id
     * @return bool
     * @throws Exception
     */
    public function markAsUsed($user_id): bool
    {
        $this->is_used = true;
        $this->user_id = $user_id;
        return $this->save(false); // Сохраняем без валидации
    }

    /**
     * Проверяем уникальность кода только если он изменился
     *
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateUniqueCode($attribute, $params): void
    {
        if (!$this->isNewRecord) {
            $existingRecord = static::findOne(['code' => $this->$attribute]);
            if ($existingRecord !== null && $existingRecord->id != $this->id) {
                $this->addError($attribute, 'Промо-код должен быть уникальным.');
            }
        }
    }
}

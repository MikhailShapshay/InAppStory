<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%promo_code}}`.
 */
class m240712_101529_add_user_id_to_promo_code_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%promo_code}}', 'user_id', $this->integer()->after('id'));

        // Добавляем внешний ключ
        $this->addForeignKey(
            'fk-promo_code-user_id',
            '{{%promo_code}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Удаляем внешний ключ
        $this->dropForeignKey('fk-promo_code-user_id', '{{%promo_code}}');

        $this->dropColumn('{{%promo_code}}', 'user_id');
    }
}

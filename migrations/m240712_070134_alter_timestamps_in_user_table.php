<?php

use yii\db\Migration;

/**
 * Class m240712_070134_alter_timestamps_in_user_table
 */
class m240712_070134_alter_timestamps_in_user_table extends Migration
{
    public function up()
    {
        // Устанавливаем значение по умолчанию для столбца created_at
        $this->alterColumn('{{%user}}', 'created_at', $this->integer()->notNull()->defaultValue(time()));

        // Устанавливаем значение по умолчанию для столбца updated_at
        $this->alterColumn('{{%user}}', 'updated_at', $this->integer()->notNull()->defaultValue(time()));
    }

    public function down()
    {
        // Убираем значение по умолчанию для столбца created_at
        $this->alterColumn('{{%user}}', 'created_at', $this->integer()->notNull());

        // Убираем значение по умолчанию для столбца updated_at
        $this->alterColumn('{{%user}}', 'updated_at', $this->integer()->notNull());
    }
}

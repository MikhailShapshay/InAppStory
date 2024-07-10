<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%promo_code}}`.
 */
class m240710_094225_create_promo_code_table extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->createTable('{{%promo_code}}', [
			'id' => $this->primaryKey(),
			'code' => $this->string()->notNull()->unique(),
			'is_used' => $this->boolean()->notNull()->defaultValue(false),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropTable('{{%promo_code}}');
	}
}

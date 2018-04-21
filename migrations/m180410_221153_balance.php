<?php

use yii\db\Migration;

/**
 * Class m180410_221153_balance
 */
class m180410_221153_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE `user` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(255) NOT NULL,
          `balance` FLOAT NOT NULL DEFAULT 0,
          `created_at` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `username` (`username`),
          KEY `balance` (`balance`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->execute("CREATE TABLE `operations` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `from_user_id` int(11) NOT NULL,
          `to_user_id` int(11) NOT NULL,
          `amount` FLOAT NOT NULL,
          `executed_at` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `amount` (`amount`),
          KEY `executed_at` (`executed_at`),
          CONSTRAINT `from_user_id` FOREIGN KEY (`from_user_id`) REFERENCES `user` (`id`),          
          CONSTRAINT `to_user_id` FOREIGN KEY (`to_user_id`) REFERENCES `user` (`id`)          
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
        $this->dropTable('operations');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180410_221153_balance cannot be reverted.\n";

        return false;
    }
    */
}

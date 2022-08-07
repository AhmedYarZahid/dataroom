<?php

use yii\db\Migration;

/**
 * Class m180618_143443_alter_profession_field_in_User
 */
class m180618_143443_alter_profession_field_in_User extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `User` CHANGE `profession` `profession` VARCHAR(70)  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NULL  DEFAULT '';
            UPDATE User SET profession = NULL;
            ALTER TABLE `User` CHANGE `profession` `profession` TINYINT(4)  NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180618_143443_alter_profession_field_in_User cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_143443_alter_profession_field_in_User cannot be reverted.\n";

        return false;
    }
    */
}

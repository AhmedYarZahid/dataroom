<?php

use yii\db\Migration;

/**
 * Class m180514_135605_add_new_type_to_Document
 */
class m180514_135605_add_new_type_to_Document extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `Document` CHANGE `type` `type` ENUM('regular','contact','resume','cover_letter','proposal','access_request','room','room_image','room_specific')  CHARACTER SET utf8  COLLATE utf8_unicode_ci  NOT NULL  DEFAULT 'regular';
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180514_135605_add_new_type_to_Document cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180514_135605_add_new_type_to_Document cannot be reverted.\n";

        return false;
    }
    */
}

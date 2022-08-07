<?php

use yii\db\Migration;

/**
 * Class m180503_144113_add_params_for_cv_rooms
 */
class m180503_144113_add_params_for_cv_rooms extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            TRUNCATE Parameter;

            INSERT INTO `Parameter` (`name`, `value`, `description`, `type`, `group`, `updatedDate`)
            VALUES ('CV_ROOM_PUBLISH_PERIOD', '72', 'Period after which CV room will be published (in hours)', 'integer', '', CURRENT_TIMESTAMP);

            INSERT INTO `Parameter` (`name`, `value`, `description`, `type`, `group`, `updatedDate`)
            VALUES ('CV_ROOM_EXPIRATION_PERIOD', '6', 'Period after which CV room will be expired (in months)', 'integer', '', CURRENT_TIMESTAMP);
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180503_144113_add_params_for_cv_rooms cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_144113_add_params_for_cv_rooms cannot be reverted.\n";

        return false;
    }
    */
}

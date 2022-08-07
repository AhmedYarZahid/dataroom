<?php

use yii\db\Migration;

/**
 * Class m180507_091144_add_state_to_RoomCV_and_related_notifications
 */
class m180507_091144_add_state_to_RoomCV_and_related_notifications extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE `RoomCV` ADD `state` ENUM('to_fill','to_correct','ready')  NOT NULL  DEFAULT 'to_fill'  AFTER `seniority`;
        ");

        $model = new \backend\modules\notify\models\Notify();
        $model->title = 'Dataroom - CV was uploaded (to admin)';
        $model->subject = 'CV was uploaded';
        $model->body = '<p>Bonjour,</p><p>CV was uploaded to {ROOM_ID} {ROOM_TITLE}.</p>
<p>Cliquer ici pour accéder à la room :&nbsp;{ROOM_LINK}</p>';
        $model->eventID = \backend\modules\notify\models\Notify::EVENT_ROOM_CV_UPLOADED;
        $model->isDefault = 1;
        $model->priority = 255;
        $model->putToQueue = 0;

        $model->save();

        $model = new \backend\modules\notify\models\Notify();
        $model->title = 'Dataroom - CV need to correct (to manager)';
        $model->subject = 'CV need to correct';
        $model->body = '<p>Bonjour,</p><p>Need to correct CV in {ROOM_ID} {ROOM_TITLE}.</p>
<p>Cliquer ici pour accéder à la room :&nbsp;{ROOM_LINK}</p>';
        $model->eventID = \backend\modules\notify\models\Notify::EVENT_ROOM_CV_NEED_TO_CORRECT;
        $model->isDefault = 1;
        $model->priority = 255;
        $model->putToQueue = 0;

        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180507_091144_add_state_to_RoomCV_and_related_notifications cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_091144_add_state_to_RoomCV_and_related_notifications cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m180803_143951_add_notify_new_document_added_to_room
 */
class m180803_143951_add_notify_new_document_added_to_room extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = new \backend\modules\notify\models\Notify();
        $model->title = 'New document was added to the room';
        $model->subject = 'New document was added to the room';
        $model->body = '<p>Bonjour {USER_NAME}, <br>
</p>
<p>A new document(s) was added to the room {ROOM_NAME}:</p>
<p>{DOCUMENTS_LIST}</p>
<p>To see it click <a href="{ROOM_LINK}">here</a></p>';
        $model->eventID = \backend\modules\notify\models\Notify::EVENT_DOCUMENT_ADDED_TO_ROOM;
        $model->isDefault = 1;
        $model->priority = 255;
        $model->putToQueue = 1;

        $model->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180803_143951_add_notify_new_document_added_to_room cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180803_143951_add_notify_new_document_added_to_room cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m180618_123738_add_requestAccessRefused_notify
 */
class m180618_123738_add_requestAccessRefused_notify extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = new \backend\modules\notify\models\Notify();
        $model->title = 'Dataroom - Demande d\'accès refusé (envoyé au repreneur)';
        $model->subject = 'Votre demande d\'accès a été refusé !';
        $model->body = '<p>Bonjour&nbsp;{FIRST_NAME}&nbsp;{LAST_NAME}</p>
<p>Votre demande d\'accès à la room&nbsp;{ROOM_ID}&nbsp;{ROOM_TITLE} a été refusé.</p>
<p><a href="{ROOM_LINK}" style="background-color: rgb(255, 255, 255);">Cliquer ici pour accéder à la room</a></p>';
        $model->eventID = \backend\modules\notify\models\Notify::EVENT_ACCESS_REQUEST_REFUSED;
        $model->isDefault = 1;
        $model->priority = 255;
        $model->putToQueue = 0;

        if (\common\models\Language::findOne('en')) {
            $model->title_en = $model->title;
            $model->subject_en = $model->subject;
            $model->body_en = $model->body;
        }

        $model->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180618_123738_add_requestAccessRefused_notify cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_123738_add_requestAccessRefused_notify cannot be reverted.\n";

        return false;
    }
    */
}

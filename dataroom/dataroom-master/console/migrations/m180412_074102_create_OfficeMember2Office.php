<?php

use yii\db\Migration;
use backend\modules\office\models\OfficeMember;

/**
 * Class m180412_074102_create_OfficeMember2Office
 */
class m180412_074102_create_OfficeMember2Office extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE `OfficeMember2Office` (
              `officeMemberID` int(11) NOT NULL,
              `officeID` int(11) NOT NULL
            );

            ALTER TABLE `OfficeMember2Office`
            ADD PRIMARY KEY `officeMemberID_officeID` (`officeMemberID`, `officeID`);
            ALTER TABLE `OfficeMember2Office`
            ADD FOREIGN KEY (`officeMemberID`) REFERENCES `OfficeMember` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
            ALTER TABLE `OfficeMember2Office`
            ADD FOREIGN KEY (`officeID`) REFERENCES `Office` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ");

        $members = OfficeMember::find()->where(['not', ['officeID' => null]])->all();

        foreach ($members as $model) {
            $this->insert('OfficeMember2Office', [
                'officeMemberID' => $model->id,
                'officeID' => $model->officeID,
            ]);
        }

        $this->dropForeignKey('fk_OfficeMember_Office', 'OfficeMember');
        $this->dropColumn('OfficeMember', 'officeID');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180412_074102_create_OfficeMember2Office cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180412_074102_create_OfficeMember2Office cannot be reverted.\n";

        return false;
    }
    */
}

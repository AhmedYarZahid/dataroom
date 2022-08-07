<?php

use yii\db\Migration;

class m180312_113522_update_RoomCompany_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE Room
            CHANGE publicationDate publicationDate datetime NOT NULL,
            CHANGE archivationDate archivationDate datetime NOT NULL,
            CHANGE expirationDate expirationDate datetime NOT NULL;


            ALTER TABLE RoomCompany

            ADD ca int(11) NULL AFTER roomID,

            # Administratif et Financier
            CHANGE legalStatus legalStatus varchar(255) NULL,
            ADD status int(11) NULL,
            ADD kbis int(11) NULL,
            ADD balanceSheet int(11) NULL,
            ADD incomeStatement int(11) NULL,
            ADD managementBalance int(11) NULL,
            ADD taxPackage int(11) NULL,
            ADD history text NULL,
            ADD concurrence text NULL,
            ADD backlog int(11) NULL,
            ADD principalClients int(11) NULL,
            ADD annualTurnover varchar(50) NULL,

            # Immobilisations
            ADD vehicles int(11) NULL,
            ADD premises int(11) NULL,
            ADD baux int(11) NULL,
            ADD inventory int(11) NULL,
            ADD assets int(11) NULL,
            ADD patents int(11) NULL,

            # Social
            ADD contributors varchar(255) NULL,
            ADD employmentContract int(11) NULL,
            ADD employeesList int(11) NULL,
            ADD procedureRules int(11) NULL,
            ADD rtt int(11) NULL,
            ADD worksCouncilReport int(11) NULL,

            # Procédure
            ADD procedureNature varchar(255) NULL,
            ADD designationDate date NULL,
            ADD procedureContact text NULL,
            ADD studyContact varchar(255) NULL,
            ADD companyContact varchar(255) NULL,
            ADD hearingDate date NULL,
            ADD refNumber1 varchar(255) NULL,
            ADD refNumber2 varchar(255) NULL;
        ");
    }

    public function safeDown()
    {
        echo "m180312_113522_update_RoomCompany_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180312_113522_update_RoomCompany_table cannot be reverted.\n";

        return false;
    }
    */
}

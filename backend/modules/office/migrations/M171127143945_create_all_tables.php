<?php

namespace backend\modules\office\migrations;

use yii\db\Migration;

class M171127143945_create_all_tables extends Migration
{
    public function safeUp()
    {
        $this->execute("CREATE TABLE OfficeCity (
              id int(11) NOT NULL AUTO_INCREMENT,
              name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              mapData text DEFAULT NULL,
              isActive tinyint(1) NOT NULL DEFAULT '1',
              createdDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $this->execute("CREATE TABLE Office (
              id int(11) NOT NULL AUTO_INCREMENT,
              name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              body text DEFAULT NULL,
              isActive tinyint(1) NOT NULL DEFAULT '1',
              latitude DECIMAL(10, 8) NULL,
              longitude DECIMAL(11, 8) NULL,
              cityID int(11),
              userID int(11),
              createdDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY fk_Office_OfficeCity_idx (cityID),
              CONSTRAINT fk_Office_OfficeCity FOREIGN KEY (cityID) REFERENCES OfficeCity (id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $this->execute("CREATE TABLE OfficeMember (
              id int(11) NOT NULL AUTO_INCREMENT,
              firstName varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              lastName varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              body text DEFAULT NULL,
              urlEntity enum('trendy-page') NULL,
              url varchar(255) COLLATE utf8_unicode_ci NULL,
              image varchar(255) COLLATE utf8_unicode_ci NULL,
              isActive tinyint(1) NOT NULL DEFAULT '1',
              officeID int(11),
              userID int(11),
              createdDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY fk_OfficeMember_Office_idx (officeID),
              CONSTRAINT fk_OfficeMember_Office FOREIGN KEY (officeID) REFERENCES Office (id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function safeDown()
    {
        echo "M171127143945_create_all_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M171127143945_create_all_tables cannot be reverted.\n";

        return false;
    }
    */
}

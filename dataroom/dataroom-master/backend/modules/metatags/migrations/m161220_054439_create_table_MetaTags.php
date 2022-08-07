<?php

use yii\db\Migration;

class m161220_054439_create_table_MetaTags extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `MetaTags` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nodeType` enum('news','staticPage','trendyPage') COLLATE utf8_unicode_ci NOT NULL,
              `nodeID` int(11) NOT NULL,
              `data` text COLLATE utf8_unicode_ci,
              `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `nodeType_nodeID` (`nodeType`,`nodeID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down()
    {
        $this->dropTable('MetaTags');
    }
}

<?php

use yii\db\Migration;

class m161220_054439_create_table_MetaTagsAttrs extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `MetaTagsAttrs` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `attrName` varchar(255) NOT NULL,
              `rank` int(11) DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }

    public function down()
    {
        $this->dropTable('MetaTagsAttrs');
    }
}

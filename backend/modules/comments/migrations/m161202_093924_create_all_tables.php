<?php

use yii\db\Migration;

class m161202_093924_create_all_tables extends Migration
{
    public function up()
    {
        $this->execute("CREATE TABLE `CommentBundle` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `nodeType` enum('news','staticPage','trendyPage') COLLATE utf8_unicode_ci NOT NULL,
              `nodeID` int(11) NOT NULL,
              `nodeTitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `isActive` tinyint(1) NOT NULL DEFAULT '0',
              `isNewCommentsAllowed` tinyint(1) NOT NULL DEFAULT '0',
              `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `nodeType_nodeID` (`nodeType`,`nodeID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");

        $this->execute("CREATE TABLE `Comment` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `commentBundleID` int(11) NOT NULL,
              `authorName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `authorEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `text` text COLLATE utf8_unicode_ci,
              `isApproved` tinyint(1) NOT NULL DEFAULT '0',
              `approvedDate` datetime DEFAULT NULL,
              `createdDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `fk_Comment_CommentBundle1_idx` (`commentBundleID`),
              CONSTRAINT `fk_Comment_CommentBundle1` FOREIGN KEY (`commentBundleID`) REFERENCES `CommentBundle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down()
    {
        $this->dropTable('Comment');
        $this->dropTable('CommentBundle');
    }
}

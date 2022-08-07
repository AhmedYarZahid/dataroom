<?php

use yii\db\Migration;

/**
 * Class m180502_145850_add_RoomCV_and_related_tables
 */
class m180502_145850_add_RoomCV_and_related_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            /*!40000 ALTER TABLE `Region` DISABLE KEYS */;
            INSERT INTO `Region` (`id`, `name`, `code`)
                VALUES
                    (14, 'Guadeloupe', 1),
                    (15, 'Martinique', 2),
                    (16, 'French Guiana', 3),
                    (17, 'Réunion', 4),
                    (18, 'Mayotte', 6);
            /*!40000 ALTER TABLE `Region` ENABLE KEYS */;


            CREATE TABLE `CVActivityDomain` (
              `id` smallint(6) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

            LOCK TABLES `CVActivityDomain` WRITE;
            /*!40000 ALTER TABLE `CVActivityDomain` DISABLE KEYS */;

            INSERT INTO `CVActivityDomain` (`id`, `name`)
            VALUES
                (1,'Accueil - Secrétariat'),
                (2,'Achats - Commerce - Distribution'),
                (3,'Agro-Alimentaire'),
                (4,'Automobile'),
                (5,'Banque - Assurances - Immobilier'),
                (6,'Bijoux - Horlogerie - Lunetterie'),
                (7,'Bureau d\'études- Méthodes - Qualité'),
                (8,'Chimie - Pharmacie - Cosmétologie'),
                (9,'Communication'),
                (10,'Comptabilité - Finance'),
                (11,'Construction - Travaux publics'),
                (12,'Electricité - Electronique'),
                (13,'Hôtellerie- Restauration - Tourisme'),
                (14,'IT - Commercial - Conseil - AMOA'),
                (15,'IT - Etude et Développement'),
                (16,'IT - Exploitation - Système - SGBD'),
                (17,'IT - Réseau - Telecom'),
                (18,'IT - Support - Maintenance - Help Desk'),
                (19,'Imprimerie'),
                (20,'Industrie aéronautique'),
                (21,'Logistique'),
                (22,'Maintenance - Entretien'),
                (23,'Multimédia'),
                (24,'Métallurgie- Fonderie'),
                (25,'Naval'),
                (26,'Nucléaire'),
                (27,'Papier - Carton'),
                (28,'Plasturgie'),
                (29,'Production Graphique'),
                (30,'Production industrielle - Mécanique'),
                (31,'Ressources humaines - Juridique'),
                (32,'Santé'),
                (33,'Spectacle'),
                (34,'Surveillance - Sécurité'),
                (35,'Textile - Couture - Cuir'),
                (36,'Transport'),
                (37,'Transport aérien'),
                (38,'Téléservices - Marketing - Vente'),
                (39,'Verre - Porcelaine'),
                (40,'Vin - Agriculture - Paysagisme'),
                (41,'Autre');

            /*!40000 ALTER TABLE `CVActivityDomain` ENABLE KEYS */;
            UNLOCK TABLES;


            # Дамп таблицы CVFunction
            # ------------------------------------------------------------

            CREATE TABLE `CVFunction` (
              `id` smallint(6) NOT NULL AUTO_INCREMENT,
              `parentID` smallint(6) DEFAULT NULL,
              `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_CVFunction_CVFunction1_idx` (`parentID`),
              CONSTRAINT `fk_CVFunction_CVFunction1` FOREIGN KEY (`parentID`) REFERENCES `CVFunction` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

            LOCK TABLES `CVFunction` WRITE;
            /*!40000 ALTER TABLE `CVFunction` DISABLE KEYS */;

            INSERT INTO `CVFunction` (`id`, `parentID`, `name`)
            VALUES
                (1,NULL,'Administration / Services généraux'),
                (2,NULL,'Audit'),
                (3,NULL,'Commercial / vente'),
                (4,3,'Direction commerciale – développement – stratégie'),
                (5,3,'Direction des ventes – chef des ventes'),
                (6,3,'Administration des ventes'),
                (7,3,'Assistanat commercial'),
                (8,3,'Chef d’agence – responsable magasin'),
                (9,3,'Chef de rayon – chef de secteur'),
                (10,3,'Commercial'),
                (11,3,'Grands-comptes'),
                (12,3,'Ingénieur commercial'),
                (13,3,'Ingénieur d’affaires'),
                (14,3,'Technico-commercial'),
                (15,3,'Télévente'),
                (16,3,'Vente'),
                (17,NULL,'Communication'),
                (18,NULL,'Créateur'),
                (19,NULL,'Conseil'),
                (20,NULL,'Directeur général'),
                (21,NULL,'Recherche / études'),
                (22,NULL,'Export'),
                (23,NULL,'Gestion / comptabilité / finance'),
                (24,23,'Directeur comptable – expert comptable'),
                (25,23,'Analyste financier'),
                (26,23,'Cadre bancaire'),
                (27,23,'Cambiste – trader – Front'),
                (28,23,'Comptable'),
                (29,23,'Consolidation'),
                (30,23,'Contrôleur de gestion – contrôleur interne ou général'),
                (31,23,'Crédit manager'),
                (32,23,'Directeur financier'),
                (33,23,'Gestion patrimoniale / fiscaliste'),
                (34,23,'Middle – back office'),
                (35,23,'Organisation comptable'),
                (36,23,'Trésorier – analyste crédit – risk manager'),
                (37,NULL,'Internet / e-commerce'),
                (38,NULL,'Juridique / fiscal'),
                (39,NULL,'Logistique / achat / stock / transport'),
                (40,NULL,'Marketing'),
                (41,NULL,'Production / maintenance / qualité / sécurité / environnement'),
                (42,41,'Direction industrielle'),
                (43,41,'Direction production'),
                (44,41,'Direction technique'),
                (45,41,'Direction d’usine'),
                (46,41,'Chef de fabrication / production'),
                (47,41,'Contrôleur qualité'),
                (48,41,'Assurance qualité'),
                (49,41,'Environnement – sécurité'),
                (50,41,'Gestion de production'),
                (51,41,'Maintenance – entretien'),
                (52,41,'Méthodes – process – industrialisation'),
                (53,41,'Production'),
                (54,41,'SAV'),
                (55,41,'Travaux neufs – chantiers'),
                (56,NULL,'Ressources Humaines / Personnel / formation'),
                (57,NULL,'Santé (industrie)'),
                (58,NULL,'Santé (médical)'),
                (59,NULL,'Social'),
                (60,NULL,'Systèmes d’information / télécom'),
                (61,NULL,'Autre');

            /*!40000 ALTER TABLE `CVFunction` ENABLE KEYS */;
            UNLOCK TABLES;


            # Дамп таблицы Department
            # ------------------------------------------------------------

            CREATE TABLE `Department` (
              `id` smallint(6) NOT NULL,
              `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
              `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `regionID` smallint(6) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_Department_Region1` (`regionID`),
              CONSTRAINT `fk_Department_Region1` FOREIGN KEY (`regionID`) REFERENCES `Region` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

            LOCK TABLES `Department` WRITE;
            /*!40000 ALTER TABLE `Department` DISABLE KEYS */;

            INSERT INTO `Department` (`id`, `name`, `code`, `regionID`)
            VALUES
                (1,'Ain','01',10),
                (2,'Aisne','02',1),
                (3,'Allier','03',10),
                (4,'Alpes-de-Haute-Provence','04',12),
                (5,'Hautes-Alpes','05',12),
                (6,'Alpes-Maritimes','06',12),
                (7,'Ardèche','07',10),
                (8,'Ardennes','08',5),
                (9,'Ariège','09',11),
                (10,'Aube','10',5),
                (11,'Aude','11',11),
                (12,'Aveyron','12',11),
                (13,'Bouches-du-Rhône','13',12),
                (14,'Calvados','14',3),
                (15,'Cantal','15',10),
                (16,'Charente','16',9),
                (17,'Charente-Maritime','17',9),
                (18,'Cher','18',7),
                (19,'Corrèze','19',9),
                (20,'Corse-du-Sud','2A',13),
                (21,'Haute-Corse','2B',13),
                (22,'Côte-d\'Or','21',8),
                (23,'Côtes-d\'Armor','22',2),
                (24,'Creuse','23',9),
                (25,'Dordogne','24',9),
                (26,'Doubs','25',8),
                (27,'Drôme','26',10),
                (28,'Eure','27',3),
                (29,'Eure-et-Loir','28',7),
                (30,'Finistère','29',2),
                (31,'Gard','30',11),
                (32,'Haute-Garonne','31',11),
                (33,'Gers','32',11),
                (34,'Gironde','33',9),
                (35,'Hérault','34',11),
                (36,'Ille-et-Vilaine','35',2),
                (37,'Indre','36',7),
                (38,'Indre-et-Loire','37',7),
                (39,'Isère','38',11),
                (40,'Jura','39',8),
                (41,'Landes','40',9),
                (42,'Loir-et-Cher','41',7),
                (43,'Loire','42',10),
                (44,'Haute-Loire','43',10),
                (45,'Loire-Atlantique','44',6),
                (46,'Loiret','45',7),
                (47,'Lot','46',11),
                (48,'Lot-et-Garonne','47',9),
                (49,'Lozère','48',11),
                (50,'Maine-et-Loire','49',6),
                (51,'Manche','50',3),
                (52,'Marne','51',5),
                (53,'Haute-Marne','52',5),
                (54,'Mayenne','53',6),
                (55,'Meurthe-et-Moselle','54',5),
                (56,'Meuse','55',5),
                (57,'Morbihan','56',2),
                (58,'Moselle','57',5),
                (59,'Nièvre','58',8),
                (60,'Nord','59',1),
                (61,'Oise','60',1),
                (62,'Orne','61',3),
                (63,'Pas-de-Calais','62',1),
                (64,'Puy-de-Dôme','63',10),
                (65,'Pyrénées-Atlantiques','64',9),
                (66,'Hautes-Pyrénées','65',11),
                (67,'Pyrénées-Orientales','66',11),
                (68,'Bas-Rhin','67',5),
                (69,'Haut-Rhin','68',5),
                (70,'Rhône','69',10),
                (71,'Haute-Saône','70',8),
                (72,'Saône-et-Loire','71',8),
                (73,'Sarthe','72',6),
                (74,'Savoie','73',10),
                (75,'Haute-Savoie','74',10),
                (76,'Paris','75',4),
                (77,'Seine-Maritime','76',3),
                (78,'Seine-et-Marne','77',4),
                (79,'Yvelines','78',4),
                (80,'Deux-Sèvres','79',9),
                (81,'Somme','80',1),
                (82,'Tarn','81',11),
                (83,'Tarn-et-Garonne','82',11),
                (84,'Var','83',12),
                (85,'Vaucluse','84',12),
                (86,'Vendée','85',6),
                (87,'Vienne','86',9),
                (88,'Haute-Vienne','87',9),
                (89,'Vosges','88',5),
                (90,'Yonne','89',8),
                (91,'Territoire de Belfort','90',8),
                (92,'Essonne','91',4),
                (93,'Hauts-de-Seine','92',4),
                (94,'Seine-Saint-Denis','93',4),
                (95,'Val-de-Marne','94',4),
                (96,'Val-d\'Oise','95',4),
                (97,'Guadeloupe','971',14),
                (98,'Martinique','972',15),
                (99,'Guyane','973',16),
                (100,'La Réunion','974',17),
                (101,'Mayotte','976',18);

            /*!40000 ALTER TABLE `Department` ENABLE KEYS */;
            UNLOCK TABLES;


            # Дамп таблицы RoomAccessRequestCV
            # ------------------------------------------------------------

            CREATE TABLE `RoomAccessRequestCV` (
              `accessRequestID` int(11) NOT NULL,
              `isAgreementSigned` tinyint(1) NOT NULL DEFAULT '1',
              PRIMARY KEY (`accessRequestID`),
              CONSTRAINT `fk_RoomAccessRequestCV_RoomAccessRequest1` FOREIGN KEY (`accessRequestID`) REFERENCES `RoomAccessRequest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



            # Дамп таблицы RoomCV
            # ------------------------------------------------------------

            CREATE TABLE `RoomCV` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `roomID` int(11) NOT NULL,
              `companyName` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
              `activityDomainID` smallint(6) NOT NULL,
              `candidateProfile` text COLLATE utf8_unicode_ci NOT NULL,
              `functionID` smallint(6) NOT NULL,
              `subFunctionID` smallint(6) DEFAULT NULL,
              `firstName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
              `lastName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
              `address` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
              `cvID` int(11) NOT NULL,
              `departmentID` smallint(6) NOT NULL,
              `regionID` smallint(6) NOT NULL,
              `seniority` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_RoomCV_Room1_idx` (`roomID`),
              KEY `fk_RoomCV_Department1_idx` (`departmentID`),
              KEY `fk_RoomCV_Document1_idx` (`cvID`),
              KEY `fk_RoomCV_Region1_idx` (`regionID`),
              KEY `fk_RoomCV_CVActivityDomain1_idx` (`activityDomainID`),
              KEY `fk_RoomCV_CVFunction1_idx` (`functionID`),
              KEY `fk_RoomCV_CVFunction2_idx` (`subFunctionID`),
              CONSTRAINT `fk_RoomCV_Room1` FOREIGN KEY (`roomID`) REFERENCES `Room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Department1` FOREIGN KEY (`departmentID`) REFERENCES `Department` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Document1` FOREIGN KEY (`cvID`) REFERENCES `Document` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_Region1` FOREIGN KEY (`regionID`) REFERENCES `Region` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVActivityDomain1` FOREIGN KEY (`activityDomainID`) REFERENCES `CVActivityDomain` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVFunction1` FOREIGN KEY (`functionID`) REFERENCES `CVFunction` (`id`) ON UPDATE CASCADE,
              CONSTRAINT `fk_RoomCV_CVFunction2` FOREIGN KEY (`subFunctionID`) REFERENCES `CVFunction` (`id`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180502_145850_add_RoomCV_and_related_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180502_145850_add_RoomCV_and_related_tables cannot be reverted.\n";

        return false;
    }
    */
}

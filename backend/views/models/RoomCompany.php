<?php

namespace backend\modules\dataroom\models;

use Yii;
use yii2mod\behaviors\CarbonBehavior;
use backend\modules\dataroom\Module;
use common\components\DocumentBehavior;
use backend\modules\document\models\Document;

/**
 * This is the model class for table "RoomCompany".
 *
 * @property integer $id
 * @property integer $roomID
 * @property integer $ca
 * @property string $activity
 * @property string $activitysector
 * @property string $region
 * @property string $website
 * @property string $address
 * @property string $zip
 * @property string $city
 * @property string $desc
 * @property string $desc2
 * @property string $siren
 * @property string $codeNaf
 * @property string $legalStatus
 * @property integer $status
 * @property integer $kbis
 * @property integer $balanceSheet
 * @property integer $incomeStatement
 * @property integer $managementBalance
 * @property integer $taxPackage
 * @property string $history
 * @property string $concurrence
 * @property integer $backlog
 * @property integer $principalClients
 * @property string $annualTurnover
 * @property integer $vehicles
 * @property integer $premises
 * @property integer $baux
 * @property integer $inventory
 * @property integer $assets
 * @property integer $patents
 * @property integer $contributors
 * @property integer $employmentContract
 * @property integer $employeesList
 * @property integer $procedureRules
 * @property integer $rtt
 * @property integer $worksCouncilReport
 * @property string $procedureNature
 * @property string $designationDate
 * @property string $procedureContact
 * @property string $companyContact
 * @property string $hearingDate
 * @property string $refNumber0
 * @property string $refNumber1
 * @property string $refNumber2
 *
 * @property Room $room
 */
class RoomCompany extends AbstractDetailedRoom
{
    protected $fileFields = [
        'status', 'backlog', 'principalClients', 'vehicles', 'premises', 'baux', 'inventory', 'assets', 'patents', 'employmentContract', 'employeesList', 'procedureRules', 'rtt', 'worksCouncilReport',
    ];

    public function getDataroomSection()
    {
        return Module::SECTION_COMPANIES;
    }

    public function getDataroomSectionLabel()
    {
        return 'AJArepreneurs';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'RoomCompany';
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        $fileAttributes = array_fill_keys($this->fileFields, Document::TYPE_ROOM_SPECIFIC);

        return [
            [
                'class' => DocumentBehavior::class,
                'attributes' => $fileAttributes,
            ],
            'carbon' => [
                'class' => CarbonBehavior::className(),
                'attributes' => [
                    'designationDate',
                    'hearingDate',
                    'refNumber0',
                    'refNumber1',
                    'refNumber2',
                ]
            ],
       ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = $scenarios['default'];

        $scenarios[self::SCENARIO_UPDATE] = $scenarios['default'];

        if (Yii::$app->user->id && Yii::$app->user->identity->isAdmin()) {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = $scenarios['default'];
        } else {
            $scenarios[self::SCENARIO_UPDATE_FRONT] = ['history', 'backlog', 'principalClients', 'annualTurnover', 'vehicles', 'premises', 'baux', 'inventory', 'assets', 'patents', 'contributors', 'employmentContract', 'employeesList', 'procedureRules', 'rtt', 'worksCouncilReport', 'companyContact'];
        }

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['procedureNature', 'designationDate', 'procedureContact', 'activity', 'activitysector', 'annualTurnover', 'city', 'refNumber0', 'contributors','region'], 'required'],
            [['roomID'], 'integer'],
            [['concurrence'], 'string'],
            [['designationDate', 'hearingDate', 'refNumber0', 'refNumber1', 'refNumber2'], 'safe'],
            [['address', 'city', 'legalStatus', 'companyContact', 'contributors'], 'string', 'max' => 255],
            [['activity'], 'string', 'max' => 100],
            [['zip'], 'string', 'max' => 5],
            [['annualTurnover'], 'string', 'max' => 50],
            [['roomID'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['roomID' => 'id']],
            [['history'], 'safe'],
            // [['companyContact','procedureContact'], 'email'],
            [['refNumber0'], 'validateRefDate'],

            [$this->fileFields, 'file', 'extensions' => ['pdf','doc','docx','txt','jpg','jpeg','gif','png']],
        ];
    }

    /**
     * Validates ref dates
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param $attr
     */
    public function validateRefDate($attr)
    {
        if ($this->refNumber1 && $this->refNumber2) {
            if ($this->refNumber1 >= $this->refNumber2) {
                $this->addError('refNumber1', Yii::t('admin', "Report n°1 must be less than Report n°2."));
            }

            if ($this->refNumber0 >= $this->refNumber1) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n°1."));
            }
        } elseif ($this->refNumber1) {
            if ($this->refNumber0 >= $this->refNumber1) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n°1."));
            }
        } elseif ($this->refNumber2) {
            if ($this->refNumber0 >= $this->refNumber2) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n°2."));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Code Room',
            'roomID' => 'Room ID',
            'ca' => 'Engagement de confidentialité',
            'activity' => 'Activité',
            'activitysector' => 'Secteur d\'Activité',
            'region' => 'Département',
            // 'website' => 'Site Internet',
            'address' => 'Adresse',
            'zip' => 'Code postal',
            'city' => 'Ville',
            // 'desc' => "Description de l'entreprise",
            // 'desc2' => "Description du siège de l'entreprise",
            'siren' => 'Numéro SIREN',
            'codeNaf' => 'Code NAF (ex APE)',
            'legalStatus' => 'Statut juridique',
            // 'status' => 'Statuts',
            // 'kbis' => 'KBis',
            // 'balanceSheet' => 'Dernier bilan',
            'incomeStatement' => 'Dernier compte de résultat',
            'managementBalance' => 'Dernier Solde Intermédiaire de Gestion',
            'taxPackage' => 'Dernière liasse fiscale',
            'history' => 'History',
            // 'concurrence' => 'Concurrence',
            'backlog' => 'Carnet de commandes',
            'principalClients' => 'Principaux clients',
            'annualTurnover' => 'CA annuel',
            'vehicles' => 'Description des véhicules',
            'premises' => 'Description des locaux',
            'baux' => 'Baux',
            'inventory' => 'Inventaire du commissaire priseur',
            'assets' => 'Actifs remarquables',
            'patents' => 'Concessions, droits, brevets',
            'contributors' => 'Effectif',
            'employmentContract' => 'Contrat de travail type',
            'employeesList' => 'Liste des salariés',
            'procedureRules' => 'Règlement intérieur',
            'rtt' => 'Accord sur la RTT',
            'worksCouncilReport' => "Dernier rapport du comité d'entreprise",
            'procedureNature' => 'Nature de la procédure',
            'designationDate' => 'Date d\'ouverture de la procédure',
            'procedureContact' => 'Contact AJA pour la procédure',
            'companyContact' => 'Email du contact au sein de la société',
            'hearingDate' => "Date de prochaine audience d'analyse des offres",
            'refNumber0' => 'Date limite de dépôt des offres',
            'refNumber1' => 'Report n°1 de la date limite de dépôt des offres au',
            'refNumber2' => 'Report n°2 de la date limite de dépôt des offres au',
            'place' => 'Lieu',
        ];
    }

    public function selectProcedureNature()
    {
        return [
            'Conciliation' => 'Conciliation',
            'Mandat ad hoc' => 'Mandat ad hoc',
            'Sauvegarde' => 'Sauvegarde',
            'Redressement judiciaire' => 'Redressement judiciaire',
            'Liquidation judiciaire' => 'Liquidation judiciaire',
            'Administration provisoire' => 'Administration provisoire',
            'Autre' => 'Autre',

        ];
    }

    public function selectSectorActivity()
    {
        return [
            'Aéronautique' => 'Aéronautique',
            'Agroalimentaire / Agriculture' => 'Agroalimentaire / Agriculture',
            'Automobile' => 'Automobile',
            'Banque / Assurance / Finance' => 'Banque / Assurance / Finance',
            'Bâtiment / BTP / Construction' => 'Bâtiment / BTP / Construction',
            'Bois / Papier / Carton / Imprimerie' => 'Bois / Papier / Carton / Imprimerie',
            'Chimie / Parachimie' => 'Chimie / Parachimie',
            'Commerce / Négoce / Distribution' => 'Commerce / Négoce / Distribution',
            'Edition / Communication / Multimédia' => 'Edition / Communication / Multimédia',
            'Electronique / Electricité' => 'Electronique / Electricité',
            'Hôtellerie' => 'Hôtellerie',
            'Informatique / Télécoms' => 'Informatique / Télécoms',
            'Mécanique' => 'Mécanique',
            'Médical / Pharmaceutique' => 'Médical / Pharmaceutique',
            'Métallurgie / Sidérurgie' => 'Métallurgie / Sidérurgie',
            'Restauration' => 'Restauration',
            'Services / Conseils' => 'Services / Conseils',
            'Textile / habillement' => 'Textile / habillement',
            'Transport / Logistique' => 'Transport / Logistique',
            'Autres' => 'Autres',
        ];
    }

    public function codeNafList()
    {
        return [
            "01.11Z" => "01.11Z - Culture de céréales (à l'exception du riz), de légumineuses et de graines oléagineuses",
            "01.12Z" => "01.12Z - Culture du riz",
            "01.13Z" => "01.13Z - Culture de légumes, de melons, de racines et de tubercules",
            "01.14Z" => "01.14Z - Culture de la canne à sucre",
            "01.15Z" => "01.15Z - Culture du tabac",
            "01.16Z" => "01.16Z - Culture de plantes à fibres",
            "01.19Z" => "01.19Z - Autres cultures non permanentes",
            "01.21Z" => "01.21Z - Culture de la vigne",
            "01.22Z" => "01.22Z - Culture de fruits tropicaux et subtropicaux",
            "01.23Z" => "01.23Z - Culture d'agrumes",
            "01.24Z" => "01.24Z - Culture de fruits à pépins et à noyau",
            "01.25Z" => "01.25Z - Culture d'autres fruits d'arbres ou d'arbustes et de fruits à coque",
            "01.26Z" => "01.26Z - Culture de fruits oléagineux",
            "01.27Z" => "01.27Z - Culture de plantes à boissons",
            "01.28Z" => "01.28Z - Culture de plantes à épices, aromatiques, médicinales et pharmaceutiques",
            "01.29Z" => "01.29Z - Autres cultures permanentes",
            "01.30Z" => "01.30Z - Reproduction de plantes",
            "01.41Z" => "01.41Z - Élevage de vaches laitières",
            "01.42Z" => "01.42Z - Élevage d'autres bovins et de buffles",
            "01.43Z" => "01.43Z - Élevage de chevaux et d'autres équidés",
            "01.44Z" => "01.44Z - Élevage de chameaux et d'autres camélidés",
            "01.45Z" => "01.45Z - Élevage d'ovins et de caprins",
            "01.46Z" => "01.46Z - Élevage de porcins",
            "01.47Z" => "01.47Z - Élevage de volailles",
            "01.49Z" => "01.49Z - Élevage d'autres animaux",
            "01.50Z" => "01.50Z - Culture et élevage associés",
            "01.61Z" => "01.61Z - Activités de soutien aux cultures",
            "01.62Z" => "01.62Z - Activités de soutien à la production animale",
            "01.63Z" => "01.63Z - Traitement primaire des récoltes",
            "01.64Z" => "01.64Z - Traitement des semences",
            "01.70Z" => "01.70Z - Chasse, piégeage et services annexes",
            "02.10Z" => "02.10Z - Sylviculture et autres activités forestières",
            "02.20Z" => "02.20Z - Exploitation forestière",
            "02.30Z" => "02.30Z - Récolte de produits forestiers non ligneux poussant à l'état sauvage",
            "02.40Z" => "02.40Z - Services de soutien à l'exploitation forestière",
            "03.11Z" => "03.11Z - Pêche en mer",
            "03.12Z" => "03.12Z - Pêche en eau douce",
            "03.21Z" => "03.21Z - Aquaculture en mer",
            "03.22Z" => "03.22Z - Aquaculture en eau douce",
            "05.10Z" => "05.10Z - Extraction de houille",
            "05.20Z" => "05.20Z - Extraction de lignite",
            "06.10Z" => "06.10Z - Extraction de pétrole brut",
            "06.20Z" => "06.20Z - Extraction de gaz naturel",
            "07.10Z" => "07.10Z - Extraction de minerais de fer",
            "07.21Z" => "07.21Z - Extraction de minerais d'uranium et de thorium",
            "07.29Z" => "07.29Z - Extraction d'autres minerais de métaux non ferreux",
            "08.11Z" => "08.11Z - Extraction de pierres ornementales et de construction, de calcaire industriel, de gypse, de craie et d'ardoise",
            "08.12Z" => "08.12Z - Exploitation de gravières et sablières, extraction d'argiles et de kaolin",
            "08.91Z" => "08.91Z - Extraction des minéraux chimiques et d'engrais minéraux",
            "08.92Z" => "08.92Z - Extraction de tourbe",
            "08.93Z" => "08.93Z - Production de sel",
            "08.99Z" => "08.99Z - Autres activités extractives n.c.a.",
            "09.10Z" => "09.10Z - Activités de soutien à l'extraction d'hydrocarbures",
            "09.90Z" => "09.90Z - Activités de soutien aux autres industries extractives",
            "10.11Z" => "10.11Z - Transformation et conservation de la viande de boucherie",
            "10.12Z" => "10.12Z - Transformation et conservation de la viande de volaille",
            "10.13A" => "10.13A - Préparation industrielle de produits à base de viande",
            "10.13B" => "10.13B - Charcuterie",
            "10.20Z" => "10.20Z - Transformation et conservation de poisson, de crustacés et de mollusques",
            "10.31Z" => "10.31Z - Transformation et conservation de pommes de terre",
            "10.32Z" => "10.32Z - Préparation de jus de fruits et légumes",
            "10.39A" => "10.39A - Autre transformation et conservation de légumes",
            "10.39B" => "10.39B - Transformation et conservation de fruits",
            "10.41A" => "10.41A - Fabrication d'huiles et graisses brutes",
            "10.41B" => "10.41B - Fabrication d'huiles et graisses raffinées",
            "10.42Z" => "10.42Z - Fabrication de margarine et graisses comestibles similaires",
            "10.51A" => "10.51A - Fabrication de lait liquide et de produits frais",
            "10.51B" => "10.51B - Fabrication de beurre",
            "10.51C" => "10.51C - Fabrication de fromage",
            "10.51D" => "10.51D - Fabrication d'autres produits laitiers",
            "10.52Z" => "10.52Z - Fabrication de glaces et sorbets",
            "10.61A" => "10.61A - Meunerie",
            "10.61B" => "10.61B - Autres activités du travail des grains",
            "10.62Z" => "10.62Z - Fabrication de produits amylacés",
            "10.71A" => "10.71A - Fabrication industrielle de pain et de pâtisserie fraîche",
            "10.71B" => "10.71B - Cuisson de produits de boulangerie",
            "10.71C" => "10.71C - Boulangerie et boulangerie-pâtisserie",
            "10.71D" => "10.71D - Pâtisserie",
            "10.72Z" => "10.72Z - Fabrication de biscuits, biscottes et pâtisseries de conservation",
            "10.73Z" => "10.73Z - Fabrication de pâtes alimentaires",
            "10.81Z" => "10.81Z - Fabrication de sucre",
            "10.82Z" => "10.82Z - Fabrication de cacao, chocolat et de produits de confiserie",
            "10.83Z" => "10.83Z - Transformation du thé et du café",
            "10.84Z" => "10.84Z - Fabrication de condiments et assaisonnements",
            "10.85Z" => "10.85Z - Fabrication de plats préparés",
            "10.86Z" => "10.86Z - Fabrication d'aliments homogénéisés et diététiques",
            "10.89Z" => "10.89Z - Fabrication d'autres produits alimentaires n.c.a.",
            "10.91Z" => "10.91Z - Fabrication d'aliments pour animaux de ferme",
            "10.92Z" => "10.92Z - Fabrication d'aliments pour animaux de compagnie",
            "11.01Z" => "11.01Z - Production de boissons alcooliques distillées",
            "11.02A" => "11.02A - Fabrication de vins effervescents",
            "11.02B" => "11.02B - Vinification",
            "11.03Z" => "11.03Z - Fabrication de cidre et de vins de fruits",
            "11.04Z" => "11.04Z - Production d'autres boissons fermentées non distillées",
            "11.05Z" => "11.05Z - Fabrication de bière",
            "11.06Z" => "11.06Z - Fabrication de malt",
            "11.07A" => "11.07A - Industrie des eaux de table",
            "11.07B" => "11.07B - Production de boissons rafraîchissantes",
            "12.00Z" => "12.00Z - Fabrication de produits à base de tabac",
            "13.10Z" => "13.10Z - Préparation de fibres textiles et filature",
            "13.20Z" => "13.20Z - Tissage",
            "13.30Z" => "13.30Z - Ennoblissement textile",
            "13.91Z" => "13.91Z - Fabrication d'étoffes à mailles",
            "13.92Z" => "13.92Z - Fabrication d'articles textiles, sauf habillement",
            "13.93Z" => "13.93Z - Fabrication de tapis et moquettes",
            "13.94Z" => "13.94Z - Fabrication de ficelles, cordes et filets",
            "13.95Z" => "13.95Z - Fabrication de non-tissés, sauf habillement",
            "13.96Z" => "13.96Z - Fabrication d'autres textiles techniques et industriels",
            "13.99Z" => "13.99Z - Fabrication d'autres textiles n.c.a.",
            "14.11Z" => "14.11Z - Fabrication de vêtements en cuir",
            "14.12Z" => "14.12Z - Fabrication de vêtements de travail",
            "14.13Z" => "14.13Z - Fabrication de vêtements de dessus",
            "14.14Z" => "14.14Z - Fabrication de vêtements de dessous",
            "14.19Z" => "14.19Z - Fabrication d'autres vêtements et accessoires",
            "14.20Z" => "14.20Z - Fabrication d'articles en fourrure",
            "14.31Z" => "14.31Z - Fabrication d'articles chaussants à mailles",
            "14.39Z" => "14.39Z - Fabrication d'autres articles à mailles",
            "15.11Z" => "15.11Z - Apprêt et tannage des cuirs ; préparation et teinture des fourrures",
            "15.12Z" => "15.12Z - Fabrication d'articles de voyage, de maroquinerie et de sellerie",
            "15.20Z" => "15.20Z - Fabrication de chaussures",
            "16.10A" => "16.10A - Sciage et rabotage du bois, hors imprégnation",
            "16.10B" => "16.10B - Imprégnation du bois",
            "16.21Z" => "16.21Z - Fabrication de placage et de panneaux de bois",
            "16.22Z" => "16.22Z - Fabrication de parquets assemblés",
            "16.23Z" => "16.23Z - Fabrication de charpentes et d'autres menuiseries",
            "16.24Z" => "16.24Z - Fabrication d'emballages en bois",
            "16.29Z" => "16.29Z - Fabrication d'objets divers en bois ; fabrication d'objets en liège, vannerie et sparterie",
            "17.11Z" => "17.11Z - Fabrication de pâte à papier",
            "17.12Z" => "17.12Z - Fabrication de papier et de carton",
            "17.21A" => "17.21A - Fabrication de carton ondulé",
            "17.21B" => "17.21B - Fabrication de cartonnages",
            "17.21C" => "17.21C - Fabrication d'emballages en papier",
            "17.22Z" => "17.22Z - Fabrication d'articles en papier à usage sanitaire ou domestique",
            "17.23Z" => "17.23Z - Fabrication d'articles de papeterie",
            "17.24Z" => "17.24Z - Fabrication de papiers peints",
            "17.29Z" => "17.29Z - Fabrication d'autres articles en papier ou en carton",
            "18.11Z" => "18.11Z - Imprimerie de journaux",
            "18.12Z" => "18.12Z - Autre imprimerie (labeur)",
            "18.13Z" => "18.13Z - Activités de pré-presse",
            "18.14Z" => "18.14Z - Reliure et activités connexes",
            "18.20Z" => "18.20Z - Reproduction d'enregistrements",
            "19.10Z" => "19.10Z - Cokéfaction",
            "19.20Z" => "19.20Z - Raffinage du pétrole",
            "20.11Z" => "20.11Z - Fabrication de gaz industriels",
            "20.12Z" => "20.12Z - Fabrication de colorants et de pigments",
            "20.13A" => "20.13A - Enrichissement et retraitement de matières nucléaires",
            "20.13B" => "20.13B - Fabrication d'autres produits chimiques inorganiques de base n.c.a.",
            "20.14Z" => "20.14Z - Fabrication d'autres produits chimiques organiques de base",
            "20.15Z" => "20.15Z - Fabrication de produits azotés et d'engrais",
            "20.16Z" => "20.16Z - Fabrication de matières plastiques de base",
            "20.17Z" => "20.17Z - Fabrication de caoutchouc synthétique",
            "20.20Z" => "20.20Z - Fabrication de pesticides et d'autres produits agrochimiques",
            "20.30Z" => "20.30Z - Fabrication de peintures, vernis, encres et mastics",
            "20.41Z" => "20.41Z - Fabrication de savons, détergents et produits d'entretien",
            "20.42Z" => "20.42Z - Fabrication de parfums et de produits pour la toilette",
            "20.51Z" => "20.51Z - Fabrication de produits explosifs",
            "20.52Z" => "20.52Z - Fabrication de colles",
            "20.53Z" => "20.53Z - Fabrication d'huiles essentielles",
            "20.59Z" => "20.59Z - Fabrication d'autres produits chimiques n.c.a.",
            "20.60Z" => "20.60Z - Fabrication de fibres artificielles ou synthétiques",
            "21.10Z" => "21.10Z - Fabrication de produits pharmaceutiques de base",
            "21.20Z" => "21.20Z - Fabrication de préparations pharmaceutiques",
            "22.11Z" => "22.11Z - Fabrication et rechapage de pneumatiques",
            "22.19Z" => "22.19Z - Fabrication d'autres articles en caoutchouc",
            "22.21Z" => "22.21Z - Fabrication de plaques, feuilles, tubes et profilés en matières plastiques",
            "22.22Z" => "22.22Z - Fabrication d'emballages en matières plastiques",
            "22.23Z" => "22.23Z - Fabrication d'éléments en matières plastiques pour la construction",
            "22.29A" => "22.29A - Fabrication de pièces techniques à base de matières plastiques",
            "22.29B" => "22.29B - Fabrication de produits de consommation courante en matières plastiques",
            "23.11Z" => "23.11Z - Fabrication de verre plat",
            "23.12Z" => "23.12Z - Façonnage et transformation du verre plat",
            "23.13Z" => "23.13Z - Fabrication de verre creux",
            "23.14Z" => "23.14Z - Fabrication de fibres de verre",
            "23.19Z" => "23.19Z - Fabrication et façonnage d'autres articles en verre, y compris verre technique",
            "23.20Z" => "23.20Z - Fabrication de produits réfractaires",
            "23.31Z" => "23.31Z - Fabrication de carreaux en céramique",
            "23.32Z" => "23.32Z - Fabrication de briques, tuiles et produits de construction, en terre cuite",
            "23.41Z" => "23.41Z - Fabrication d'articles céramiques à usage domestique ou ornemental",
            "23.42Z" => "23.42Z - Fabrication d'appareils sanitaires en céramique",
            "23.43Z" => "23.43Z - Fabrication d'isolateurs et pièces isolantes en céramique",
            "23.44Z" => "23.44Z - Fabrication d'autres produits céramiques à usage technique",
            "23.49Z" => "23.49Z - Fabrication d'autres produits céramiques",
            "23.51Z" => "23.51Z - Fabrication de ciment",
            "23.52Z" => "23.52Z - Fabrication de chaux et plâtre",
            "23.61Z" => "23.61Z - Fabrication d'éléments en béton pour la construction",
            "23.62Z" => "23.62Z - Fabrication d'éléments en plâtre pour la construction",
            "23.63Z" => "23.63Z - Fabrication de béton prêt à l'emploi",
            "23.64Z" => "23.64Z - Fabrication de mortiers et bétons secs",
            "23.65Z" => "23.65Z - Fabrication d'ouvrages en fibre-ciment",
            "23.69Z" => "23.69Z - Fabrication d'autres ouvrages en béton, en ciment ou en plâtre",
            "23.70Z" => "23.70Z - Taille, façonnage et finissage de pierres",
            "23.91Z" => "23.91Z - Fabrication de produits abrasifs",
            "23.99Z" => "23.99Z - Fabrication d'autres produits minéraux non métalliques n.c.a.",
            "24.10Z" => "24.10Z - Sidérurgie",
            "24.20Z" => "24.20Z - Fabrication de tubes, tuyaux, profilés creux et accessoires correspondants en acier",
            "24.31Z" => "24.31Z - Étirage à froid de barres",
            "24.32Z" => "24.32Z - Laminage à froid de feuillards",
            "24.33Z" => "24.33Z - Profilage à froid par formage ou pliage",
            "24.34Z" => "24.34Z - Tréfilage à froid",
            "24.41Z" => "24.41Z - Production de métaux précieux",
            "24.42Z" => "24.42Z - Métallurgie de l'aluminium",
            "24.43Z" => "24.43Z - Métallurgie du plomb, du zinc ou de l'étain",
            "24.44Z" => "24.44Z - Métallurgie du cuivre",
            "24.45Z" => "24.45Z - Métallurgie des autres métaux non ferreux",
            "24.46Z" => "24.46Z - Élaboration et transformation de matières nucléaires",
            "24.51Z" => "24.51Z - Fonderie de fonte",
            "24.52Z" => "24.52Z - Fonderie d'acier",
            "24.53Z" => "24.53Z - Fonderie de métaux légers",
            "24.54Z" => "24.54Z - Fonderie d'autres métaux non ferreux",
            "25.11Z" => "25.11Z - Fabrication de structures métalliques et de parties de structures",
            "25.12Z" => "25.12Z - Fabrication de portes et fenêtres en métal",
            "25.21Z" => "25.21Z - Fabrication de radiateurs et de chaudières pour le chauffage central",
            "25.29Z" => "25.29Z - Fabrication d'autres réservoirs, citernes et conteneurs métalliques",
            "25.30Z" => "25.30Z - Fabrication de générateurs de vapeur, à l'exception des chaudières pour le chauffage central",
            "25.40Z" => "25.40Z - Fabrication d'armes et de munitions",
            "25.50A" => "25.50A - Forge, estampage, matriçage ; métallurgie des poudres",
            "25.50B" => "25.50B - Découpage, emboutissage",
            "25.61Z" => "25.61Z - Traitement et revêtement des métaux",
            "25.62A" => "25.62A - Décolletage",
            "25.62B" => "25.62B - Mécanique industrielle",
            "25.71Z" => "25.71Z - Fabrication de coutellerie",
            "25.72Z" => "25.72Z - Fabrication de serrures et de ferrures",
            "25.73A" => "25.73A - Fabrication de moules et modèles",
            "25.73B" => "25.73B - Fabrication d'autres outillages",
            "25.91Z" => "25.91Z - Fabrication de fûts et emballages métalliques similaires",
            "25.92Z" => "25.92Z - Fabrication d'emballages métalliques légers",
            "25.93Z" => "25.93Z - Fabrication d'articles en fils métalliques, de chaînes et de ressorts",
            "25.94Z" => "25.94Z - Fabrication de vis et de boulons",
            "25.99A" => "25.99A - Fabrication d'articles métalliques ménagers",
            "25.99B" => "25.99B - Fabrication d'autres articles métalliques",
            "26.11Z" => "26.11Z - Fabrication de composants électroniques",
            "26.12Z" => "26.12Z - Fabrication de cartes électroniques assemblées",
            "26.20Z" => "26.20Z - Fabrication d'ordinateurs et d'équipements périphériques",
            "26.30Z" => "26.30Z - Fabrication d'équipements de communication",
            "26.40Z" => "26.40Z - Fabrication de produits électroniques grand public",
            "26.51A" => "26.51A - Fabrication d'équipements d'aide à la navigation",
            "26.51B" => "26.51B - Fabrication d'instrumentation scientifique et technique",
            "26.52Z" => "26.52Z - Horlogerie",
            "26.60Z" => "26.60Z - Fabrication d'équipements d'irradiation médicale, d'équipements électromédicaux et électrothérapeutiques",
            "26.70Z" => "26.70Z - Fabrication de matériels optique et photographique",
            "26.80Z" => "26.80Z - Fabrication de supports magnétiques et optiques",
            "27.11Z" => "27.11Z - Fabrication de moteurs, génératrices et transformateurs électriques",
            "27.12Z" => "27.12Z - Fabrication de matériel de distribution et de commande électrique",
            "27.20Z" => "27.20Z - Fabrication de piles et d'accumulateurs électriques",
            "27.31Z" => "27.31Z - Fabrication de câbles de fibres optiques",
            "27.32Z" => "27.32Z - Fabrication d'autres fils et câbles électroniques ou électriques",
            "27.33Z" => "27.33Z - Fabrication de matériel d'installation électrique",
            "27.40Z" => "27.40Z - Fabrication d'appareils d'éclairage électrique",
            "27.51Z" => "27.51Z - Fabrication d'appareils électroménagers",
            "27.52Z" => "27.52Z - Fabrication d'appareils ménagers non électriques",
            "27.90Z" => "27.90Z - Fabrication d'autres matériels électriques",
            "28.11Z" => "28.11Z - Fabrication de moteurs et turbines, à l'exception des moteurs d'avions et de véhicules",
            "28.12Z" => "28.12Z - Fabrication d'équipements hydrauliques et pneumatiques",
            "28.13Z" => "28.13Z - Fabrication d'autres pompes et compresseurs",
            "28.14Z" => "28.14Z - Fabrication d'autres articles de robinetterie",
            "28.15Z" => "28.15Z - Fabrication d'engrenages et d'organes mécaniques de transmission",
            "28.21Z" => "28.21Z - Fabrication de fours et brûleurs",
            "28.22Z" => "28.22Z - Fabrication de matériel de levage et de manutention",
            "28.23Z" => "28.23Z - Fabrication de machines et d'équipements de bureau (à l'exception des ordinateurs et équipements périphériques)",
            "28.24Z" => "28.24Z - Fabrication d'outillage portatif à moteur incorporé",
            "28.25Z" => "28.25Z - Fabrication d'équipements aérauliques et frigorifiques industriels",
            "28.29A" => "28.29A - Fabrication d'équipements d'emballage, de conditionnement et de pesage",
            "28.29B" => "28.29B - Fabrication d'autres machines d'usage général",
            "28.30Z" => "28.30Z - Fabrication de machines agricoles et forestières",
            "28.41Z" => "28.41Z - Fabrication de machines-outils pour le travail des métaux",
            "28.49Z" => "28.49Z - Fabrication d'autres machines-outils",
            "28.91Z" => "28.91Z - Fabrication de machines pour la métallurgie",
            "28.92Z" => "28.92Z - Fabrication de machines pour l'extraction ou la construction",
            "28.93Z" => "28.93Z - Fabrication de machines pour l'industrie agro-alimentaire",
            "28.94Z" => "28.94Z - Fabrication de machines pour les industries textiles",
            "28.95Z" => "28.95Z - Fabrication de machines pour les industries du papier et du carton",
            "28.96Z" => "28.96Z - Fabrication de machines pour le travail du caoutchouc ou des plastiques",
            "28.99A" => "28.99A - Fabrication de machines d'imprimerie",
            "28.99B" => "28.99B - Fabrication d'autres machines spécialisées",
            "29.10Z" => "29.10Z - Construction de véhicules automobiles",
            "29.20Z" => "29.20Z - Fabrication de carrosseries et remorques",
            "29.31Z" => "29.31Z - Fabrication d'équipements électriques et électroniques automobiles",
            "29.32Z" => "29.32Z - Fabrication d'autres équipements automobiles",
            "30.11Z" => "30.11Z - Construction de navires et de structures flottantes",
            "30.12Z" => "30.12Z - Construction de bateaux de plaisance",
            "30.20Z" => "30.20Z - Construction de locomotives et d'autre matériel ferroviaire roulant",
            "30.30Z" => "30.30Z - Construction aéronautique et spatiale",
            "30.40Z" => "30.40Z - Construction de véhicules militaires de combat",
            "30.91Z" => "30.91Z - Fabrication de motocycles",
            "30.92Z" => "30.92Z - Fabrication de bicyclettes et de véhicules pour invalides",
            "30.99Z" => "30.99Z - Fabrication d'autres équipements de transport n.c.a.",
            "31.01Z" => "31.01Z - Fabrication de meubles de bureau et de magasin",
            "31.02Z" => "31.02Z - Fabrication de meubles de cuisine",
            "31.03Z" => "31.03Z - Fabrication de matelas",
            "31.09A" => "31.09A - Fabrication de sièges d'ameublement d'intérieur",
            "31.09B" => "31.09B - Fabrication d'autres meubles et industries connexes de l'ameublement",
            "32.11Z" => "32.11Z - Frappe de monnaie",
            "32.12Z" => "32.12Z - Fabrication d'articles de joaillerie et bijouterie",
            "32.13Z" => "32.13Z - Fabrication d'articles de bijouterie fantaisie et articles similaires",
            "32.20Z" => "32.20Z - Fabrication d'instruments de musique",
            "32.30Z" => "32.30Z - Fabrication d'articles de sport",
            "32.40Z" => "32.40Z - Fabrication de jeux et jouets",
            "32.50A" => "32.50A - Fabrication de matériel médico-chirurgical et dentaire",
            "32.50B" => "32.50B - Fabrication de lunettes",
            "32.91Z" => "32.91Z - Fabrication d'articles de brosserie",
            "32.99Z" => "32.99Z - Autres activités manufacturières n.c.a.",
            "33.11Z" => "33.11Z - Réparation d'ouvrages en métaux",
            "33.12Z" => "33.12Z - Réparation de machines et équipements mécaniques",
            "33.13Z" => "33.13Z - Réparation de matériels électroniques et optiques",
            "33.14Z" => "33.14Z - Réparation d'équipements électriques",
            "33.15Z" => "33.15Z - Réparation et maintenance navale",
            "33.16Z" => "33.16Z - Réparation et maintenance d'aéronefs et d'engins spatiaux",
            "33.17Z" => "33.17Z - Réparation et maintenance d'autres équipements de transport",
            "33.19Z" => "33.19Z - Réparation d'autres équipements",
            "33.20A" => "33.20A - Installation de structures métalliques, chaudronnées et de tuyauterie",
            "33.20B" => "33.20B - Installation de machines et équipements mécaniques",
            "33.20C" => "33.20C - Conception d'ensemble et assemblage sur site industriel d'équipements de contrôle des processus industriels",
            "33.20D" => "33.20D - Installation d'équipements électriques, de matériels électroniques et optiques ou d'autres matériels",
            "35.11Z" => "35.11Z - Production d'électricité",
            "35.12Z" => "35.12Z - Transport d'électricité",
            "35.13Z" => "35.13Z - Distribution d'électricité",
            "35.14Z" => "35.14Z - Commerce d'électricité",
            "35.21Z" => "35.21Z - Production de combustibles gazeux",
            "35.22Z" => "35.22Z - Distribution de combustibles gazeux par conduites",
            "35.23Z" => "35.23Z - Commerce de combustibles gazeux par conduites",
            "35.30Z" => "35.30Z - Production et distribution de vapeur et d'air conditionné",
            "36.00Z" => "36.00Z - Captage, traitement et distribution d'eau",
            "37.00Z" => "37.00Z - Collecte et traitement des eaux usées",
            "38.11Z" => "38.11Z - Collecte des déchets non dangereux",
            "38.12Z" => "38.12Z - Collecte des déchets dangereux",
            "38.21Z" => "38.21Z - Traitement et élimination des déchets non dangereux",
            "38.22Z" => "38.22Z - Traitement et élimination des déchets dangereux",
            "38.31Z" => "38.31Z - Démantèlement d'épaves",
            "38.32Z" => "38.32Z - Récupération de déchets triés",
            "39.00Z" => "39.00Z - Dépollution et autres services de gestion des déchets",
            "41.10A" => "41.10A - Promotion immobilière de logements",
            "41.10B" => "41.10B - Promotion immobilière de bureaux",
            "41.10C" => "41.10C - Promotion immobilière d'autres bâtiments",
            "41.10D" => "41.10D - Supports juridiques de programmes",
            "41.20A" => "41.20A - Construction de maisons individuelles",
            "41.20B" => "41.20B - Construction d'autres bâtiments",
            "42.11Z" => "42.11Z - Construction de routes et autoroutes",
            "42.12Z" => "42.12Z - Construction de voies ferrées de surface et souterraines",
            "42.13A" => "42.13A - Construction d'ouvrages d'art",
            "42.13B" => "42.13B - Construction et entretien de tunnels",
            "42.21Z" => "42.21Z - Construction de réseaux pour fluides",
            "42.22Z" => "42.22Z - Construction de réseaux électriques et de télécommunications",
            "42.91Z" => "42.91Z - Construction d'ouvrages maritimes et fluviaux",
            "42.99Z" => "42.99Z - Construction d'autres ouvrages de génie civil n.c.a.",
            "43.11Z" => "43.11Z - Travaux de démolition",
            "43.12A" => "43.12A - Travaux de terrassement courants et travaux préparatoires",
            "43.12B" => "43.12B - Travaux de terrassement spécialisés ou de grande masse",
            "43.13Z" => "43.13Z - Forages et sondages",
            "43.21A" => "43.21A - Travaux d'installation électrique dans tous locaux",
            "43.21B" => "43.21B - Travaux d'installation électrique sur la voie publique",
            "43.22A" => "43.22A - Travaux d'installation d'eau et de gaz en tous locaux",
            "43.22B" => "43.22B - Travaux d'installation d'équipements thermiques et de climatisation",
            "43.29A" => "43.29A - Travaux d'isolation",
            "43.29B" => "43.29B - Autres travaux d'installation n.c.a.",
            "43.31Z" => "43.31Z - Travaux de plâtrerie",
            "43.32A" => "43.32A - Travaux de menuiserie bois et PVC",
            "43.32B" => "43.32B - Travaux de menuiserie métallique et serrurerie",
            "43.32C" => "43.32C - Agencement de lieux de vente",
            "43.33Z" => "43.33Z - Travaux de revêtement des sols et des murs",
            "43.34Z" => "43.34Z - Travaux de peinture et vitrerie",
            "43.39Z" => "43.39Z - Autres travaux de finition",
            "43.91A" => "43.91A - Travaux de charpente",
            "43.91B" => "43.91B - Travaux de couverture par éléments",
            "43.99A" => "43.99A - Travaux d'étanchéification",
            "43.99B" => "43.99B - Travaux de montage de structures métalliques",
            "43.99C" => "43.99C - Travaux de maçonnerie générale et gros œuvre de bâtiment",
            "43.99D" => "43.99D - Autres travaux spécialisés de construction",
            "43.99E" => "43.99E - Location avec opérateur de matériel de construction",
            "45.11Z" => "45.11Z - Commerce de voitures et de véhicules automobiles légers",
            "45.19Z" => "45.19Z - Commerce d'autres véhicules automobiles",
            "45.20A" => "45.20A - Entretien et réparation de véhicules automobiles légers",
            "45.20B" => "45.20B - Entretien et réparation d'autres véhicules automobiles",
            "45.31Z" => "45.31Z - Commerce de gros d'équipements automobiles",
            "45.32Z" => "45.32Z - Commerce de détail d'équipements automobiles",
            "45.40Z" => "45.40Z - Commerce et réparation de motocycles",
            "46.11Z" => "46.11Z - Intermédiaires du commerce en matières premières agricoles, animaux vivants, matières premières textiles et produits semi-finis",
            "46.12A" => "46.12A - Centrales d'achat de carburant",
            "46.12B" => "46.12B - Autres intermédiaires du commerce en combustibles, métaux, minéraux et produits chimiques",
            "46.13Z" => "46.13Z - Intermédiaires du commerce en bois et matériaux de construction",
            "46.14Z" => "46.14Z - Intermédiaires du commerce en machines, équipements industriels, navires et avions",
            "46.15Z" => "46.15Z - Intermédiaires du commerce en meubles, articles de ménage et quincaillerie",
            "46.16Z" => "46.16Z - Intermédiaires du commerce en textiles, habillement, fourrures, chaussures et articles en cuir",
            "46.17A" => "46.17A - Centrales d'achat alimentaires",
            "46.17B" => "46.17B - Autres intermédiaires du commerce en denrées, boissons et tabac",
            "46.18Z" => "46.18Z - Intermédiaires spécialisés dans le commerce d'autres produits spécifiques",
            "46.19A" => "46.19A - Centrales d'achat non alimentaires",
            "46.19B" => "46.19B - Autres intermédiaires du commerce en produits divers",
            "46.21Z" => "46.21Z - Commerce de gros (commerce interentreprises) de céréales, de tabac non manufacturé, de semences et d'aliments pour le bétail",
            "46.22Z" => "46.22Z - Commerce de gros (commerce interentreprises) de fleurs et plantes",
            "46.23Z" => "46.23Z - Commerce de gros (commerce interentreprises) d'animaux vivants",
            "46.24Z" => "46.24Z - Commerce de gros (commerce interentreprises) de cuirs et peaux",
            "46.31Z" => "46.31Z - Commerce de gros (commerce interentreprises) de fruits et légumes",
            "46.32A" => "46.32A - Commerce de gros (commerce interentreprises) de viandes de boucherie",
            "46.32B" => "46.32B - Commerce de gros (commerce interentreprises) de produits à base de viande",
            "46.32C" => "46.32C - Commerce de gros (commerce interentreprises) de volailles et gibier",
            "46.33Z" => "46.33Z - Commerce de gros (commerce interentreprises) de produits laitiers, œufs, huiles et matières grasses comestibles",
            "46.34Z" => "46.34Z - Commerce de gros (commerce interentreprises) de boissons",
            "46.35Z" => "46.35Z - Commerce de gros (commerce interentreprises) de produits à base de tabac",
            "46.36Z" => "46.36Z - Commerce de gros (commerce interentreprises) de sucre, chocolat et confiserie",
            "46.37Z" => "46.37Z - Commerce de gros (commerce interentreprises) de café, thé, cacao et épices",
            "46.38A" => "46.38A - Commerce de gros (commerce interentreprises) de poissons, crustacés et mollusques",
            "46.38B" => "46.38B - Commerce de gros (commerce interentreprises) alimentaire spécialisé divers",
            "46.39A" => "46.39A - Commerce de gros (commerce interentreprises) de produits surgelés",
            "46.39B" => "46.39B - Commerce de gros (commerce interentreprises) alimentaire non spécialisé",
            "46.41Z" => "46.41Z - Commerce de gros (commerce interentreprises) de textiles",
            "46.42Z" => "46.42Z - Commerce de gros (commerce interentreprises) d'habillement et de chaussures",
            "46.43Z" => "46.43Z - Commerce de gros (commerce interentreprises) d'appareils électroménagers",
            "46.44Z" => "46.44Z - Commerce de gros (commerce interentreprises) de vaisselle, verrerie et produits d'entretien",
            "46.45Z" => "46.45Z - Commerce de gros (commerce interentreprises) de parfumerie et de produits de beauté",
            "46.46Z" => "46.46Z - Commerce de gros (commerce interentreprises) de produits pharmaceutiques",
            "46.47Z" => "46.47Z - Commerce de gros (commerce interentreprises) de meubles, de tapis et d'appareils d'éclairage",
            "46.48Z" => "46.48Z - Commerce de gros (commerce interentreprises) d'articles d'horlogerie et de bijouterie",
            "46.49Z" => "46.49Z - Commerce de gros (commerce interentreprises) d'autres biens domestiques",
            "46.51Z" => "46.51Z - Commerce de gros (commerce interentreprises) d'ordinateurs, d'équipements informatiques périphériques et de logiciels",
            "46.52Z" => "46.52Z - Commerce de gros (commerce interentreprises) de composants et d'équipements électroniques et de télécommunication",
            "46.61Z" => "46.61Z - Commerce de gros (commerce interentreprises) de matériel agricole",
            "46.62Z" => "46.62Z - Commerce de gros (commerce interentreprises) de machines-outils",
            "46.63Z" => "46.63Z - Commerce de gros (commerce interentreprises) de machines pour l'extraction, la construction et le génie civil",
            "46.64Z" => "46.64Z - Commerce de gros (commerce interentreprises) de machines pour l'industrie textile et l'habillement",
            "46.65Z" => "46.65Z - Commerce de gros (commerce interentreprises) de mobilier de bureau",
            "46.66Z" => "46.66Z - Commerce de gros (commerce interentreprises) d'autres machines et équipements de bureau",
            "46.69A" => "46.69A - Commerce de gros (commerce interentreprises) de matériel électrique",
            "46.69B" => "46.69B - Commerce de gros (commerce interentreprises) de fournitures et équipements industriels divers",
            "46.69C" => "46.69C - Commerce de gros (commerce interentreprises) de fournitures et équipements divers pour le commerce et les services",
            "46.71Z" => "46.71Z - Commerce de gros (commerce interentreprises) de combustibles et de produits annexes",
            "46.72Z" => "46.72Z - Commerce de gros (commerce interentreprises) de minerais et métaux",
            "46.73A" => "46.73A - Commerce de gros (commerce interentreprises) de bois et de matériaux de construction",
            "46.73B" => "46.73B - Commerce de gros (commerce interentreprises) d'appareils sanitaires et de produits de décoration",
            "46.74A" => "46.74A - Commerce de gros (commerce interentreprises) de quincaillerie",
            "46.74B" => "46.74B - Commerce de gros (commerce interentreprises) de fournitures pour la plomberie et le chauffage",
            "46.75Z" => "46.75Z - Commerce de gros (commerce interentreprises) de produits chimiques",
            "46.76Z" => "46.76Z - Commerce de gros (commerce interentreprises) d'autres produits intermédiaires",
            "46.77Z" => "46.77Z - Commerce de gros (commerce interentreprises) de déchets et débris",
            "46.90Z" => "46.90Z - Commerce de gros (commerce interentreprises) non spécialisé",
            "47.11A" => "47.11A - Commerce de détail de produits surgelés",
            "47.11B" => "47.11B - Commerce d'alimentation générale",
            "47.11C" => "47.11C - Supérettes",
            "47.11D" => "47.11D - Supermarchés",
            "47.11E" => "47.11E - Magasins multi-commerces",
            "47.11F" => "47.11F - Hypermarchés",
            "47.19A" => "47.19A - Grands magasins",
            "47.19B" => "47.19B - Autres commerces de détail en magasin non spécialisé",
            "47.21Z" => "47.21Z - Commerce de détail de fruits et légumes en magasin spécialisé",
            "47.22Z" => "47.22Z - Commerce de détail de viandes et de produits à base de viande en magasin spécialisé",
            "47.23Z" => "47.23Z - Commerce de détail de poissons, crustacés et mollusques en magasin spécialisé",
            "47.24Z" => "47.24Z - Commerce de détail de pain, pâtisserie et confiserie en magasin spécialisé",
            "47.25Z" => "47.25Z - Commerce de détail de boissons en magasin spécialisé",
            "47.26Z" => "47.26Z - Commerce de détail de produits à base de tabac en magasin spécialisé",
            "47.29Z" => "47.29Z - Autres commerces de détail alimentaires en magasin spécialisé",
            "47.30Z" => "47.30Z - Commerce de détail de carburants en magasin spécialisé",
            "47.41Z" => "47.41Z - Commerce de détail d'ordinateurs, d'unités périphériques et de logiciels en magasin spécialisé",
            "47.42Z" => "47.42Z - Commerce de détail de matériels de télécommunication en magasin spécialisé",
            "47.43Z" => "47.43Z - Commerce de détail de matériels audio et vidéo en magasin spécialisé",
            "47.51Z" => "47.51Z - Commerce de détail de textiles en magasin spécialisé",
            "47.52A" => "47.52A - Commerce de détail de quincaillerie, peintures et verres en petites surfaces (moins de 400 m²)",
            "47.52B" => "47.52B - Commerce de détail de quincaillerie, peintures et verres en grandes surfaces (400 m² et plus)",
            "47.53Z" => "47.53Z - Commerce de détail de tapis, moquettes et revêtements de murs et de sols en magasin spécialisé",
            "47.54Z" => "47.54Z - Commerce de détail d'appareils électroménagers en magasin spécialisé",
            "47.59A" => "47.59A - Commerce de détail de meubles",
            "47.59B" => "47.59B - Commerce de détail d'autres équipements du foyer",
            "47.61Z" => "47.61Z - Commerce de détail de livres en magasin spécialisé",
            "47.62Z" => "47.62Z - Commerce de détail de journaux et papeterie en magasin spécialisé",
            "47.63Z" => "47.63Z - Commerce de détail d'enregistrements musicaux et vidéo en magasin spécialisé",
            "47.64Z" => "47.64Z - Commerce de détail d'articles de sport en magasin spécialisé",
            "47.65Z" => "47.65Z - Commerce de détail de jeux et jouets en magasin spécialisé",
            "47.71Z" => "47.71Z - Commerce de détail d'habillement en magasin spécialisé",
            "47.72A" => "47.72A - Commerce de détail de la chaussure",
            "47.72B" => "47.72B - Commerce de détail de maroquinerie et d'articles de voyage",
            "47.73Z" => "47.73Z - Commerce de détail de produits pharmaceutiques en magasin spécialisé",
            "47.74Z" => "47.74Z - Commerce de détail d'articles médicaux et orthopédiques en magasin spécialisé",
            "47.75Z" => "47.75Z - Commerce de détail de parfumerie et de produits de beauté en magasin spécialisé",
            "47.76Z" => "47.76Z - Commerce de détail de fleurs, plantes, graines, engrais, animaux de compagnie et aliments pour ces animaux en magasin spécialisé",
            "47.77Z" => "47.77Z - Commerce de détail d'articles d'horlogerie et de bijouterie en magasin spécialisé",
            "47.78A" => "47.78A - Commerces de détail d'optique",
            "47.78B" => "47.78B - Commerces de détail de charbons et combustibles",
            "47.78C" => "47.78C - Autres commerces de détail spécialisés divers",
            "47.79Z" => "47.79Z - Commerce de détail de biens d'occasion en magasin",
            "47.81Z" => "47.81Z - Commerce de détail alimentaire sur éventaires et marchés",
            "47.82Z" => "47.82Z - Commerce de détail de textiles, d'habillement et de chaussures sur éventaires et marchés",
            "47.89Z" => "47.89Z - Autres commerces de détail sur éventaires et marchés",
            "47.91A" => "47.91A - Vente à distance sur catalogue général",
            "47.91B" => "47.91B - Vente à distance sur catalogue spécialisé",
            "47.99A" => "47.99A - Vente à domicile",
            "47.99B" => "47.99B - Vente par automates et autres commerces de détail hors magasin, éventaires ou marchés n.c.a.",
            "49.10Z" => "49.10Z - Transport ferroviaire interurbain de voyageurs",
            "49.20Z" => "49.20Z - Transports ferroviaires de fret",
            "49.31Z" => "49.31Z - Transports urbains et suburbains de voyageurs",
            "49.32Z" => "49.32Z - Transports de voyageurs par taxis",
            "49.39A" => "49.39A - Transports routiers réguliers de voyageurs",
            "49.39B" => "49.39B - Autres transports routiers de voyageurs",
            "49.39C" => "49.39C - Téléphériques et remontées mécaniques",
            "49.41A" => "49.41A - Transports routiers de fret interurbains",
            "49.41B" => "49.41B - Transports routiers de fret de proximité",
            "49.41C" => "49.41C - Location de camions avec chauffeur",
            "49.42Z" => "49.42Z - Services de déménagement",
            "49.50Z" => "49.50Z - Transports par conduites",
            "50.10Z" => "50.10Z - Transports maritimes et côtiers de passagers",
            "50.20Z" => "50.20Z - Transports maritimes et côtiers de fret",
            "50.30Z" => "50.30Z - Transports fluviaux de passagers",
            "50.40Z" => "50.40Z - Transports fluviaux de fret",
            "51.10Z" => "51.10Z - Transports aériens de passagers",
            "51.21Z" => "51.21Z - Transports aériens de fret",
            "51.22Z" => "51.22Z - Transports spatiaux",
            "52.10A" => "52.10A - Entreposage et stockage frigorifique",
            "52.10B" => "52.10B - Entreposage et stockage non frigorifique",
            "52.21Z" => "52.21Z - Services auxiliaires des transports terrestres",
            "52.22Z" => "52.22Z - Services auxiliaires des transports par eau",
            "52.23Z" => "52.23Z - Services auxiliaires des transports aériens",
            "52.24A" => "52.24A - Manutention portuaire",
            "52.24B" => "52.24B - Manutention non portuaire",
            "52.29A" => "52.29A - Messagerie, fret express",
            "52.29B" => "52.29B - Affrètement et organisation des transports",
            "53.10Z" => "53.10Z - Activités de poste dans le cadre d'une obligation de service universel",
            "53.20Z" => "53.20Z - Autres activités de poste et de courrier",
            "55.10Z" => "55.10Z - Hôtels et hébergement similaire",
            "55.20Z" => "55.20Z - Hébergement touristique et autre hébergement de courte durée",
            "55.30Z" => "55.30Z - Terrains de camping et parcs pour caravanes ou véhicules de loisirs",
            "55.90Z" => "55.90Z - Autres hébergements",
            "56.10A" => "56.10A - Restauration traditionnelle",
            "56.10B" => "56.10B - Cafétérias et autres libres-services",
            "56.10C" => "56.10C - Restauration de type rapide",
            "56.21Z" => "56.21Z - Services des traiteurs",
            "56.29A" => "56.29A - Restauration collective sous contrat",
            "56.29B" => "56.29B - Autres services de restauration n.c.a.",
            "56.30Z" => "56.30Z - Débits de boissons",
            "58.11Z" => "58.11Z - Édition de livres",
            "58.12Z" => "58.12Z - Édition de répertoires et de fichiers d'adresses",
            "58.13Z" => "58.13Z - Édition de journaux",
            "58.14Z" => "58.14Z - Édition de revues et périodiques",
            "58.19Z" => "58.19Z - Autres activités d'édition",
            "58.21Z" => "58.21Z - Édition de jeux électroniques",
            "58.29A" => "58.29A - Édition de logiciels système et de réseau",
            "58.29B" => "58.29B - Édition de logiciels outils de développement et de langages",
            "58.29C" => "58.29C - Édition de logiciels applicatifs",
            "59.11A" => "59.11A - Production de films et de programmes pour la télévision",
            "59.11B" => "59.11B - Production de films institutionnels et publicitaires",
            "59.11C" => "59.11C - Production de films pour le cinéma",
            "59.12Z" => "59.12Z - Post-production de films cinématographiques, de vidéo et de programmes de télévision",
            "59.13A" => "59.13A - Distribution de films cinématographiques",
            "59.13B" => "59.13B - Édition et distribution vidéo",
            "59.14Z" => "59.14Z - Projection de films cinématographiques",
            "59.20Z" => "59.20Z - Enregistrement sonore et édition musicale",
            "60.10Z" => "60.10Z - Édition et diffusion de programmes radio",
            "60.20A" => "60.20A - Édition de chaînes généralistes",
            "60.20B" => "60.20B - Édition de chaînes thématiques",
            "61.10Z" => "61.10Z - Télécommunications filaires",
            "61.20Z" => "61.20Z - Télécommunications sans fil",
            "61.30Z" => "61.30Z - Télécommunications par satellite",
            "61.90Z" => "61.90Z - Autres activités de télécommunication",
            "62.01Z" => "62.01Z - Programmation informatique",
            "62.02A" => "62.02A - Conseil en systèmes et logiciels informatiques",
            "62.02B" => "62.02B - Tierce maintenance de systèmes et d'applications informatiques",
            "62.03Z" => "62.03Z - Gestion d'installations informatiques",
            "62.09Z" => "62.09Z - Autres activités informatiques",
            "63.11Z" => "63.11Z - Traitement de données, hébergement et activités connexes",
            "63.12Z" => "63.12Z - Portails internet",
            "63.91Z" => "63.91Z - Activités des agences de presse",
            "63.99Z" => "63.99Z - Autres services d'information n.c.a.",
            "64.11Z" => "64.11Z - Activités de banque centrale",
            "64.19Z" => "64.19Z - Autres intermédiations monétaires",
            "64.20Z" => "64.20Z - Activités des sociétés holding",
            "64.30Z" => "64.30Z - Fonds de placement et entités financières similaires",
            "64.91Z" => "64.91Z - Crédit-bail",
            "64.92Z" => "64.92Z - Autre distribution de crédit",
            "64.99Z" => "64.99Z - Autres activités des services financiers, hors assurance et caisses de retraite, n.c.a.",
            "65.11Z" => "65.11Z - Assurance vie",
            "65.12Z" => "65.12Z - Autres assurances",
            "65.20Z" => "65.20Z - Réassurance",
            "65.30Z" => "65.30Z - Caisses de retraite",
            "66.11Z" => "66.11Z - Administration de marchés financiers",
            "66.12Z" => "66.12Z - Courtage de valeurs mobilières et de marchandises",
            "66.19A" => "66.19A - Supports juridiques de gestion de patrimoine mobilier",
            "66.19B" => "66.19B - Autres activités auxiliaires de services financiers, hors assurance et caisses de retraite, n.c.a.",
            "66.21Z" => "66.21Z - Évaluation des risques et dommages",
            "66.22Z" => "66.22Z - Activités des agents et courtiers d'assurances",
            "66.29Z" => "66.29Z - Autres activités auxiliaires d'assurance et de caisses de retraite",
            "66.30Z" => "66.30Z - Gestion de fonds",
            "68.10Z" => "68.10Z - Activités des marchands de biens immobiliers",
            "68.20A" => "68.20A - Location de logements",
            "68.20B" => "68.20B - Location de terrains et d'autres biens immobiliers",
            "68.31Z" => "68.31Z - Agences immobilières",
            "68.32A" => "68.32A - Administration d'immeubles et autres biens immobiliers",
            "68.32B" => "68.32B - Supports juridiques de gestion de patrimoine immobilier",
            "69.10Z" => "69.10Z - Activités juridiques",
            "69.20Z" => "69.20Z - Activités comptables",
            "70.10Z" => "70.10Z - Activités des sièges sociaux",
            "70.21Z" => "70.21Z - Conseil en relations publiques et communication",
            "70.22Z" => "70.22Z - Conseil pour les affaires et autres conseils de gestion",
            "71.11Z" => "71.11Z - Activités d'architecture",
            "71.12A" => "71.12A - Activité des géomètres",
            "71.12B" => "71.12B - Ingénierie, études techniques",
            "71.20A" => "71.20A - Contrôle technique automobile",
            "71.20B" => "71.20B - Analyses, essais et inspections techniques",
            "72.11Z" => "72.11Z - Recherche-développement en biotechnologie",
            "72.19Z" => "72.19Z - Recherche-développement en autres sciences physiques et naturelles",
            "72.20Z" => "72.20Z - Recherche-développement en sciences humaines et sociales",
            "73.11Z" => "73.11Z - Activités des agences de publicité",
            "73.12Z" => "73.12Z - Régie publicitaire de médias",
            "73.20Z" => "73.20Z - Études de marché et sondages",
            "74.10Z" => "74.10Z - Activités spécialisées de design",
            "74.20Z" => "74.20Z - Activités photographiques",
            "74.30Z" => "74.30Z - Traduction et interprétation",
            "74.90A" => "74.90A - Activité des économistes de la construction",
            "74.90B" => "74.90B - Activités spécialisées, scientifiques et techniques diverses",
            "75.00Z" => "75.00Z - Activités vétérinaires",
            "77.11A" => "77.11A - Location de courte durée de voitures et de véhicules automobiles légers",
            "77.11B" => "77.11B - Location de longue durée de voitures et de véhicules automobiles légers",
            "77.12Z" => "77.12Z - Location et location-bail de camions",
            "77.21Z" => "77.21Z - Location et location-bail d'articles de loisirs et de sport",
            "77.22Z" => "77.22Z - Location de vidéocassettes et disques vidéo",
            "77.29Z" => "77.29Z - Location et location-bail d'autres biens personnels et domestiques",
            "77.31Z" => "77.31Z - Location et location-bail de machines et équipements agricoles",
            "77.32Z" => "77.32Z - Location et location-bail de machines et équipements pour la construction",
            "77.33Z" => "77.33Z - Location et location-bail de machines de bureau et de matériel informatique",
            "77.34Z" => "77.34Z - Location et location-bail de matériels de transport par eau",
            "77.35Z" => "77.35Z - Location et location-bail de matériels de transport aérien",
            "77.39Z" => "77.39Z - Location et location-bail d'autres machines, équipements et biens matériels n.c.a.",
            "77.40Z" => "77.40Z - Location-bail de propriété intellectuelle et de produits similaires, à l'exception des œuvres soumises à copyright",
            "78.10Z" => "78.10Z - Activités des agences de placement de main-d'œuvre",
            "78.20Z" => "78.20Z - Activités des agences de travail temporaire",
            "78.30Z" => "78.30Z - Autre mise à disposition de ressources humaines",
            "79.11Z" => "79.11Z - Activités des agences de voyage",
            "79.12Z" => "79.12Z - Activités des voyagistes",
            "79.90Z" => "79.90Z - Autres services de réservation et activités connexes",
            "80.10Z" => "80.10Z - Activités de sécurité privée : Prévention et sécurité privée en France",
            "80.20Z" => "80.20Z - Activités liées aux systèmes de sécurité",
            "80.30Z" => "80.30Z - Activités d'enquête",
            "81.10Z" => "81.10Z - Activités combinées de soutien lié aux bâtiments",
            "81.21Z" => "81.21Z - Nettoyage courant des bâtiments",
            "81.22Z" => "81.22Z - Autres activités de nettoyage des bâtiments et nettoyage industriel",
            "81.29A" => "81.29A - Désinfection, désinsectisation, dératisation",
            "81.29B" => "81.29B - Autres activités de nettoyage n.c.a.",
            "81.30Z" => "81.30Z - Services d'aménagement paysager",
            "82.11Z" => "82.11Z - Services administratifs combinés de bureau",
            "82.19Z" => "82.19Z - Photocopie, préparation de documents et autres activités spécialisées de soutien de bureau",
            "82.20Z" => "82.20Z - Activités de centres d'appels",
            "82.30Z" => "82.30Z - Organisation de foires, salons professionnels et congrès",
            "82.91Z" => "82.91Z - Activités des agences de recouvrement de factures et des sociétés d'information financière sur la clientèle",
            "82.92Z" => "82.92Z - Activités de conditionnement",
            "82.99Z" => "82.99Z - Autres activités de soutien aux entreprises n.c.a.",
            "84.11Z" => "84.11Z - Administration publique générale",
            "84.12Z" => "84.12Z - Administration publique (tutelle) de la santé, de la formation, de la culture et des services sociaux, autre que sécurité sociale",
            "84.13Z" => "84.13Z - Administration publique (tutelle) des activités économiques",
            "84.21Z" => "84.21Z - Affaires étrangères",
            "84.22Z" => "84.22Z - Défense",
            "84.23Z" => "84.23Z - Justice",
            "84.24Z" => "84.24Z - Activités d'ordre public et de sécurité",
            "84.25Z" => "84.25Z - Services du feu et de secours",
            "84.30A" => "84.30A - Activités générales de sécurité sociale",
            "84.30B" => "84.30B - Gestion des retraites complémentaires",
            "84.30C" => "84.30C - Distribution sociale de revenus",
            "85.10Z" => "85.10Z - Enseignement pré-primaire",
            "85.20Z" => "85.20Z - Enseignement primaire",
            "85.31Z" => "85.31Z - Enseignement secondaire général",
            "85.32Z" => "85.32Z - Enseignement secondaire technique ou professionnel",
            "85.41Z" => "85.41Z - Enseignement post-secondaire non supérieur",
            "85.42Z" => "85.42Z - Enseignement supérieur",
            "85.51Z" => "85.51Z - Enseignement de disciplines sportives et d'activités de loisirs",
            "85.52Z" => "85.52Z - Enseignement culturel",
            "85.53Z" => "85.53Z - Enseignement de la conduite",
            "85.59A" => "85.59A - Formation continue d'adultes",
            "85.59B" => "85.59B - Autres enseignements",
            "85.60Z" => "85.60Z - Activités de soutien à l'enseignement",
            "86.10Z" => "86.10Z - Activités hospitalières",
            "86.21Z" => "86.21Z - Activité des médecins généralistes",
            "86.22A" => "86.22A - Activités de radiodiagnostic et de radiothérapie",
            "86.22B" => "86.22B - Activités chirurgicales",
            "86.22C" => "86.22C - Autres activités des médecins spécialistes",
            "86.23Z" => "86.23Z - Pratique dentaire",
            "86.90A" => "86.90A - Ambulances",
            "86.90B" => "86.90B - Laboratoires d'analyses médicales",
            "86.90C" => "86.90C - Centres de collecte et banques d'organes",
            "86.90D" => "86.90D - Activités des infirmiers et des sages-femmes",
            "86.90E" => "86.90E - Activités des professionnels de la rééducation, de l'appareillage et des pédicures-podologues",
            "86.90F" => "86.90F - Activités de santé humaine non classées ailleurs",
            "87.10A" => "87.10A - Hébergement médicalisé pour personnes âgées",
            "87.10B" => "87.10B - Hébergement médicalisé pour enfants handicapés",
            "87.10C" => "87.10C - Hébergement médicalisé pour adultes handicapés et autre hébergement médicalisé",
            "87.20A" => "87.20A - Hébergement social pour handicapés mentaux et malades mentaux",
            "87.20B" => "87.20B - Hébergement social pour toxicomanes",
            "87.30A" => "87.30A - Hébergement social pour personnes âgées",
            "87.30B" => "87.30B - Hébergement social pour handicapés physiques",
            "87.90A" => "87.90A - Hébergement social pour enfants en difficultés",
            "87.90B" => "87.90B - Hébergement social pour adultes et familles en difficultés et autre hébergement social",
            "88.10A" => "88.10A - Aide à domicile",
            "88.10B" => "88.10B - Accueil ou accompagnement sans hébergement d'adultes handicapés ou de personnes âgées",
            "88.10C" => "88.10C - Aide par le travail",
            "88.91A" => "88.91A - Accueil de jeunes enfants",
            "88.91B" => "88.91B - Accueil ou accompagnement sans hébergement d'enfants handicapés",
            "88.99A" => "88.99A - Autre accueil ou accompagnement sans hébergement d'enfants et d'adolescents",
            "88.99B" => "88.99B - Action sociale sans hébergement n.c.a.",
            "90.01Z" => "90.01Z - Arts du spectacle vivant",
            "90.02Z" => "90.02Z - Activités de soutien au spectacle vivant",
            "90.03A" => "90.03A - Création artistique relevant des arts plastiques",
            "90.03B" => "90.03B - Autre création artistique",
            "90.04Z" => "90.04Z - Gestion de salles de spectacles",
            "91.01Z" => "91.01Z - Gestion des bibliothèques et des archives",
            "91.02Z" => "91.02Z - Gestion des musées",
            "91.03Z" => "91.03Z - Gestion des sites et monuments historiques et des attractions touristiques similaires",
            "91.04Z" => "91.04Z - Gestion des jardins botaniques et zoologiques et des réserves naturelles",
            "92.00Z" => "92.00Z - Organisation de jeux de hasard et d'argent",
            "93.11Z" => "93.11Z - Gestion d'installations sportives",
            "93.12Z" => "93.12Z - Activités de clubs de sports",
            "93.13Z" => "93.13Z - Activités des centres de culture physique",
            "93.19Z" => "93.19Z - Autres activités liées au sport",
            "93.21Z" => "93.21Z - Activités des parcs d'attractions et parcs à thèmes",
            "93.29Z" => "93.29Z - Autres activités récréatives et de loisirs",
            "94.11Z" => "94.11Z - Activités des organisations patronales et consulaires",
            "94.12Z" => "94.12Z - Activités des organisations professionnelles",
            "94.20Z" => "94.20Z - Activités des syndicats de salariés",
            "94.91Z" => "94.91Z - Activités des organisations religieuses",
            "94.92Z" => "94.92Z - Activités des organisations politiques",
            "94.99Z" => "94.99Z - Autres organisations fonctionnant par adhésion volontaire",
            "95.11Z" => "95.11Z - Réparation d'ordinateurs et d'équipements périphériques",
            "95.12Z" => "95.12Z - Réparation d'équipements de communication",
            "95.21Z" => "95.21Z - Réparation de produits électroniques grand public",
            "95.22Z" => "95.22Z - Réparation d'appareils électroménagers et d'équipements pour la maison et le jardin",
            "95.23Z" => "95.23Z - Réparation de chaussures et d'articles en cuir",
            "95.24Z" => "95.24Z - Réparation de meubles et d'équipements du foyer",
            "95.25Z" => "95.25Z - Réparation d'articles d'horlogerie et de bijouterie",
            "95.29Z" => "95.29Z - Réparation d'autres biens personnels et domestiques",
            "96.01A" => "96.01A - Blanchisserie-teinturerie de gros",
            "96.01B" => "96.01B - Blanchisserie-teinturerie de détail",
            "96.02A" => "96.02A - Coiffure",
            "96.02B" => "96.02B - Soins de beauté",
            "96.03Z" => "96.03Z - Services funéraires",
            "96.04Z" => "96.04Z - Entretien corporel",
            "96.09Z" => "96.09Z - Autres services personnels n.c.a.",
            "97.00Z" => "97.00Z - Activités des ménages en tant qu'employeurs de personnel domestique",
            "98.10Z" => "98.10Z - Activités indifférenciées des ménages en tant que producteurs de biens pour usage propre",
            "98.20Z" => "98.20Z - Activités indifférenciées des ménages en tant que producteurs de services pour usage propre",
            "99.00Z" => "99.00Z - Activités des organisations et organismes extraterritoriaux",
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->history = json_encode($this->history);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->history = json_decode($this->history, true);

        parent::afterFind();
    }

    /**
     * Get sector activity list
     *
     * @author Vincent ESTEVES <vinz.neo@gmail.com>
     *
     * @return array
     */

    public static function getSectorActivityList()
    {

        $query = self::find()->where(['not', ['activitysector' => null]]);
        $query->orderBy(['id' => SORT_ASC]);

        $sectoractivitylist = $query->all();

        return $sectoractivitylist;
    }


    /**
     * Get region name with code (magic method)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string
     */
    public function getNameWithCode()
    {
        return $this->activitysector;
    }

    public function getUrl($viaLogin = true)
    {
        if ($viaLogin) {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/dataroom/user/login', 'goToRoomID' => $this->roomID]);
        } else {
            return Yii::$app->urlManagerFrontend->createAbsoluteUrl(['dataroom/companies/view-room', 'id' => $this->id]);
        }
    }

    public function getUrlBackend()
    {
        return Yii::$app->urlManagerBackend->createAbsoluteUrl(['/dataroom/companies/room/update/', 'id' => $this->id]);
    }

    public function getPlace()
    {
        // return $this->region;

        $place = '';

        if ($this->city) {
            $place = $this->city;
        }

        if ($this->zip) {
            $place = $place ? $place . ' (' . $this->zip . ')' : $this->zip;
        }

        return $place;
    }
}
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
                $this->addError('refNumber1', Yii::t('admin', "Report n??1 must be less than Report n??2."));
            }

            if ($this->refNumber0 >= $this->refNumber1) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n??1."));
            }
        } elseif ($this->refNumber1) {
            if ($this->refNumber0 >= $this->refNumber1) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n??1."));
            }
        } elseif ($this->refNumber2) {
            if ($this->refNumber0 >= $this->refNumber2) {
                $this->addError('refNumber0', Yii::t('admin', "Date limite must be less than Report n??2."));
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
            'ca' => 'Engagement de confidentialit??',
            'activity' => 'Activit??',
            'activitysector' => 'Secteur d\'Activit??',
            'region' => 'D??partement',
            // 'website' => 'Site Internet',
            'address' => 'Adresse',
            'zip' => 'Code postal',
            'city' => 'Ville',
            // 'desc' => "Description de l'entreprise",
            // 'desc2' => "Description du si??ge de l'entreprise",
            'siren' => 'Num??ro SIREN',
            'codeNaf' => 'Code NAF (ex APE)',
            'legalStatus' => 'Statut juridique',
            // 'status' => 'Statuts',
            // 'kbis' => 'KBis',
            // 'balanceSheet' => 'Dernier bilan',
            'incomeStatement' => 'Dernier compte de r??sultat',
            'managementBalance' => 'Dernier Solde Interm??diaire de Gestion',
            'taxPackage' => 'Derni??re liasse fiscale',
            'history' => 'History',
            // 'concurrence' => 'Concurrence',
            'backlog' => 'Carnet de commandes',
            'principalClients' => 'Principaux clients',
            'annualTurnover' => 'CA annuel',
            'vehicles' => 'Description des v??hicules',
            'premises' => 'Description des locaux',
            'baux' => 'Baux',
            'inventory' => 'Inventaire du commissaire priseur',
            'assets' => 'Actifs remarquables',
            'patents' => 'Concessions, droits, brevets',
            'contributors' => 'Effectif',
            'employmentContract' => 'Contrat de travail type',
            'employeesList' => 'Liste des salari??s',
            'procedureRules' => 'R??glement int??rieur',
            'rtt' => 'Accord sur la RTT',
            'worksCouncilReport' => "Dernier rapport du comit?? d'entreprise",
            'procedureNature' => 'Nature de la proc??dure',
            'designationDate' => 'Date d\'ouverture de la proc??dure',
            'procedureContact' => 'Contact AJA pour la proc??dure',
            'companyContact' => 'Email du contact au sein de la soci??t??',
            'hearingDate' => "Date de prochaine audience d'analyse des offres",
            'refNumber0' => 'Date limite de d??p??t des offres',
            'refNumber1' => 'Report n??1 de la date limite de d??p??t des offres au',
            'refNumber2' => 'Report n??2 de la date limite de d??p??t des offres au',
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
            'A??ronautique' => 'A??ronautique',
            'Agroalimentaire / Agriculture' => 'Agroalimentaire / Agriculture',
            'Automobile' => 'Automobile',
            'Banque / Assurance / Finance' => 'Banque / Assurance / Finance',
            'B??timent / BTP / Construction' => 'B??timent / BTP / Construction',
            'Bois / Papier / Carton / Imprimerie' => 'Bois / Papier / Carton / Imprimerie',
            'Chimie / Parachimie' => 'Chimie / Parachimie',
            'Commerce / N??goce / Distribution' => 'Commerce / N??goce / Distribution',
            'Edition / Communication / Multim??dia' => 'Edition / Communication / Multim??dia',
            'Electronique / Electricit??' => 'Electronique / Electricit??',
            'H??tellerie' => 'H??tellerie',
            'Informatique / T??l??coms' => 'Informatique / T??l??coms',
            'M??canique' => 'M??canique',
            'M??dical / Pharmaceutique' => 'M??dical / Pharmaceutique',
            'M??tallurgie / Sid??rurgie' => 'M??tallurgie / Sid??rurgie',
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
            "01.11Z" => "01.11Z - Culture de c??r??ales (?? l'exception du riz), de l??gumineuses et de graines ol??agineuses",
            "01.12Z" => "01.12Z - Culture du riz",
            "01.13Z" => "01.13Z - Culture de l??gumes, de melons, de racines et de tubercules",
            "01.14Z" => "01.14Z - Culture de la canne ?? sucre",
            "01.15Z" => "01.15Z - Culture du tabac",
            "01.16Z" => "01.16Z - Culture de plantes ?? fibres",
            "01.19Z" => "01.19Z - Autres cultures non permanentes",
            "01.21Z" => "01.21Z - Culture de la vigne",
            "01.22Z" => "01.22Z - Culture de fruits tropicaux et subtropicaux",
            "01.23Z" => "01.23Z - Culture d'agrumes",
            "01.24Z" => "01.24Z - Culture de fruits ?? p??pins et ?? noyau",
            "01.25Z" => "01.25Z - Culture d'autres fruits d'arbres ou d'arbustes et de fruits ?? coque",
            "01.26Z" => "01.26Z - Culture de fruits ol??agineux",
            "01.27Z" => "01.27Z - Culture de plantes ?? boissons",
            "01.28Z" => "01.28Z - Culture de plantes ?? ??pices, aromatiques, m??dicinales et pharmaceutiques",
            "01.29Z" => "01.29Z - Autres cultures permanentes",
            "01.30Z" => "01.30Z - Reproduction de plantes",
            "01.41Z" => "01.41Z - ??levage de vaches laiti??res",
            "01.42Z" => "01.42Z - ??levage d'autres bovins et de buffles",
            "01.43Z" => "01.43Z - ??levage de chevaux et d'autres ??quid??s",
            "01.44Z" => "01.44Z - ??levage de chameaux et d'autres cam??lid??s",
            "01.45Z" => "01.45Z - ??levage d'ovins et de caprins",
            "01.46Z" => "01.46Z - ??levage de porcins",
            "01.47Z" => "01.47Z - ??levage de volailles",
            "01.49Z" => "01.49Z - ??levage d'autres animaux",
            "01.50Z" => "01.50Z - Culture et ??levage associ??s",
            "01.61Z" => "01.61Z - Activit??s de soutien aux cultures",
            "01.62Z" => "01.62Z - Activit??s de soutien ?? la production animale",
            "01.63Z" => "01.63Z - Traitement primaire des r??coltes",
            "01.64Z" => "01.64Z - Traitement des semences",
            "01.70Z" => "01.70Z - Chasse, pi??geage et services annexes",
            "02.10Z" => "02.10Z - Sylviculture et autres activit??s foresti??res",
            "02.20Z" => "02.20Z - Exploitation foresti??re",
            "02.30Z" => "02.30Z - R??colte de produits forestiers non ligneux poussant ?? l'??tat sauvage",
            "02.40Z" => "02.40Z - Services de soutien ?? l'exploitation foresti??re",
            "03.11Z" => "03.11Z - P??che en mer",
            "03.12Z" => "03.12Z - P??che en eau douce",
            "03.21Z" => "03.21Z - Aquaculture en mer",
            "03.22Z" => "03.22Z - Aquaculture en eau douce",
            "05.10Z" => "05.10Z - Extraction de houille",
            "05.20Z" => "05.20Z - Extraction de lignite",
            "06.10Z" => "06.10Z - Extraction de p??trole brut",
            "06.20Z" => "06.20Z - Extraction de gaz naturel",
            "07.10Z" => "07.10Z - Extraction de minerais de fer",
            "07.21Z" => "07.21Z - Extraction de minerais d'uranium et de thorium",
            "07.29Z" => "07.29Z - Extraction d'autres minerais de m??taux non ferreux",
            "08.11Z" => "08.11Z - Extraction de pierres ornementales et de construction, de calcaire industriel, de gypse, de craie et d'ardoise",
            "08.12Z" => "08.12Z - Exploitation de gravi??res et sabli??res, extraction d'argiles et de kaolin",
            "08.91Z" => "08.91Z - Extraction des min??raux chimiques et d'engrais min??raux",
            "08.92Z" => "08.92Z - Extraction de tourbe",
            "08.93Z" => "08.93Z - Production de sel",
            "08.99Z" => "08.99Z - Autres activit??s extractives n.c.a.",
            "09.10Z" => "09.10Z - Activit??s de soutien ?? l'extraction d'hydrocarbures",
            "09.90Z" => "09.90Z - Activit??s de soutien aux autres industries extractives",
            "10.11Z" => "10.11Z - Transformation et conservation de la viande de boucherie",
            "10.12Z" => "10.12Z - Transformation et conservation de la viande de volaille",
            "10.13A" => "10.13A - Pr??paration industrielle de produits ?? base de viande",
            "10.13B" => "10.13B - Charcuterie",
            "10.20Z" => "10.20Z - Transformation et conservation de poisson, de crustac??s et de mollusques",
            "10.31Z" => "10.31Z - Transformation et conservation de pommes de terre",
            "10.32Z" => "10.32Z - Pr??paration de jus de fruits et l??gumes",
            "10.39A" => "10.39A - Autre transformation et conservation de l??gumes",
            "10.39B" => "10.39B - Transformation et conservation de fruits",
            "10.41A" => "10.41A - Fabrication d'huiles et graisses brutes",
            "10.41B" => "10.41B - Fabrication d'huiles et graisses raffin??es",
            "10.42Z" => "10.42Z - Fabrication de margarine et graisses comestibles similaires",
            "10.51A" => "10.51A - Fabrication de lait liquide et de produits frais",
            "10.51B" => "10.51B - Fabrication de beurre",
            "10.51C" => "10.51C - Fabrication de fromage",
            "10.51D" => "10.51D - Fabrication d'autres produits laitiers",
            "10.52Z" => "10.52Z - Fabrication de glaces et sorbets",
            "10.61A" => "10.61A - Meunerie",
            "10.61B" => "10.61B - Autres activit??s du travail des grains",
            "10.62Z" => "10.62Z - Fabrication de produits amylac??s",
            "10.71A" => "10.71A - Fabrication industrielle de pain et de p??tisserie fra??che",
            "10.71B" => "10.71B - Cuisson de produits de boulangerie",
            "10.71C" => "10.71C - Boulangerie et boulangerie-p??tisserie",
            "10.71D" => "10.71D - P??tisserie",
            "10.72Z" => "10.72Z - Fabrication de biscuits, biscottes et p??tisseries de conservation",
            "10.73Z" => "10.73Z - Fabrication de p??tes alimentaires",
            "10.81Z" => "10.81Z - Fabrication de sucre",
            "10.82Z" => "10.82Z - Fabrication de cacao, chocolat et de produits de confiserie",
            "10.83Z" => "10.83Z - Transformation du th?? et du caf??",
            "10.84Z" => "10.84Z - Fabrication de condiments et assaisonnements",
            "10.85Z" => "10.85Z - Fabrication de plats pr??par??s",
            "10.86Z" => "10.86Z - Fabrication d'aliments homog??n??is??s et di??t??tiques",
            "10.89Z" => "10.89Z - Fabrication d'autres produits alimentaires n.c.a.",
            "10.91Z" => "10.91Z - Fabrication d'aliments pour animaux de ferme",
            "10.92Z" => "10.92Z - Fabrication d'aliments pour animaux de compagnie",
            "11.01Z" => "11.01Z - Production de boissons alcooliques distill??es",
            "11.02A" => "11.02A - Fabrication de vins effervescents",
            "11.02B" => "11.02B - Vinification",
            "11.03Z" => "11.03Z - Fabrication de cidre et de vins de fruits",
            "11.04Z" => "11.04Z - Production d'autres boissons ferment??es non distill??es",
            "11.05Z" => "11.05Z - Fabrication de bi??re",
            "11.06Z" => "11.06Z - Fabrication de malt",
            "11.07A" => "11.07A - Industrie des eaux de table",
            "11.07B" => "11.07B - Production de boissons rafra??chissantes",
            "12.00Z" => "12.00Z - Fabrication de produits ?? base de tabac",
            "13.10Z" => "13.10Z - Pr??paration de fibres textiles et filature",
            "13.20Z" => "13.20Z - Tissage",
            "13.30Z" => "13.30Z - Ennoblissement textile",
            "13.91Z" => "13.91Z - Fabrication d'??toffes ?? mailles",
            "13.92Z" => "13.92Z - Fabrication d'articles textiles, sauf habillement",
            "13.93Z" => "13.93Z - Fabrication de tapis et moquettes",
            "13.94Z" => "13.94Z - Fabrication de ficelles, cordes et filets",
            "13.95Z" => "13.95Z - Fabrication de non-tiss??s, sauf habillement",
            "13.96Z" => "13.96Z - Fabrication d'autres textiles techniques et industriels",
            "13.99Z" => "13.99Z - Fabrication d'autres textiles n.c.a.",
            "14.11Z" => "14.11Z - Fabrication de v??tements en cuir",
            "14.12Z" => "14.12Z - Fabrication de v??tements de travail",
            "14.13Z" => "14.13Z - Fabrication de v??tements de dessus",
            "14.14Z" => "14.14Z - Fabrication de v??tements de dessous",
            "14.19Z" => "14.19Z - Fabrication d'autres v??tements et accessoires",
            "14.20Z" => "14.20Z - Fabrication d'articles en fourrure",
            "14.31Z" => "14.31Z - Fabrication d'articles chaussants ?? mailles",
            "14.39Z" => "14.39Z - Fabrication d'autres articles ?? mailles",
            "15.11Z" => "15.11Z - Appr??t et tannage des cuirs ; pr??paration et teinture des fourrures",
            "15.12Z" => "15.12Z - Fabrication d'articles de voyage, de maroquinerie et de sellerie",
            "15.20Z" => "15.20Z - Fabrication de chaussures",
            "16.10A" => "16.10A - Sciage et rabotage du bois, hors impr??gnation",
            "16.10B" => "16.10B - Impr??gnation du bois",
            "16.21Z" => "16.21Z - Fabrication de placage et de panneaux de bois",
            "16.22Z" => "16.22Z - Fabrication de parquets assembl??s",
            "16.23Z" => "16.23Z - Fabrication de charpentes et d'autres menuiseries",
            "16.24Z" => "16.24Z - Fabrication d'emballages en bois",
            "16.29Z" => "16.29Z - Fabrication d'objets divers en bois ; fabrication d'objets en li??ge, vannerie et sparterie",
            "17.11Z" => "17.11Z - Fabrication de p??te ?? papier",
            "17.12Z" => "17.12Z - Fabrication de papier et de carton",
            "17.21A" => "17.21A - Fabrication de carton ondul??",
            "17.21B" => "17.21B - Fabrication de cartonnages",
            "17.21C" => "17.21C - Fabrication d'emballages en papier",
            "17.22Z" => "17.22Z - Fabrication d'articles en papier ?? usage sanitaire ou domestique",
            "17.23Z" => "17.23Z - Fabrication d'articles de papeterie",
            "17.24Z" => "17.24Z - Fabrication de papiers peints",
            "17.29Z" => "17.29Z - Fabrication d'autres articles en papier ou en carton",
            "18.11Z" => "18.11Z - Imprimerie de journaux",
            "18.12Z" => "18.12Z - Autre imprimerie (labeur)",
            "18.13Z" => "18.13Z - Activit??s de pr??-presse",
            "18.14Z" => "18.14Z - Reliure et activit??s connexes",
            "18.20Z" => "18.20Z - Reproduction d'enregistrements",
            "19.10Z" => "19.10Z - Cok??faction",
            "19.20Z" => "19.20Z - Raffinage du p??trole",
            "20.11Z" => "20.11Z - Fabrication de gaz industriels",
            "20.12Z" => "20.12Z - Fabrication de colorants et de pigments",
            "20.13A" => "20.13A - Enrichissement et retraitement de mati??res nucl??aires",
            "20.13B" => "20.13B - Fabrication d'autres produits chimiques inorganiques de base n.c.a.",
            "20.14Z" => "20.14Z - Fabrication d'autres produits chimiques organiques de base",
            "20.15Z" => "20.15Z - Fabrication de produits azot??s et d'engrais",
            "20.16Z" => "20.16Z - Fabrication de mati??res plastiques de base",
            "20.17Z" => "20.17Z - Fabrication de caoutchouc synth??tique",
            "20.20Z" => "20.20Z - Fabrication de pesticides et d'autres produits agrochimiques",
            "20.30Z" => "20.30Z - Fabrication de peintures, vernis, encres et mastics",
            "20.41Z" => "20.41Z - Fabrication de savons, d??tergents et produits d'entretien",
            "20.42Z" => "20.42Z - Fabrication de parfums et de produits pour la toilette",
            "20.51Z" => "20.51Z - Fabrication de produits explosifs",
            "20.52Z" => "20.52Z - Fabrication de colles",
            "20.53Z" => "20.53Z - Fabrication d'huiles essentielles",
            "20.59Z" => "20.59Z - Fabrication d'autres produits chimiques n.c.a.",
            "20.60Z" => "20.60Z - Fabrication de fibres artificielles ou synth??tiques",
            "21.10Z" => "21.10Z - Fabrication de produits pharmaceutiques de base",
            "21.20Z" => "21.20Z - Fabrication de pr??parations pharmaceutiques",
            "22.11Z" => "22.11Z - Fabrication et rechapage de pneumatiques",
            "22.19Z" => "22.19Z - Fabrication d'autres articles en caoutchouc",
            "22.21Z" => "22.21Z - Fabrication de plaques, feuilles, tubes et profil??s en mati??res plastiques",
            "22.22Z" => "22.22Z - Fabrication d'emballages en mati??res plastiques",
            "22.23Z" => "22.23Z - Fabrication d'??l??ments en mati??res plastiques pour la construction",
            "22.29A" => "22.29A - Fabrication de pi??ces techniques ?? base de mati??res plastiques",
            "22.29B" => "22.29B - Fabrication de produits de consommation courante en mati??res plastiques",
            "23.11Z" => "23.11Z - Fabrication de verre plat",
            "23.12Z" => "23.12Z - Fa??onnage et transformation du verre plat",
            "23.13Z" => "23.13Z - Fabrication de verre creux",
            "23.14Z" => "23.14Z - Fabrication de fibres de verre",
            "23.19Z" => "23.19Z - Fabrication et fa??onnage d'autres articles en verre, y compris verre technique",
            "23.20Z" => "23.20Z - Fabrication de produits r??fractaires",
            "23.31Z" => "23.31Z - Fabrication de carreaux en c??ramique",
            "23.32Z" => "23.32Z - Fabrication de briques, tuiles et produits de construction, en terre cuite",
            "23.41Z" => "23.41Z - Fabrication d'articles c??ramiques ?? usage domestique ou ornemental",
            "23.42Z" => "23.42Z - Fabrication d'appareils sanitaires en c??ramique",
            "23.43Z" => "23.43Z - Fabrication d'isolateurs et pi??ces isolantes en c??ramique",
            "23.44Z" => "23.44Z - Fabrication d'autres produits c??ramiques ?? usage technique",
            "23.49Z" => "23.49Z - Fabrication d'autres produits c??ramiques",
            "23.51Z" => "23.51Z - Fabrication de ciment",
            "23.52Z" => "23.52Z - Fabrication de chaux et pl??tre",
            "23.61Z" => "23.61Z - Fabrication d'??l??ments en b??ton pour la construction",
            "23.62Z" => "23.62Z - Fabrication d'??l??ments en pl??tre pour la construction",
            "23.63Z" => "23.63Z - Fabrication de b??ton pr??t ?? l'emploi",
            "23.64Z" => "23.64Z - Fabrication de mortiers et b??tons secs",
            "23.65Z" => "23.65Z - Fabrication d'ouvrages en fibre-ciment",
            "23.69Z" => "23.69Z - Fabrication d'autres ouvrages en b??ton, en ciment ou en pl??tre",
            "23.70Z" => "23.70Z - Taille, fa??onnage et finissage de pierres",
            "23.91Z" => "23.91Z - Fabrication de produits abrasifs",
            "23.99Z" => "23.99Z - Fabrication d'autres produits min??raux non m??talliques n.c.a.",
            "24.10Z" => "24.10Z - Sid??rurgie",
            "24.20Z" => "24.20Z - Fabrication de tubes, tuyaux, profil??s creux et accessoires correspondants en acier",
            "24.31Z" => "24.31Z - ??tirage ?? froid de barres",
            "24.32Z" => "24.32Z - Laminage ?? froid de feuillards",
            "24.33Z" => "24.33Z - Profilage ?? froid par formage ou pliage",
            "24.34Z" => "24.34Z - Tr??filage ?? froid",
            "24.41Z" => "24.41Z - Production de m??taux pr??cieux",
            "24.42Z" => "24.42Z - M??tallurgie de l'aluminium",
            "24.43Z" => "24.43Z - M??tallurgie du plomb, du zinc ou de l'??tain",
            "24.44Z" => "24.44Z - M??tallurgie du cuivre",
            "24.45Z" => "24.45Z - M??tallurgie des autres m??taux non ferreux",
            "24.46Z" => "24.46Z - ??laboration et transformation de mati??res nucl??aires",
            "24.51Z" => "24.51Z - Fonderie de fonte",
            "24.52Z" => "24.52Z - Fonderie d'acier",
            "24.53Z" => "24.53Z - Fonderie de m??taux l??gers",
            "24.54Z" => "24.54Z - Fonderie d'autres m??taux non ferreux",
            "25.11Z" => "25.11Z - Fabrication de structures m??talliques et de parties de structures",
            "25.12Z" => "25.12Z - Fabrication de portes et fen??tres en m??tal",
            "25.21Z" => "25.21Z - Fabrication de radiateurs et de chaudi??res pour le chauffage central",
            "25.29Z" => "25.29Z - Fabrication d'autres r??servoirs, citernes et conteneurs m??talliques",
            "25.30Z" => "25.30Z - Fabrication de g??n??rateurs de vapeur, ?? l'exception des chaudi??res pour le chauffage central",
            "25.40Z" => "25.40Z - Fabrication d'armes et de munitions",
            "25.50A" => "25.50A - Forge, estampage, matri??age ; m??tallurgie des poudres",
            "25.50B" => "25.50B - D??coupage, emboutissage",
            "25.61Z" => "25.61Z - Traitement et rev??tement des m??taux",
            "25.62A" => "25.62A - D??colletage",
            "25.62B" => "25.62B - M??canique industrielle",
            "25.71Z" => "25.71Z - Fabrication de coutellerie",
            "25.72Z" => "25.72Z - Fabrication de serrures et de ferrures",
            "25.73A" => "25.73A - Fabrication de moules et mod??les",
            "25.73B" => "25.73B - Fabrication d'autres outillages",
            "25.91Z" => "25.91Z - Fabrication de f??ts et emballages m??talliques similaires",
            "25.92Z" => "25.92Z - Fabrication d'emballages m??talliques l??gers",
            "25.93Z" => "25.93Z - Fabrication d'articles en fils m??talliques, de cha??nes et de ressorts",
            "25.94Z" => "25.94Z - Fabrication de vis et de boulons",
            "25.99A" => "25.99A - Fabrication d'articles m??talliques m??nagers",
            "25.99B" => "25.99B - Fabrication d'autres articles m??talliques",
            "26.11Z" => "26.11Z - Fabrication de composants ??lectroniques",
            "26.12Z" => "26.12Z - Fabrication de cartes ??lectroniques assembl??es",
            "26.20Z" => "26.20Z - Fabrication d'ordinateurs et d'??quipements p??riph??riques",
            "26.30Z" => "26.30Z - Fabrication d'??quipements de communication",
            "26.40Z" => "26.40Z - Fabrication de produits ??lectroniques grand public",
            "26.51A" => "26.51A - Fabrication d'??quipements d'aide ?? la navigation",
            "26.51B" => "26.51B - Fabrication d'instrumentation scientifique et technique",
            "26.52Z" => "26.52Z - Horlogerie",
            "26.60Z" => "26.60Z - Fabrication d'??quipements d'irradiation m??dicale, d'??quipements ??lectrom??dicaux et ??lectroth??rapeutiques",
            "26.70Z" => "26.70Z - Fabrication de mat??riels optique et photographique",
            "26.80Z" => "26.80Z - Fabrication de supports magn??tiques et optiques",
            "27.11Z" => "27.11Z - Fabrication de moteurs, g??n??ratrices et transformateurs ??lectriques",
            "27.12Z" => "27.12Z - Fabrication de mat??riel de distribution et de commande ??lectrique",
            "27.20Z" => "27.20Z - Fabrication de piles et d'accumulateurs ??lectriques",
            "27.31Z" => "27.31Z - Fabrication de c??bles de fibres optiques",
            "27.32Z" => "27.32Z - Fabrication d'autres fils et c??bles ??lectroniques ou ??lectriques",
            "27.33Z" => "27.33Z - Fabrication de mat??riel d'installation ??lectrique",
            "27.40Z" => "27.40Z - Fabrication d'appareils d'??clairage ??lectrique",
            "27.51Z" => "27.51Z - Fabrication d'appareils ??lectrom??nagers",
            "27.52Z" => "27.52Z - Fabrication d'appareils m??nagers non ??lectriques",
            "27.90Z" => "27.90Z - Fabrication d'autres mat??riels ??lectriques",
            "28.11Z" => "28.11Z - Fabrication de moteurs et turbines, ?? l'exception des moteurs d'avions et de v??hicules",
            "28.12Z" => "28.12Z - Fabrication d'??quipements hydrauliques et pneumatiques",
            "28.13Z" => "28.13Z - Fabrication d'autres pompes et compresseurs",
            "28.14Z" => "28.14Z - Fabrication d'autres articles de robinetterie",
            "28.15Z" => "28.15Z - Fabrication d'engrenages et d'organes m??caniques de transmission",
            "28.21Z" => "28.21Z - Fabrication de fours et br??leurs",
            "28.22Z" => "28.22Z - Fabrication de mat??riel de levage et de manutention",
            "28.23Z" => "28.23Z - Fabrication de machines et d'??quipements de bureau (?? l'exception des ordinateurs et ??quipements p??riph??riques)",
            "28.24Z" => "28.24Z - Fabrication d'outillage portatif ?? moteur incorpor??",
            "28.25Z" => "28.25Z - Fabrication d'??quipements a??rauliques et frigorifiques industriels",
            "28.29A" => "28.29A - Fabrication d'??quipements d'emballage, de conditionnement et de pesage",
            "28.29B" => "28.29B - Fabrication d'autres machines d'usage g??n??ral",
            "28.30Z" => "28.30Z - Fabrication de machines agricoles et foresti??res",
            "28.41Z" => "28.41Z - Fabrication de machines-outils pour le travail des m??taux",
            "28.49Z" => "28.49Z - Fabrication d'autres machines-outils",
            "28.91Z" => "28.91Z - Fabrication de machines pour la m??tallurgie",
            "28.92Z" => "28.92Z - Fabrication de machines pour l'extraction ou la construction",
            "28.93Z" => "28.93Z - Fabrication de machines pour l'industrie agro-alimentaire",
            "28.94Z" => "28.94Z - Fabrication de machines pour les industries textiles",
            "28.95Z" => "28.95Z - Fabrication de machines pour les industries du papier et du carton",
            "28.96Z" => "28.96Z - Fabrication de machines pour le travail du caoutchouc ou des plastiques",
            "28.99A" => "28.99A - Fabrication de machines d'imprimerie",
            "28.99B" => "28.99B - Fabrication d'autres machines sp??cialis??es",
            "29.10Z" => "29.10Z - Construction de v??hicules automobiles",
            "29.20Z" => "29.20Z - Fabrication de carrosseries et remorques",
            "29.31Z" => "29.31Z - Fabrication d'??quipements ??lectriques et ??lectroniques automobiles",
            "29.32Z" => "29.32Z - Fabrication d'autres ??quipements automobiles",
            "30.11Z" => "30.11Z - Construction de navires et de structures flottantes",
            "30.12Z" => "30.12Z - Construction de bateaux de plaisance",
            "30.20Z" => "30.20Z - Construction de locomotives et d'autre mat??riel ferroviaire roulant",
            "30.30Z" => "30.30Z - Construction a??ronautique et spatiale",
            "30.40Z" => "30.40Z - Construction de v??hicules militaires de combat",
            "30.91Z" => "30.91Z - Fabrication de motocycles",
            "30.92Z" => "30.92Z - Fabrication de bicyclettes et de v??hicules pour invalides",
            "30.99Z" => "30.99Z - Fabrication d'autres ??quipements de transport n.c.a.",
            "31.01Z" => "31.01Z - Fabrication de meubles de bureau et de magasin",
            "31.02Z" => "31.02Z - Fabrication de meubles de cuisine",
            "31.03Z" => "31.03Z - Fabrication de matelas",
            "31.09A" => "31.09A - Fabrication de si??ges d'ameublement d'int??rieur",
            "31.09B" => "31.09B - Fabrication d'autres meubles et industries connexes de l'ameublement",
            "32.11Z" => "32.11Z - Frappe de monnaie",
            "32.12Z" => "32.12Z - Fabrication d'articles de joaillerie et bijouterie",
            "32.13Z" => "32.13Z - Fabrication d'articles de bijouterie fantaisie et articles similaires",
            "32.20Z" => "32.20Z - Fabrication d'instruments de musique",
            "32.30Z" => "32.30Z - Fabrication d'articles de sport",
            "32.40Z" => "32.40Z - Fabrication de jeux et jouets",
            "32.50A" => "32.50A - Fabrication de mat??riel m??dico-chirurgical et dentaire",
            "32.50B" => "32.50B - Fabrication de lunettes",
            "32.91Z" => "32.91Z - Fabrication d'articles de brosserie",
            "32.99Z" => "32.99Z - Autres activit??s manufacturi??res n.c.a.",
            "33.11Z" => "33.11Z - R??paration d'ouvrages en m??taux",
            "33.12Z" => "33.12Z - R??paration de machines et ??quipements m??caniques",
            "33.13Z" => "33.13Z - R??paration de mat??riels ??lectroniques et optiques",
            "33.14Z" => "33.14Z - R??paration d'??quipements ??lectriques",
            "33.15Z" => "33.15Z - R??paration et maintenance navale",
            "33.16Z" => "33.16Z - R??paration et maintenance d'a??ronefs et d'engins spatiaux",
            "33.17Z" => "33.17Z - R??paration et maintenance d'autres ??quipements de transport",
            "33.19Z" => "33.19Z - R??paration d'autres ??quipements",
            "33.20A" => "33.20A - Installation de structures m??talliques, chaudronn??es et de tuyauterie",
            "33.20B" => "33.20B - Installation de machines et ??quipements m??caniques",
            "33.20C" => "33.20C - Conception d'ensemble et assemblage sur site industriel d'??quipements de contr??le des processus industriels",
            "33.20D" => "33.20D - Installation d'??quipements ??lectriques, de mat??riels ??lectroniques et optiques ou d'autres mat??riels",
            "35.11Z" => "35.11Z - Production d'??lectricit??",
            "35.12Z" => "35.12Z - Transport d'??lectricit??",
            "35.13Z" => "35.13Z - Distribution d'??lectricit??",
            "35.14Z" => "35.14Z - Commerce d'??lectricit??",
            "35.21Z" => "35.21Z - Production de combustibles gazeux",
            "35.22Z" => "35.22Z - Distribution de combustibles gazeux par conduites",
            "35.23Z" => "35.23Z - Commerce de combustibles gazeux par conduites",
            "35.30Z" => "35.30Z - Production et distribution de vapeur et d'air conditionn??",
            "36.00Z" => "36.00Z - Captage, traitement et distribution d'eau",
            "37.00Z" => "37.00Z - Collecte et traitement des eaux us??es",
            "38.11Z" => "38.11Z - Collecte des d??chets non dangereux",
            "38.12Z" => "38.12Z - Collecte des d??chets dangereux",
            "38.21Z" => "38.21Z - Traitement et ??limination des d??chets non dangereux",
            "38.22Z" => "38.22Z - Traitement et ??limination des d??chets dangereux",
            "38.31Z" => "38.31Z - D??mant??lement d'??paves",
            "38.32Z" => "38.32Z - R??cup??ration de d??chets tri??s",
            "39.00Z" => "39.00Z - D??pollution et autres services de gestion des d??chets",
            "41.10A" => "41.10A - Promotion immobili??re de logements",
            "41.10B" => "41.10B - Promotion immobili??re de bureaux",
            "41.10C" => "41.10C - Promotion immobili??re d'autres b??timents",
            "41.10D" => "41.10D - Supports juridiques de programmes",
            "41.20A" => "41.20A - Construction de maisons individuelles",
            "41.20B" => "41.20B - Construction d'autres b??timents",
            "42.11Z" => "42.11Z - Construction de routes et autoroutes",
            "42.12Z" => "42.12Z - Construction de voies ferr??es de surface et souterraines",
            "42.13A" => "42.13A - Construction d'ouvrages d'art",
            "42.13B" => "42.13B - Construction et entretien de tunnels",
            "42.21Z" => "42.21Z - Construction de r??seaux pour fluides",
            "42.22Z" => "42.22Z - Construction de r??seaux ??lectriques et de t??l??communications",
            "42.91Z" => "42.91Z - Construction d'ouvrages maritimes et fluviaux",
            "42.99Z" => "42.99Z - Construction d'autres ouvrages de g??nie civil n.c.a.",
            "43.11Z" => "43.11Z - Travaux de d??molition",
            "43.12A" => "43.12A - Travaux de terrassement courants et travaux pr??paratoires",
            "43.12B" => "43.12B - Travaux de terrassement sp??cialis??s ou de grande masse",
            "43.13Z" => "43.13Z - Forages et sondages",
            "43.21A" => "43.21A - Travaux d'installation ??lectrique dans tous locaux",
            "43.21B" => "43.21B - Travaux d'installation ??lectrique sur la voie publique",
            "43.22A" => "43.22A - Travaux d'installation d'eau et de gaz en tous locaux",
            "43.22B" => "43.22B - Travaux d'installation d'??quipements thermiques et de climatisation",
            "43.29A" => "43.29A - Travaux d'isolation",
            "43.29B" => "43.29B - Autres travaux d'installation n.c.a.",
            "43.31Z" => "43.31Z - Travaux de pl??trerie",
            "43.32A" => "43.32A - Travaux de menuiserie bois et PVC",
            "43.32B" => "43.32B - Travaux de menuiserie m??tallique et serrurerie",
            "43.32C" => "43.32C - Agencement de lieux de vente",
            "43.33Z" => "43.33Z - Travaux de rev??tement des sols et des murs",
            "43.34Z" => "43.34Z - Travaux de peinture et vitrerie",
            "43.39Z" => "43.39Z - Autres travaux de finition",
            "43.91A" => "43.91A - Travaux de charpente",
            "43.91B" => "43.91B - Travaux de couverture par ??l??ments",
            "43.99A" => "43.99A - Travaux d'??tanch??ification",
            "43.99B" => "43.99B - Travaux de montage de structures m??talliques",
            "43.99C" => "43.99C - Travaux de ma??onnerie g??n??rale et gros ??uvre de b??timent",
            "43.99D" => "43.99D - Autres travaux sp??cialis??s de construction",
            "43.99E" => "43.99E - Location avec op??rateur de mat??riel de construction",
            "45.11Z" => "45.11Z - Commerce de voitures et de v??hicules automobiles l??gers",
            "45.19Z" => "45.19Z - Commerce d'autres v??hicules automobiles",
            "45.20A" => "45.20A - Entretien et r??paration de v??hicules automobiles l??gers",
            "45.20B" => "45.20B - Entretien et r??paration d'autres v??hicules automobiles",
            "45.31Z" => "45.31Z - Commerce de gros d'??quipements automobiles",
            "45.32Z" => "45.32Z - Commerce de d??tail d'??quipements automobiles",
            "45.40Z" => "45.40Z - Commerce et r??paration de motocycles",
            "46.11Z" => "46.11Z - Interm??diaires du commerce en mati??res premi??res agricoles, animaux vivants, mati??res premi??res textiles et produits semi-finis",
            "46.12A" => "46.12A - Centrales d'achat de carburant",
            "46.12B" => "46.12B - Autres interm??diaires du commerce en combustibles, m??taux, min??raux et produits chimiques",
            "46.13Z" => "46.13Z - Interm??diaires du commerce en bois et mat??riaux de construction",
            "46.14Z" => "46.14Z - Interm??diaires du commerce en machines, ??quipements industriels, navires et avions",
            "46.15Z" => "46.15Z - Interm??diaires du commerce en meubles, articles de m??nage et quincaillerie",
            "46.16Z" => "46.16Z - Interm??diaires du commerce en textiles, habillement, fourrures, chaussures et articles en cuir",
            "46.17A" => "46.17A - Centrales d'achat alimentaires",
            "46.17B" => "46.17B - Autres interm??diaires du commerce en denr??es, boissons et tabac",
            "46.18Z" => "46.18Z - Interm??diaires sp??cialis??s dans le commerce d'autres produits sp??cifiques",
            "46.19A" => "46.19A - Centrales d'achat non alimentaires",
            "46.19B" => "46.19B - Autres interm??diaires du commerce en produits divers",
            "46.21Z" => "46.21Z - Commerce de gros (commerce interentreprises) de c??r??ales, de tabac non manufactur??, de semences et d'aliments pour le b??tail",
            "46.22Z" => "46.22Z - Commerce de gros (commerce interentreprises) de fleurs et plantes",
            "46.23Z" => "46.23Z - Commerce de gros (commerce interentreprises) d'animaux vivants",
            "46.24Z" => "46.24Z - Commerce de gros (commerce interentreprises) de cuirs et peaux",
            "46.31Z" => "46.31Z - Commerce de gros (commerce interentreprises) de fruits et l??gumes",
            "46.32A" => "46.32A - Commerce de gros (commerce interentreprises) de viandes de boucherie",
            "46.32B" => "46.32B - Commerce de gros (commerce interentreprises) de produits ?? base de viande",
            "46.32C" => "46.32C - Commerce de gros (commerce interentreprises) de volailles et gibier",
            "46.33Z" => "46.33Z - Commerce de gros (commerce interentreprises) de produits laitiers, ??ufs, huiles et mati??res grasses comestibles",
            "46.34Z" => "46.34Z - Commerce de gros (commerce interentreprises) de boissons",
            "46.35Z" => "46.35Z - Commerce de gros (commerce interentreprises) de produits ?? base de tabac",
            "46.36Z" => "46.36Z - Commerce de gros (commerce interentreprises) de sucre, chocolat et confiserie",
            "46.37Z" => "46.37Z - Commerce de gros (commerce interentreprises) de caf??, th??, cacao et ??pices",
            "46.38A" => "46.38A - Commerce de gros (commerce interentreprises) de poissons, crustac??s et mollusques",
            "46.38B" => "46.38B - Commerce de gros (commerce interentreprises) alimentaire sp??cialis?? divers",
            "46.39A" => "46.39A - Commerce de gros (commerce interentreprises) de produits surgel??s",
            "46.39B" => "46.39B - Commerce de gros (commerce interentreprises) alimentaire non sp??cialis??",
            "46.41Z" => "46.41Z - Commerce de gros (commerce interentreprises) de textiles",
            "46.42Z" => "46.42Z - Commerce de gros (commerce interentreprises) d'habillement et de chaussures",
            "46.43Z" => "46.43Z - Commerce de gros (commerce interentreprises) d'appareils ??lectrom??nagers",
            "46.44Z" => "46.44Z - Commerce de gros (commerce interentreprises) de vaisselle, verrerie et produits d'entretien",
            "46.45Z" => "46.45Z - Commerce de gros (commerce interentreprises) de parfumerie et de produits de beaut??",
            "46.46Z" => "46.46Z - Commerce de gros (commerce interentreprises) de produits pharmaceutiques",
            "46.47Z" => "46.47Z - Commerce de gros (commerce interentreprises) de meubles, de tapis et d'appareils d'??clairage",
            "46.48Z" => "46.48Z - Commerce de gros (commerce interentreprises) d'articles d'horlogerie et de bijouterie",
            "46.49Z" => "46.49Z - Commerce de gros (commerce interentreprises) d'autres biens domestiques",
            "46.51Z" => "46.51Z - Commerce de gros (commerce interentreprises) d'ordinateurs, d'??quipements informatiques p??riph??riques et de logiciels",
            "46.52Z" => "46.52Z - Commerce de gros (commerce interentreprises) de composants et d'??quipements ??lectroniques et de t??l??communication",
            "46.61Z" => "46.61Z - Commerce de gros (commerce interentreprises) de mat??riel agricole",
            "46.62Z" => "46.62Z - Commerce de gros (commerce interentreprises) de machines-outils",
            "46.63Z" => "46.63Z - Commerce de gros (commerce interentreprises) de machines pour l'extraction, la construction et le g??nie civil",
            "46.64Z" => "46.64Z - Commerce de gros (commerce interentreprises) de machines pour l'industrie textile et l'habillement",
            "46.65Z" => "46.65Z - Commerce de gros (commerce interentreprises) de mobilier de bureau",
            "46.66Z" => "46.66Z - Commerce de gros (commerce interentreprises) d'autres machines et ??quipements de bureau",
            "46.69A" => "46.69A - Commerce de gros (commerce interentreprises) de mat??riel ??lectrique",
            "46.69B" => "46.69B - Commerce de gros (commerce interentreprises) de fournitures et ??quipements industriels divers",
            "46.69C" => "46.69C - Commerce de gros (commerce interentreprises) de fournitures et ??quipements divers pour le commerce et les services",
            "46.71Z" => "46.71Z - Commerce de gros (commerce interentreprises) de combustibles et de produits annexes",
            "46.72Z" => "46.72Z - Commerce de gros (commerce interentreprises) de minerais et m??taux",
            "46.73A" => "46.73A - Commerce de gros (commerce interentreprises) de bois et de mat??riaux de construction",
            "46.73B" => "46.73B - Commerce de gros (commerce interentreprises) d'appareils sanitaires et de produits de d??coration",
            "46.74A" => "46.74A - Commerce de gros (commerce interentreprises) de quincaillerie",
            "46.74B" => "46.74B - Commerce de gros (commerce interentreprises) de fournitures pour la plomberie et le chauffage",
            "46.75Z" => "46.75Z - Commerce de gros (commerce interentreprises) de produits chimiques",
            "46.76Z" => "46.76Z - Commerce de gros (commerce interentreprises) d'autres produits interm??diaires",
            "46.77Z" => "46.77Z - Commerce de gros (commerce interentreprises) de d??chets et d??bris",
            "46.90Z" => "46.90Z - Commerce de gros (commerce interentreprises) non sp??cialis??",
            "47.11A" => "47.11A - Commerce de d??tail de produits surgel??s",
            "47.11B" => "47.11B - Commerce d'alimentation g??n??rale",
            "47.11C" => "47.11C - Sup??rettes",
            "47.11D" => "47.11D - Supermarch??s",
            "47.11E" => "47.11E - Magasins multi-commerces",
            "47.11F" => "47.11F - Hypermarch??s",
            "47.19A" => "47.19A - Grands magasins",
            "47.19B" => "47.19B - Autres commerces de d??tail en magasin non sp??cialis??",
            "47.21Z" => "47.21Z - Commerce de d??tail de fruits et l??gumes en magasin sp??cialis??",
            "47.22Z" => "47.22Z - Commerce de d??tail de viandes et de produits ?? base de viande en magasin sp??cialis??",
            "47.23Z" => "47.23Z - Commerce de d??tail de poissons, crustac??s et mollusques en magasin sp??cialis??",
            "47.24Z" => "47.24Z - Commerce de d??tail de pain, p??tisserie et confiserie en magasin sp??cialis??",
            "47.25Z" => "47.25Z - Commerce de d??tail de boissons en magasin sp??cialis??",
            "47.26Z" => "47.26Z - Commerce de d??tail de produits ?? base de tabac en magasin sp??cialis??",
            "47.29Z" => "47.29Z - Autres commerces de d??tail alimentaires en magasin sp??cialis??",
            "47.30Z" => "47.30Z - Commerce de d??tail de carburants en magasin sp??cialis??",
            "47.41Z" => "47.41Z - Commerce de d??tail d'ordinateurs, d'unit??s p??riph??riques et de logiciels en magasin sp??cialis??",
            "47.42Z" => "47.42Z - Commerce de d??tail de mat??riels de t??l??communication en magasin sp??cialis??",
            "47.43Z" => "47.43Z - Commerce de d??tail de mat??riels audio et vid??o en magasin sp??cialis??",
            "47.51Z" => "47.51Z - Commerce de d??tail de textiles en magasin sp??cialis??",
            "47.52A" => "47.52A - Commerce de d??tail de quincaillerie, peintures et verres en petites surfaces (moins de 400 m??)",
            "47.52B" => "47.52B - Commerce de d??tail de quincaillerie, peintures et verres en grandes surfaces (400 m?? et plus)",
            "47.53Z" => "47.53Z - Commerce de d??tail de tapis, moquettes et rev??tements de murs et de sols en magasin sp??cialis??",
            "47.54Z" => "47.54Z - Commerce de d??tail d'appareils ??lectrom??nagers en magasin sp??cialis??",
            "47.59A" => "47.59A - Commerce de d??tail de meubles",
            "47.59B" => "47.59B - Commerce de d??tail d'autres ??quipements du foyer",
            "47.61Z" => "47.61Z - Commerce de d??tail de livres en magasin sp??cialis??",
            "47.62Z" => "47.62Z - Commerce de d??tail de journaux et papeterie en magasin sp??cialis??",
            "47.63Z" => "47.63Z - Commerce de d??tail d'enregistrements musicaux et vid??o en magasin sp??cialis??",
            "47.64Z" => "47.64Z - Commerce de d??tail d'articles de sport en magasin sp??cialis??",
            "47.65Z" => "47.65Z - Commerce de d??tail de jeux et jouets en magasin sp??cialis??",
            "47.71Z" => "47.71Z - Commerce de d??tail d'habillement en magasin sp??cialis??",
            "47.72A" => "47.72A - Commerce de d??tail de la chaussure",
            "47.72B" => "47.72B - Commerce de d??tail de maroquinerie et d'articles de voyage",
            "47.73Z" => "47.73Z - Commerce de d??tail de produits pharmaceutiques en magasin sp??cialis??",
            "47.74Z" => "47.74Z - Commerce de d??tail d'articles m??dicaux et orthop??diques en magasin sp??cialis??",
            "47.75Z" => "47.75Z - Commerce de d??tail de parfumerie et de produits de beaut?? en magasin sp??cialis??",
            "47.76Z" => "47.76Z - Commerce de d??tail de fleurs, plantes, graines, engrais, animaux de compagnie et aliments pour ces animaux en magasin sp??cialis??",
            "47.77Z" => "47.77Z - Commerce de d??tail d'articles d'horlogerie et de bijouterie en magasin sp??cialis??",
            "47.78A" => "47.78A - Commerces de d??tail d'optique",
            "47.78B" => "47.78B - Commerces de d??tail de charbons et combustibles",
            "47.78C" => "47.78C - Autres commerces de d??tail sp??cialis??s divers",
            "47.79Z" => "47.79Z - Commerce de d??tail de biens d'occasion en magasin",
            "47.81Z" => "47.81Z - Commerce de d??tail alimentaire sur ??ventaires et march??s",
            "47.82Z" => "47.82Z - Commerce de d??tail de textiles, d'habillement et de chaussures sur ??ventaires et march??s",
            "47.89Z" => "47.89Z - Autres commerces de d??tail sur ??ventaires et march??s",
            "47.91A" => "47.91A - Vente ?? distance sur catalogue g??n??ral",
            "47.91B" => "47.91B - Vente ?? distance sur catalogue sp??cialis??",
            "47.99A" => "47.99A - Vente ?? domicile",
            "47.99B" => "47.99B - Vente par automates et autres commerces de d??tail hors magasin, ??ventaires ou march??s n.c.a.",
            "49.10Z" => "49.10Z - Transport ferroviaire interurbain de voyageurs",
            "49.20Z" => "49.20Z - Transports ferroviaires de fret",
            "49.31Z" => "49.31Z - Transports urbains et suburbains de voyageurs",
            "49.32Z" => "49.32Z - Transports de voyageurs par taxis",
            "49.39A" => "49.39A - Transports routiers r??guliers de voyageurs",
            "49.39B" => "49.39B - Autres transports routiers de voyageurs",
            "49.39C" => "49.39C - T??l??ph??riques et remont??es m??caniques",
            "49.41A" => "49.41A - Transports routiers de fret interurbains",
            "49.41B" => "49.41B - Transports routiers de fret de proximit??",
            "49.41C" => "49.41C - Location de camions avec chauffeur",
            "49.42Z" => "49.42Z - Services de d??m??nagement",
            "49.50Z" => "49.50Z - Transports par conduites",
            "50.10Z" => "50.10Z - Transports maritimes et c??tiers de passagers",
            "50.20Z" => "50.20Z - Transports maritimes et c??tiers de fret",
            "50.30Z" => "50.30Z - Transports fluviaux de passagers",
            "50.40Z" => "50.40Z - Transports fluviaux de fret",
            "51.10Z" => "51.10Z - Transports a??riens de passagers",
            "51.21Z" => "51.21Z - Transports a??riens de fret",
            "51.22Z" => "51.22Z - Transports spatiaux",
            "52.10A" => "52.10A - Entreposage et stockage frigorifique",
            "52.10B" => "52.10B - Entreposage et stockage non frigorifique",
            "52.21Z" => "52.21Z - Services auxiliaires des transports terrestres",
            "52.22Z" => "52.22Z - Services auxiliaires des transports par eau",
            "52.23Z" => "52.23Z - Services auxiliaires des transports a??riens",
            "52.24A" => "52.24A - Manutention portuaire",
            "52.24B" => "52.24B - Manutention non portuaire",
            "52.29A" => "52.29A - Messagerie, fret express",
            "52.29B" => "52.29B - Affr??tement et organisation des transports",
            "53.10Z" => "53.10Z - Activit??s de poste dans le cadre d'une obligation de service universel",
            "53.20Z" => "53.20Z - Autres activit??s de poste et de courrier",
            "55.10Z" => "55.10Z - H??tels et h??bergement similaire",
            "55.20Z" => "55.20Z - H??bergement touristique et autre h??bergement de courte dur??e",
            "55.30Z" => "55.30Z - Terrains de camping et parcs pour caravanes ou v??hicules de loisirs",
            "55.90Z" => "55.90Z - Autres h??bergements",
            "56.10A" => "56.10A - Restauration traditionnelle",
            "56.10B" => "56.10B - Caf??t??rias et autres libres-services",
            "56.10C" => "56.10C - Restauration de type rapide",
            "56.21Z" => "56.21Z - Services des traiteurs",
            "56.29A" => "56.29A - Restauration collective sous contrat",
            "56.29B" => "56.29B - Autres services de restauration n.c.a.",
            "56.30Z" => "56.30Z - D??bits de boissons",
            "58.11Z" => "58.11Z - ??dition de livres",
            "58.12Z" => "58.12Z - ??dition de r??pertoires et de fichiers d'adresses",
            "58.13Z" => "58.13Z - ??dition de journaux",
            "58.14Z" => "58.14Z - ??dition de revues et p??riodiques",
            "58.19Z" => "58.19Z - Autres activit??s d'??dition",
            "58.21Z" => "58.21Z - ??dition de jeux ??lectroniques",
            "58.29A" => "58.29A - ??dition de logiciels syst??me et de r??seau",
            "58.29B" => "58.29B - ??dition de logiciels outils de d??veloppement et de langages",
            "58.29C" => "58.29C - ??dition de logiciels applicatifs",
            "59.11A" => "59.11A - Production de films et de programmes pour la t??l??vision",
            "59.11B" => "59.11B - Production de films institutionnels et publicitaires",
            "59.11C" => "59.11C - Production de films pour le cin??ma",
            "59.12Z" => "59.12Z - Post-production de films cin??matographiques, de vid??o et de programmes de t??l??vision",
            "59.13A" => "59.13A - Distribution de films cin??matographiques",
            "59.13B" => "59.13B - ??dition et distribution vid??o",
            "59.14Z" => "59.14Z - Projection de films cin??matographiques",
            "59.20Z" => "59.20Z - Enregistrement sonore et ??dition musicale",
            "60.10Z" => "60.10Z - ??dition et diffusion de programmes radio",
            "60.20A" => "60.20A - ??dition de cha??nes g??n??ralistes",
            "60.20B" => "60.20B - ??dition de cha??nes th??matiques",
            "61.10Z" => "61.10Z - T??l??communications filaires",
            "61.20Z" => "61.20Z - T??l??communications sans fil",
            "61.30Z" => "61.30Z - T??l??communications par satellite",
            "61.90Z" => "61.90Z - Autres activit??s de t??l??communication",
            "62.01Z" => "62.01Z - Programmation informatique",
            "62.02A" => "62.02A - Conseil en syst??mes et logiciels informatiques",
            "62.02B" => "62.02B - Tierce maintenance de syst??mes et d'applications informatiques",
            "62.03Z" => "62.03Z - Gestion d'installations informatiques",
            "62.09Z" => "62.09Z - Autres activit??s informatiques",
            "63.11Z" => "63.11Z - Traitement de donn??es, h??bergement et activit??s connexes",
            "63.12Z" => "63.12Z - Portails internet",
            "63.91Z" => "63.91Z - Activit??s des agences de presse",
            "63.99Z" => "63.99Z - Autres services d'information n.c.a.",
            "64.11Z" => "64.11Z - Activit??s de banque centrale",
            "64.19Z" => "64.19Z - Autres interm??diations mon??taires",
            "64.20Z" => "64.20Z - Activit??s des soci??t??s holding",
            "64.30Z" => "64.30Z - Fonds de placement et entit??s financi??res similaires",
            "64.91Z" => "64.91Z - Cr??dit-bail",
            "64.92Z" => "64.92Z - Autre distribution de cr??dit",
            "64.99Z" => "64.99Z - Autres activit??s des services financiers, hors assurance et caisses de retraite, n.c.a.",
            "65.11Z" => "65.11Z - Assurance vie",
            "65.12Z" => "65.12Z - Autres assurances",
            "65.20Z" => "65.20Z - R??assurance",
            "65.30Z" => "65.30Z - Caisses de retraite",
            "66.11Z" => "66.11Z - Administration de march??s financiers",
            "66.12Z" => "66.12Z - Courtage de valeurs mobili??res et de marchandises",
            "66.19A" => "66.19A - Supports juridiques de gestion de patrimoine mobilier",
            "66.19B" => "66.19B - Autres activit??s auxiliaires de services financiers, hors assurance et caisses de retraite, n.c.a.",
            "66.21Z" => "66.21Z - ??valuation des risques et dommages",
            "66.22Z" => "66.22Z - Activit??s des agents et courtiers d'assurances",
            "66.29Z" => "66.29Z - Autres activit??s auxiliaires d'assurance et de caisses de retraite",
            "66.30Z" => "66.30Z - Gestion de fonds",
            "68.10Z" => "68.10Z - Activit??s des marchands de biens immobiliers",
            "68.20A" => "68.20A - Location de logements",
            "68.20B" => "68.20B - Location de terrains et d'autres biens immobiliers",
            "68.31Z" => "68.31Z - Agences immobili??res",
            "68.32A" => "68.32A - Administration d'immeubles et autres biens immobiliers",
            "68.32B" => "68.32B - Supports juridiques de gestion de patrimoine immobilier",
            "69.10Z" => "69.10Z - Activit??s juridiques",
            "69.20Z" => "69.20Z - Activit??s comptables",
            "70.10Z" => "70.10Z - Activit??s des si??ges sociaux",
            "70.21Z" => "70.21Z - Conseil en relations publiques et communication",
            "70.22Z" => "70.22Z - Conseil pour les affaires et autres conseils de gestion",
            "71.11Z" => "71.11Z - Activit??s d'architecture",
            "71.12A" => "71.12A - Activit?? des g??om??tres",
            "71.12B" => "71.12B - Ing??nierie, ??tudes techniques",
            "71.20A" => "71.20A - Contr??le technique automobile",
            "71.20B" => "71.20B - Analyses, essais et inspections techniques",
            "72.11Z" => "72.11Z - Recherche-d??veloppement en biotechnologie",
            "72.19Z" => "72.19Z - Recherche-d??veloppement en autres sciences physiques et naturelles",
            "72.20Z" => "72.20Z - Recherche-d??veloppement en sciences humaines et sociales",
            "73.11Z" => "73.11Z - Activit??s des agences de publicit??",
            "73.12Z" => "73.12Z - R??gie publicitaire de m??dias",
            "73.20Z" => "73.20Z - ??tudes de march?? et sondages",
            "74.10Z" => "74.10Z - Activit??s sp??cialis??es de design",
            "74.20Z" => "74.20Z - Activit??s photographiques",
            "74.30Z" => "74.30Z - Traduction et interpr??tation",
            "74.90A" => "74.90A - Activit?? des ??conomistes de la construction",
            "74.90B" => "74.90B - Activit??s sp??cialis??es, scientifiques et techniques diverses",
            "75.00Z" => "75.00Z - Activit??s v??t??rinaires",
            "77.11A" => "77.11A - Location de courte dur??e de voitures et de v??hicules automobiles l??gers",
            "77.11B" => "77.11B - Location de longue dur??e de voitures et de v??hicules automobiles l??gers",
            "77.12Z" => "77.12Z - Location et location-bail de camions",
            "77.21Z" => "77.21Z - Location et location-bail d'articles de loisirs et de sport",
            "77.22Z" => "77.22Z - Location de vid??ocassettes et disques vid??o",
            "77.29Z" => "77.29Z - Location et location-bail d'autres biens personnels et domestiques",
            "77.31Z" => "77.31Z - Location et location-bail de machines et ??quipements agricoles",
            "77.32Z" => "77.32Z - Location et location-bail de machines et ??quipements pour la construction",
            "77.33Z" => "77.33Z - Location et location-bail de machines de bureau et de mat??riel informatique",
            "77.34Z" => "77.34Z - Location et location-bail de mat??riels de transport par eau",
            "77.35Z" => "77.35Z - Location et location-bail de mat??riels de transport a??rien",
            "77.39Z" => "77.39Z - Location et location-bail d'autres machines, ??quipements et biens mat??riels n.c.a.",
            "77.40Z" => "77.40Z - Location-bail de propri??t?? intellectuelle et de produits similaires, ?? l'exception des ??uvres soumises ?? copyright",
            "78.10Z" => "78.10Z - Activit??s des agences de placement de main-d'??uvre",
            "78.20Z" => "78.20Z - Activit??s des agences de travail temporaire",
            "78.30Z" => "78.30Z - Autre mise ?? disposition de ressources humaines",
            "79.11Z" => "79.11Z - Activit??s des agences de voyage",
            "79.12Z" => "79.12Z - Activit??s des voyagistes",
            "79.90Z" => "79.90Z - Autres services de r??servation et activit??s connexes",
            "80.10Z" => "80.10Z - Activit??s de s??curit?? priv??e : Pr??vention et s??curit?? priv??e en France",
            "80.20Z" => "80.20Z - Activit??s li??es aux syst??mes de s??curit??",
            "80.30Z" => "80.30Z - Activit??s d'enqu??te",
            "81.10Z" => "81.10Z - Activit??s combin??es de soutien li?? aux b??timents",
            "81.21Z" => "81.21Z - Nettoyage courant des b??timents",
            "81.22Z" => "81.22Z - Autres activit??s de nettoyage des b??timents et nettoyage industriel",
            "81.29A" => "81.29A - D??sinfection, d??sinsectisation, d??ratisation",
            "81.29B" => "81.29B - Autres activit??s de nettoyage n.c.a.",
            "81.30Z" => "81.30Z - Services d'am??nagement paysager",
            "82.11Z" => "82.11Z - Services administratifs combin??s de bureau",
            "82.19Z" => "82.19Z - Photocopie, pr??paration de documents et autres activit??s sp??cialis??es de soutien de bureau",
            "82.20Z" => "82.20Z - Activit??s de centres d'appels",
            "82.30Z" => "82.30Z - Organisation de foires, salons professionnels et congr??s",
            "82.91Z" => "82.91Z - Activit??s des agences de recouvrement de factures et des soci??t??s d'information financi??re sur la client??le",
            "82.92Z" => "82.92Z - Activit??s de conditionnement",
            "82.99Z" => "82.99Z - Autres activit??s de soutien aux entreprises n.c.a.",
            "84.11Z" => "84.11Z - Administration publique g??n??rale",
            "84.12Z" => "84.12Z - Administration publique (tutelle) de la sant??, de la formation, de la culture et des services sociaux, autre que s??curit?? sociale",
            "84.13Z" => "84.13Z - Administration publique (tutelle) des activit??s ??conomiques",
            "84.21Z" => "84.21Z - Affaires ??trang??res",
            "84.22Z" => "84.22Z - D??fense",
            "84.23Z" => "84.23Z - Justice",
            "84.24Z" => "84.24Z - Activit??s d'ordre public et de s??curit??",
            "84.25Z" => "84.25Z - Services du feu et de secours",
            "84.30A" => "84.30A - Activit??s g??n??rales de s??curit?? sociale",
            "84.30B" => "84.30B - Gestion des retraites compl??mentaires",
            "84.30C" => "84.30C - Distribution sociale de revenus",
            "85.10Z" => "85.10Z - Enseignement pr??-primaire",
            "85.20Z" => "85.20Z - Enseignement primaire",
            "85.31Z" => "85.31Z - Enseignement secondaire g??n??ral",
            "85.32Z" => "85.32Z - Enseignement secondaire technique ou professionnel",
            "85.41Z" => "85.41Z - Enseignement post-secondaire non sup??rieur",
            "85.42Z" => "85.42Z - Enseignement sup??rieur",
            "85.51Z" => "85.51Z - Enseignement de disciplines sportives et d'activit??s de loisirs",
            "85.52Z" => "85.52Z - Enseignement culturel",
            "85.53Z" => "85.53Z - Enseignement de la conduite",
            "85.59A" => "85.59A - Formation continue d'adultes",
            "85.59B" => "85.59B - Autres enseignements",
            "85.60Z" => "85.60Z - Activit??s de soutien ?? l'enseignement",
            "86.10Z" => "86.10Z - Activit??s hospitali??res",
            "86.21Z" => "86.21Z - Activit?? des m??decins g??n??ralistes",
            "86.22A" => "86.22A - Activit??s de radiodiagnostic et de radioth??rapie",
            "86.22B" => "86.22B - Activit??s chirurgicales",
            "86.22C" => "86.22C - Autres activit??s des m??decins sp??cialistes",
            "86.23Z" => "86.23Z - Pratique dentaire",
            "86.90A" => "86.90A - Ambulances",
            "86.90B" => "86.90B - Laboratoires d'analyses m??dicales",
            "86.90C" => "86.90C - Centres de collecte et banques d'organes",
            "86.90D" => "86.90D - Activit??s des infirmiers et des sages-femmes",
            "86.90E" => "86.90E - Activit??s des professionnels de la r????ducation, de l'appareillage et des p??dicures-podologues",
            "86.90F" => "86.90F - Activit??s de sant?? humaine non class??es ailleurs",
            "87.10A" => "87.10A - H??bergement m??dicalis?? pour personnes ??g??es",
            "87.10B" => "87.10B - H??bergement m??dicalis?? pour enfants handicap??s",
            "87.10C" => "87.10C - H??bergement m??dicalis?? pour adultes handicap??s et autre h??bergement m??dicalis??",
            "87.20A" => "87.20A - H??bergement social pour handicap??s mentaux et malades mentaux",
            "87.20B" => "87.20B - H??bergement social pour toxicomanes",
            "87.30A" => "87.30A - H??bergement social pour personnes ??g??es",
            "87.30B" => "87.30B - H??bergement social pour handicap??s physiques",
            "87.90A" => "87.90A - H??bergement social pour enfants en difficult??s",
            "87.90B" => "87.90B - H??bergement social pour adultes et familles en difficult??s et autre h??bergement social",
            "88.10A" => "88.10A - Aide ?? domicile",
            "88.10B" => "88.10B - Accueil ou accompagnement sans h??bergement d'adultes handicap??s ou de personnes ??g??es",
            "88.10C" => "88.10C - Aide par le travail",
            "88.91A" => "88.91A - Accueil de jeunes enfants",
            "88.91B" => "88.91B - Accueil ou accompagnement sans h??bergement d'enfants handicap??s",
            "88.99A" => "88.99A - Autre accueil ou accompagnement sans h??bergement d'enfants et d'adolescents",
            "88.99B" => "88.99B - Action sociale sans h??bergement n.c.a.",
            "90.01Z" => "90.01Z - Arts du spectacle vivant",
            "90.02Z" => "90.02Z - Activit??s de soutien au spectacle vivant",
            "90.03A" => "90.03A - Cr??ation artistique relevant des arts plastiques",
            "90.03B" => "90.03B - Autre cr??ation artistique",
            "90.04Z" => "90.04Z - Gestion de salles de spectacles",
            "91.01Z" => "91.01Z - Gestion des biblioth??ques et des archives",
            "91.02Z" => "91.02Z - Gestion des mus??es",
            "91.03Z" => "91.03Z - Gestion des sites et monuments historiques et des attractions touristiques similaires",
            "91.04Z" => "91.04Z - Gestion des jardins botaniques et zoologiques et des r??serves naturelles",
            "92.00Z" => "92.00Z - Organisation de jeux de hasard et d'argent",
            "93.11Z" => "93.11Z - Gestion d'installations sportives",
            "93.12Z" => "93.12Z - Activit??s de clubs de sports",
            "93.13Z" => "93.13Z - Activit??s des centres de culture physique",
            "93.19Z" => "93.19Z - Autres activit??s li??es au sport",
            "93.21Z" => "93.21Z - Activit??s des parcs d'attractions et parcs ?? th??mes",
            "93.29Z" => "93.29Z - Autres activit??s r??cr??atives et de loisirs",
            "94.11Z" => "94.11Z - Activit??s des organisations patronales et consulaires",
            "94.12Z" => "94.12Z - Activit??s des organisations professionnelles",
            "94.20Z" => "94.20Z - Activit??s des syndicats de salari??s",
            "94.91Z" => "94.91Z - Activit??s des organisations religieuses",
            "94.92Z" => "94.92Z - Activit??s des organisations politiques",
            "94.99Z" => "94.99Z - Autres organisations fonctionnant par adh??sion volontaire",
            "95.11Z" => "95.11Z - R??paration d'ordinateurs et d'??quipements p??riph??riques",
            "95.12Z" => "95.12Z - R??paration d'??quipements de communication",
            "95.21Z" => "95.21Z - R??paration de produits ??lectroniques grand public",
            "95.22Z" => "95.22Z - R??paration d'appareils ??lectrom??nagers et d'??quipements pour la maison et le jardin",
            "95.23Z" => "95.23Z - R??paration de chaussures et d'articles en cuir",
            "95.24Z" => "95.24Z - R??paration de meubles et d'??quipements du foyer",
            "95.25Z" => "95.25Z - R??paration d'articles d'horlogerie et de bijouterie",
            "95.29Z" => "95.29Z - R??paration d'autres biens personnels et domestiques",
            "96.01A" => "96.01A - Blanchisserie-teinturerie de gros",
            "96.01B" => "96.01B - Blanchisserie-teinturerie de d??tail",
            "96.02A" => "96.02A - Coiffure",
            "96.02B" => "96.02B - Soins de beaut??",
            "96.03Z" => "96.03Z - Services fun??raires",
            "96.04Z" => "96.04Z - Entretien corporel",
            "96.09Z" => "96.09Z - Autres services personnels n.c.a.",
            "97.00Z" => "97.00Z - Activit??s des m??nages en tant qu'employeurs de personnel domestique",
            "98.10Z" => "98.10Z - Activit??s indiff??renci??es des m??nages en tant que producteurs de biens pour usage propre",
            "98.20Z" => "98.20Z - Activit??s indiff??renci??es des m??nages en tant que producteurs de services pour usage propre",
            "99.00Z" => "99.00Z - Activit??s des organisations et organismes extraterritoriaux",
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
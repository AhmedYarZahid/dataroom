<?php

namespace backend\modules\mailing\controllers;

use backend\modules\mailing\models\MailingContact;
use backend\modules\mailing\models\MailingContactSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use backend\modules\mailing\managers\MailingListManager;
use backend\modules\mailing\models\MailingList;
use backend\modules\mailing\models\MailingListSearch;
use backend\modules\mailing\models\MailingContactForm;
use yii\web\Response;

class ListController extends \backend\controllers\Controller
{
    public $title = 'AJA List';
    public $titleSmall = 'Gérer les listes de diffusion';
    public $layout = 'main';

    protected $manager;

    public function __construct($id, $controller, MailingListManager $manager, $config = [])
    {
        $this->manager = $manager;

        parent::__construct($id, $controller, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays all lists.
     */
    public function actionIndex()
    {
        $searchModel = new MailingListSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays users inside the list.
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new MailingContactSearch();

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search($id, Yii::$app->request->queryParams),
            'listID' => $id
        ]);
    }

    /**
     * Creates a new list.
     */
    public function actionCreate()
    {
        $model = new MailingList;
        $model->createdByUserID = Yii::$app->user->id;

        $contactForm = new MailingContactForm;

        $data = Yii::$app->request->post();

        $contactForm->load($data);
        $model->load($data);
        if(!empty($data)) $model = $this->actionSetFiltersJson($model, $data);
        $contactForm->contactIds = $model->contactIds;

        if (!Yii::$app->request->isAjax && !empty($data) && $this->manager->create($model)) {

            foreach ($data['extraContacts'] as $extraContact) {
                $newsletter = new \common\models\Newsletter();
                $newsletter->email = $extraContact;
                $newsletter->createdDate = date("Y-m-d H:i:s");
                $newsletter->languageID = "fr";
                $newsletter->isActive = 1;
                $newsletter->profession = $data['MailingContactForm']['profession'];
                $newsletter->save();

                $mailingContact = new  \backend\modules\mailing\models\MailingContact();
                $mailingContact->code = \Yii::$app->security->generateRandomString(32);
                $mailingContact->newsletterID = $newsletter->id;
                $mailingContact->listID = $model->id;
                if (!$mailingContact->save()) {
                    print_r($mailingContact->errors);
                }
            }

            Yii::$app->session->setFlash('success', Yii::t('admin', 'New list has been created successfully.'));

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'contactForm' => $contactForm,
        ]);
    }

    /**
     * @param $model
     * @param $post
     * @return void
     * create and store filters json in mailing list
     */
    public function actionSetFiltersJson($model, $post)
    {
        $filters = [];
        $filters['profile'] = $post['MailingContactForm']['profile'];
        $filters['profession'] = $post['MailingContactForm']['profession'];
        $filters['activity'] = $post['MailingContactForm']['activity'];
        $filters['targetedSector'] = implode(',', $post['MailingContactForm']['targetedSector']);
        $filters['targetedTurnover'] = implode(',', $post['MailingContactForm']['targetedTurnover']);
        $filters['entranceTicket'] = implode(',', $post['MailingContactForm']['entranceTicket']);
        $filters['geographicalArea'] = implode(',', $post['MailingContactForm']['geographicalArea']);
        $filters['targetAmount'] = implode(',', $post['MailingContactForm']['targetAmount']);
        $filters['effectiveMin'] = $post['MailingContactForm']['effectiveMin'];
        $filters['effectiveMax'] = $post['MailingContactForm']['effectiveMax'];
        $filters['targetSector'] = implode(',', $post['MailingContactForm']['targetSector']);
        $filters['regionIDs'] = implode(',', $post['MailingContactForm']['regionIDs']);
        $filters['targetedAssetsAmount'] = implode(',', $post['MailingContactForm']['targetedAssetsAmount']);
        $filters['assetsDestination'] = implode(',', $post['MailingContactForm']['assetsDestination']);
        $filters['operationNature'] = implode(',', $post['MailingContactForm']['operationNature']);
        $filters['propertyType'] = implode(',', $post['MailingContactForm']['propertyType']);
        $filters['coownershipRegionIDs'] = implode(',', $post['MailingContactForm']['coownershipRegionIDs']);
        $filters['lotsNumber'] = $post['MailingContactForm']['lotsNumber'];
        $filters['coownersNumber'] = $post['MailingContactForm']['coownersNumber'];
        $filters['extraContacts'] = implode(',', $post['extraContacts']);
        $model->filters_json = json_encode($filters);
        return $model;
    }

    /**
     * Updates a list.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $contactForm = new MailingContactForm;

        $data = Yii::$app->request->post();

        if ($data) {
            $contactForm->load($data);
            $model->load($data);
            $this->actionSetFiltersJson($model, $data);
        } else {
            $model->loadContacts();
            $filters = $this->setSelectedFilters($model, $contactForm);
        }

        $contactForm->contactIds = $model->contactIds;

        if (!Yii::$app->request->isAjax && !empty($data) && $this->manager->update($model)) {

            Yii::$app->session->setFlash('success', Yii::t('admin', 'The list has been updated successfully.'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'contactForm' => $contactForm,
            'filters' => $filters
        ]);
    }

    /**
     * @param $model
     * @param $contactForm
     * @return mixed
     * set selected filters
     */
    function setSelectedFilters($model, $contactForm)
    {
        $filters = json_decode($model->filters_json);
        $contactForm->profile = $filters->profile;
        $contactForm->profession = $filters->profession;
        $contactForm->activity = $filters->activity;
        $contactForm->effectiveMin = $filters->effectiveMin;
        $contactForm->effectiveMax = $filters->effectiveMax;
        $contactForm->lotsNumber = $filters->lotsNumber;
        $contactForm->coownersNumber = $filters->coownersNumber;
        return $filters;
    }

    /**
     * @param $selections
     * @return array
     * set selected array values as true for multiselect options
     */
    function makeSelections($selections) {
        $options = [];
        foreach ($selections as $selection) {
            $options[$selection] = ['selected' => true];
        }
        return $options;
    }


    /**
     * Deletes a list.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes a contact form the list
     */
    public function actionDeleteContact($id, $listID)
    {
        $model = MailingContact::findOne($id);

        $model->delete();

        return $this->redirect(['view', 'id' => $listID]);
    }

    /**
     * Deletes a contact form the list
     */
    public function actionDeleteContacts(array $ids)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $models = MailingContact::find()->andWhere(['id' => $ids])->all();

        foreach ($models as $model) {
            $model->delete();
        }

        return true;
    }

    /**
     * Finds the MailingList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MailingList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MailingList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

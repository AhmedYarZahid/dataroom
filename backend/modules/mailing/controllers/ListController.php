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
//                $mailingContact->createdDate = date("Y-m-d H:i:s");
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
        } else {
            $model->loadContacts();
        }

        $contactForm->contactIds = $model->contactIds;

        if (!Yii::$app->request->isAjax && !empty($data) && $this->manager->update($model)) {
            Yii::$app->session->setFlash('success', Yii::t('admin', 'The list has been updated successfully.'));

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'contactForm' => $contactForm,
        ]);
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

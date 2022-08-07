<?php

namespace backend\controllers;

use common\models\Newsletter;
use kartik\helpers\Html;
use Yii;
use common\models\User;
use common\models\UserSearch;
use common\models\UserHistory;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\notify\models\Notify;
use backend\modules\dataroom\UserManager;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    protected $userManager;

    public function __construct($id, $controller, UserManager $userManager, $config = [])
    {
        $this->userManager = $userManager;

        parent::__construct($id, $controller, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Users');
        $this->titleSmall = Yii::t('admin', 'Manage users');

        parent::init();
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $userHistory = new ActiveDataProvider([
            'query' => UserHistory::find()->where(['entityID' => $id])->with('user'),
            'sort' => [
                'defaultOrder' => [
                    'createdDate' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageParam' => 'login-history-page',
            ],
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'userHistory' => $userHistory,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('register');
        $model->loadDefaultValues();

        $profiles = $this->userManager->getAvailableProfiles($model);

        if ($model->load(Yii::$app->request->post())) {

            $model->generateConfirmationCode();
            $model->generateAuthKey();
            $model->setPassword($model->password);
            $model->isConfirmed = 1; // Maybe user should confirm his account using link from email?

            if ($model->type == User::TYPE_ADMIN) {
                $model->profession = Newsletter::PROFESSION_MEMBER_AJA;
            }

            if ($model->validate()) {

                $model->saveUploadedLogo();

                if ($this->userManager->save($model)) {

                    // In case using RBAC
                    /*$userRole = Yii::$app->authManager->getRole($model->type);
                    Yii::$app->authManager->assign($userRole, $model->id);*/

                    Notify::sendUserCreatedByAdmin($model);

                    \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'New user has been created successfully.'));

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        $photosInitData = [
            'preview' => [],
            'config' => [],
        ];

        return $this->render('create', [
            'model' => $model,
            'profiles' => $profiles,
            'photosInitData' => $photosInitData,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->isAllowUpdate()) {
            throw new BadRequestHttpException(Yii::t('admin', 'You have no rights to update this user.'));
        }

        $profiles = $this->userManager->getAvailableProfiles($model);

        if (Yii::$app->request->post('UpdatePassword') !== null) {
            $model->setScenario('update-password');
        } else {
            $model->setScenario('update-profile-admin');
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->scenario == 'update-password') {
                $model->setPassword($model->password);
                $saved = $model->save();
            } else {
                if ($model->isMailingContact) {
                    $this->userManager->validateProfiles($profiles);
                }
                $saved = $this->userManager->save($model, null, !$model->isMailingContact);
            }
            
            if ($saved) {
                // In case using RBAC
                /*$userRole = Yii::$app->authManager->getRole($model->type);
                Yii::$app->authManager->revokeAll($model->id);
                Yii::$app->authManager->assign($userRole, $model->id);*/

                \Yii::$app->getSession()->setFlash('success', Yii::t('admin', 'Changes saved successfully.'));

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $photosInitData = [
            'preview' => [],
            'config' => [],
        ];

        if ($model->getLogoPath()) {
            $photosInitData['preview'][] = [
                Html::img($model->getLogoPath(true), ['class'=>'file-preview-image']),
            ];

            $photosInitData['config'][] = [
                'url' => Url::to(['user/delete-photo', 'id' => $model->id]),
            ];
        }

        return $this->render('update', [
            'model' => $model,
            'profiles' => $profiles,
            'photosInitData' => $photosInitData,
        ]);
    }

    /**
     * Delete user logo
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param integer $id
     * @return bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDeletePhoto($id)
    {
        $model = $this->findModel($id);

        if (!$model->isAllowUpdate()) {
            throw new BadRequestHttpException(Yii::t('admin', 'You have no rights to update this user.'));
        }

        if ($fullPath = $model->getLogoPath()) {
            @unlink($fullPath);
        }

        $model->logo = '';
        $model->save(false, ['logo']);

        return true;
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->isAllowDelete()) {
            throw new BadRequestHttpException(Yii::t('admin', 'You have no rights to remove this user.'));
        }

        $model->isRemoved = 1;
        $model->isActive = 0;
        $model->generateDeletedEmail();

        $model->save(false);

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findByID($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionTestEmail()
    {
        $adminEmail = Yii::$app->params['mail']['adminEmail'];
        if (is_array(Yii::$app->params['mail']['adminEmail'])) {
            $adminEmail = Yii::$app->params['mail']['adminEmail'][0];
        }

        $message = Yii::$app->mailjet->compose()
            ->setFrom($adminEmail)
            ->setTo("isaakahmedov@gmail.com")
            ->setHtmlBody("test")
            ->setTextBody("test")
            ->setSubject("asdasd")
            ->setCampaign("notify auto");

        $sent = $message->send();

        if (!$sent) {
            dd($sent);
        }

        dd(Yii::$app->mailjet->response->getData());

//        $mail = Yii::$app->mailer->compose()
//            ->setFrom(array($adminEmail => Yii::$app->params['mail']['adminName']))
//            ->setTo("isxoqjon_7710@mail.ru")
//            ->setSubject(Yii::$app->env->getEmailSubject("asdadssadasd"))
//            ->setHtmlBody( "sadsadasd")
//            ->setReplyTo(Yii::$app->params['mail']['replyToEmail']);
//
//
//
//        if (!$mail->send()) {
//            throw new \Exception('Cannot send email');
//        }

    }
}

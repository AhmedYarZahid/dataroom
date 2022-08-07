<?php

namespace frontend\modules\dataroom\controllers;

use backend\modules\dataroom\models\Room;
use backend\modules\dataroom\models\RoomCoownership;
use backend\modules\dataroom\models\RoomCV;
use backend\modules\dataroom\models\RoomRealEstate;
use backend\modules\dataroom\models\search\RoomCoownershipSearch;
use backend\modules\dataroom\models\search\RoomCVSearch;
use backend\modules\dataroom\models\search\RoomRealEstateSearch;
use backend\modules\notify\models\Notify;
use Da\QrCode\Exception\Exception;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\User;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use backend\modules\dataroom\UserManager;
use backend\modules\dataroom\models\search\RoomCompanySearch;
use backend\modules\dataroom\Module as DataroomModule;

class UserController extends AbstractController
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
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', /*'login',*/ 'my-profile', 'my-rooms'/*, 'one-time-login'*/],
                'rules' => [
                    /*[
                        'actions' => ['login', 'one-time-login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],*/
                    [
                        'actions' => ['logout', 'my-profile', 'my-rooms'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Show owned rooms for manager and rooms with access for buyer.
     * @param string $section
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionMyRooms($section = null)
    {
        $this->layout = 'my-rooms-list';

        if (!$section) {
            $section = DataroomModule::SECTION_COMPANIES;
        }

        $user = User::findOne(Yii::$app->user->id);

        if ($section == DataroomModule::SECTION_COMPANIES) {
            $searchModel = new RoomCompanySearch();
        } elseif ($section == DataroomModule::SECTION_REAL_ESTATE) {
            $searchModel = new RoomRealEstateSearch();
        } elseif ($section == DataroomModule::SECTION_COOWNERSHIP) {
            $searchModel = new RoomCoownershipSearch();
        } elseif ($section == DataroomModule::SECTION_CV) {
            // We should allow access for all CV Rooms in case user has already at least one access to any CV room
            Yii::$app->user->identity->getCVAccessRequestAllRooms();

            $searchModel = new RoomCVSearch();
        } else {
            throw new BadRequestHttpException();
        }

        return $this->render('my-rooms', [
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->searchUserRooms($user),
            'user' => $user,
            'section' => $section
        ]);
    }

    public function actionMyProfile()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->scenario = 'update-profile';

        $profiles = $this->userManager->getAvailableProfiles($user);
        
        if ($user->load(Yii::$app->request->post())) {

            if ($user->isMailingContact) {
                $this->userManager->validateProfiles($profiles);
            }

            if ($this->userManager->save($user, null, !$user->isMailingContact)) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Changes saved successfully.'));

                return $this->redirect(['my-profile']);
            }
        }

        return $this->render('my-profile', [
            'user' => $user,
            'profiles' => $profiles,
        ]);
    }

    /**
     * Login user
     *
     * @param integer $goToRoomID
     * @return string|Response
     */
    public function actionLogin($goToRoomID = null)
    {
        if ($goToRoomID && !Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $redirect = Yii::$app->request->get('redirect');

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if ($redirect) {
                return $this->redirect($redirect);
            } elseif ($goToRoomID) {
                return $this->redirect(Room::findOne($goToRoomID)->getDetailedRoom()->one()->getUrl(false));
            } else {
                return $this->goBack();
            }
        } else {
            return $this->render('login', [
                'model' => $model,
                'redirect' => $redirect,
            ]);
        }
    }

    /**
     * Logout user
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * "Forgot password" form
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return string|Response
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Sorry, we are unable to reset password for email provided.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password by token
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $token
     * @param bool $expired
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token, $expired = false)
    {
        if (!$expired) {
            try {
                $model = new ResetPasswordForm($token);
            } catch (InvalidParamException $e) {
                throw new BadRequestHttpException($e->getMessage());
            }

            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'New password was saved.'));

                return $this->goHome();
            }
        }

        return $this->render('resetPassword', [
            'model' => isset($model) ? $model : null,
            'expired' => $expired,
        ]);
    }

    public function actionOneTimeLogin($token)
    {
        $user = User::findByOneTimeLoginToken($token);

        $expired = false;
        if (!$user) {
            $expired = true;
            /*Yii::$app->getSession()->setFlash('warning',
                'Ce lien n\'est plus accessible, vous l\'avez déjà utilisé pour définir votre mot de passe.'
                . '<br>'
                . 'Veuillez vous connecter avec votre adresse email et le mot de passe que vous avez choisi.'
            );*/

            return $this->redirect(['/reset-password', 'token' => '', 'expired' => $expired]);
            //throw new BadRequestHttpException('Token is invalid');
        } else {
            Yii::$app->user->login($user);
            Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'Please set your password or you won\'t be able to login next time.'));

            $user->oneTimeLoginToken = null;
            $user->generatePasswordResetToken();
            $user->save(false);

            return $this->redirect(['/reset-password', 'token' => $user->passwordResetToken, 'expired' => $expired]);
        }

    }
}

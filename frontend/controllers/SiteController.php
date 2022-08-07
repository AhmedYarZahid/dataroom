<?php
namespace frontend\controllers;

use backend\modules\contact\models\Contact;
use backend\modules\contact\models\ContactNotify;
use backend\modules\contact\models\ContactThread;
use backend\modules\document\models\Document;
use backend\modules\document\models\DocumentSearch;
use backend\modules\news\models\News;
use backend\modules\notify\models\Notify;
use backend\modules\staticpage\models\StaticPage;
use lateos\trendypage\models\TrendyPage;
use lateos\trendypage\models\TrendyPageTestAB;
use lateos\formpage\models\FormPage;
use common\models\User;
use Yii;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\JobOffer;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\controllers\Controller as FrontendController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use common\helpers\FileHelper;

/**
 * Site controller
 */
class SiteController extends FrontendController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id == 'trendy-page-preview') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Home page
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->actionTrendyPage(1);
    }

    /**
     * Confirms email by specified link from email
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $code
     *
     * @return string
     * @throws HttpException in case no confirmation code provided
     */
    public function actionEmailConfirm($code)
    {
        $code = trim($code);

        if ($code === '' ) {
            throw new NotFoundHttpException(404, 'No confirmation code found.');
        }

        $confirmResult = 0;
        if ($userModel = User::findOne(['confirmationCode' => $code])) {
            if (!$userModel->isConfirmed) {
                $userModel->isConfirmed = 1;
                $userModel->isActive = 1;
                $userModel->historyComment = Yii::t('admin', 'User confirmed his account');

                $userModel->save(false);

                $confirmResult = 1;
            } else {
                $confirmResult = 2;
            }
        }

        return $this->render('email-confirm', array(
            'confirmResult' => $confirmResult,
        ));
    }

    /**
     * Show static page
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @param bool $json
     * @return string
     * @throws HttpException
     */
    public function actionPage($id, $json = false)
    {
        if (!$page = StaticPage::getPage($id)) {
            throw new HttpException(404, 'Page not found.');
        }

        if ($json) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => $page->title,
                'content' => $page->body
            ];
        } else {
            return $this->render('/site/page', [
                'page' => $page
            ]);
        }
    }

    /**
     * Show trendy page
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @param int $id
     * @param bool $json
     * @return string
     * @throws HttpException
     */
    public function actionTrendyPage($id, $json = false)
    {
		


        if (!$page = TrendyPage::getPage($id)) {
            throw new HttpException(404, 'Page not found.');
        }

        $test = TrendyPageTestAB::find()->where(['trendyPageLangID' => $page->translation->id])->one();
        if ($test && $test->isActive) {
            $revisionID = $test->revisionAID;

            if (rand(0, 1) == 1) {
                $revisionID = $test->revisionBID;
            }

            $testPage = TrendyPageTestAB::getRevision($revisionID);
            if ($testPage) {
                $page = $testPage;
            }
        }

        if ($json) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => $page->title,
                'content' => $page->body
            ];
        } else {
            return $this->render('/site/trendy-page', [
                'page' => $page
            ]);
        }
    }

    /**
     * Preview trendy page
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @return mixed
     */
    public function actionTrendyPagePreview()
    {
        // @todo make available for for admins only

        $body = '<h1 style="text-align: center;">An Error Occurred, please contact developer team</h1>';
        if (isset($_POST['body'])) {
            $body = $_POST['body'];
        }

        $page = (object) [
            'title' => 'Page Preview',
            'body' => $body,
        ];

        return $this->render('/site/trendy-page-preview', [
            'page' => $page
        ]);
    }

    /**
     * Show form page
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @param int $id
     * @return string
     * @throws HttpException
     */
    public function actionFormPage($id)
    {
        if (!$page = FormPage::getPage($id)) {
            throw new HttpException(404, 'Page not found.');
        }

        $formModel = $page->getFormModel();

        if ($formModel->load(Yii::$app->request->post())) {
            $page->processFileFields($formModel);

            if ($formModel->validate()) {
                $formResultsModel = $page->saveFormResult($formModel);
                if ($formResultsModel) {
                    Notify::sendFormPageAdminNotify($page, FormPage::getFormResultById($formResultsModel->id));
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for participating in the survey'));

                    return $this->goHome();
                }

                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'An error occurred. Please try again later'));
            }
        }

        return $this->render('/site/form-page', [
            'page' => $page,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Send email to admin (Contact Us)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public function actionContact($type = null)
    {
        if ($type == 'dataroom') {
            $this->layout = '@app/modules/dataroom/views/layouts/main';
        }

        $model = new Contact();

        if ($model->load(Yii::$app->request->post())) {
            $submitted = false;

            $model->toUserID = Contact::DUMMY_ADMIN;
            $model->fromUserID = Yii::$app->user->isGuest ? Contact::DUMMY_GUEST : Yii::$app->user->identity->getId();
            $model->generateCode();

            if ($model->save()) {
                $document = Yii::$app->documentManager->createFromContact($model, 'attachment', Document::TYPE_CONTACT);

                if ($model->subscribe) {
                    Yii::$app->newsletterManager->createFromContactForm($model);
                }

                if (ContactNotify::sendNewContactMessage($model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
                    $submitted = true;
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'There was an error sending email. Please try to contact us one more time.'));
                }
            }

            return $this->render('_contact-form', [
                'model' => $model,
                'submitted' => $submitted,
            ]);
        }

        return $this->render('contact', [
            'model' => $model,
            'submitted' => false,
            'type' => $type,
        ]);
    }

    /**
     * Send email to admin about resume (Contact us about resume)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @return array
     */
    public function actionContactResume($id = null)
    {
        $offer = null;
        if ($id) {
            $offer = JobOffer::find()
                ->removed(false)
                ->published()
                ->andWhere(['id' => $id])
                ->one();
        }

        $model = new Contact();
        $model->scenario = 'contact-resume';

        if ($model->load(Yii::$app->request->post())) {
            $model->toUserID = Contact::DUMMY_ADMIN;
            $model->fromUserID = Yii::$app->user->isGuest ? Contact::DUMMY_GUEST : Yii::$app->user->identity->getId();
            $model->generateCode();
            $model->type = Contact::TYPE_RESUME;

            if ($model->save()) {

                $model->refresh();

                $resume = Yii::$app->documentManager->createFromContact($model, 'resume', Document::TYPE_RESUME);
                $coverLetter = Yii::$app->documentManager->createFromContact($model, 'coverLetter', Document::TYPE_COVER_LETTER);

                if (ContactNotify::sendNewContactMessage($model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'There was an error sending email. Please try to contact us one more time.'));
                }

                return $this->redirect(['/job-offer']);
            }
        } elseif ($offer) {
            $model->subject = $offer->title;
        }

        return $this->render('contact-resume', [
            'model' => $model,
        ]);
    }

    /**
     * Send new message in contact thread (message to admin)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @param string $code
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionContactReply($id, $code)
    {
        if (!$model = Contact::find()->andWhere(['id' => $id, 'code' => trim($code)/*, 'isClosed' => 0*/])->one()) {
            throw new NotFoundHttpException("Page not found");
        }

        $answerModel = new ContactThread();
        if (!$model->isClosed && $answerModel->load(Yii::$app->request->post())) {

            $answerModel->contactID = $model->id;
            $answerModel->sender = ContactThread::SENDER_USER;
            $answerModel->createdDate = date('Y-m-d H:i:s');
            $answerModel->isLastMessage = 1;

            if ($answerModel->validate()) {
                if (ContactNotify::sendNewUserReply($answerModel)) {
                    $answerModel->save(false);
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'There was an error sending email.'));
                }

                return $this->refresh();
            }
        }

        return $this->render('contact-reply', [
            'model' => $model,
            'answerModel' => $answerModel,
        ]);
    }

    /**
     * FAQ page
     *
     * @return string
     */
    public function actionFaq()
    {
        return $this->render('faq', [
        ]);
    }

    /**
     * Download document
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param int $id
     * @throws NotFoundHttpException
     */
    public function actionDownloadDocument($id)
    {
        if (!$model = Document::find()->published()->andWhere(['type' => Document::TYPE_REGULAR, 'id' => $id])->one()) {
            throw new NotFoundHttpException('File not found.');
        }

        Yii::$app->response->sendFile($model->getDocumentPath(), $model->title . '.' . pathinfo($model->getDocumentPath(), PATHINFO_EXTENSION));
    }
}

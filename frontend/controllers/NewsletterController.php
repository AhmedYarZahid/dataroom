<?php
namespace frontend\controllers;

use backend\modules\mailing\models\MailingContact;
use common\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\controllers\Controller as FrontendController;
use frontend\widgets\newsletter\Form;
use common\models\Newsletter;

class NewsletterController extends FrontendController
{
    public $defaultAction = 'add';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add' => ['post'],
                ],
            ],
        ];
    }

    public function actionAdd()
    {   
        $submitted = false;

        $model = new Newsletter;
        $model->scenario = Newsletter::SCENARIO_NEWSLETTER_FORM;

        if ($model->load(Yii::$app->request->post())) {

            // Re-activate in case email already in DB
            if ($inactiveModel = Newsletter::findOne(['email' => $model->email, 'isActive' => 0])) {
                $model = clone $inactiveModel;
                $model->scenario = Newsletter::SCENARIO_NEWSLETTER_FORM;;
                $model->load(Yii::$app->request->post());
                $model->isActive = 1;
            }

            if ($model->save()) {
                $submitted = true;
                Yii::$app->session->setFlash('success', Yii::t('app', 'Thank you for subscription!'));
            }
        }

        return Form::widget([
            'newsletterModel' => $model,
            'submitted' => $submitted,
        ]);
    }

    /**
     * Unsubscribe from newsletters
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     *
     * @param string $code
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionUnsubscribe($code)
    {
        if (!$model = MailingContact::findOne(['code' => $code])) {
            throw new BadRequestHttpException();
        }

        if ($model->user) {
            $model->user->mailingUnsubscribe();
        } elseif ($model->newsletter) {
            $model->newsletter->isActive = 0;
            $model->newsletter->save(false);
        }

        return $this->render('unsubscribe');
    }

    /**
     * Unsubscribe from newsletters (for authorized users)
     *
     * @author Vadim Bulochnik <vadim.bulochnik@gmail.com>
     */
    public function actionUnsubscribeAuthorizedUser($code)
    {
        if (!$model = User::findOne(['confirmationCode' => $code])) {
            throw new BadRequestHttpException();
        }

        $model->mailingUnsubscribe();

        return $this->render('unsubscribe');
    }
}

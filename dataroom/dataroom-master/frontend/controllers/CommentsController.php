<?php
namespace frontend\controllers;

use Yii;
use backend\modules\comments\models\CommentBundle;
use backend\modules\comments\models\Comment;
use backend\modules\notify\models\Notify;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\Response;
use frontend\controllers\Controller as FrontendController;

/**
 * Comments controller
 */
class CommentsController extends FrontendController
{
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
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Add comment
     *
     * @author Petr Dvukhrechensky <petr.sdkb@gmail.com>
     *
     * @param int $id
     * @param boolean $json
     * @return string
     * @throws HttpException
     */
    public function actionAdd($id, $json = false)
    {
        $response = [
            'status' => 'error',
            'errors' => [],
        ];

        $commentBundle = CommentBundle::findOne($id);

        if ($commentBundle) {
            $comment = new Comment();
            $comment->loadDefaultValues();

            $comment->load(Yii::$app->request->post());

            if ($comment->validate()) {
                if ($comment->save(false)) {
                    $comment->refresh();

                    $response['status'] = 'success';
                    $response['data'] = $comment;

                    Notify::sendCommentAdminNotify($commentBundle, $comment);
                } else {
                    $response['errors'] = $comment->errors;
                    $response['errors'][] = 'Database error';
                }
            } else {
                $response['errors'] = $comment->errors;
            }
        } else {
            $response['errors'][] = 'No bundle';
        }

        if ($json) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $response;
        }

        return $this->goBack();
    }
}

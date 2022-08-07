<?php

namespace backend\modules\parameter\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use backend\modules\parameter\models\Parameter;
use backend\modules\parameter\models\ParameterSearch;
use yii\helpers\Json;
use yii\filters\AccessControl;


class ManageController extends \backend\controllers\Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Parameters');
        $this->titleSmall = Yii::t('admin', 'Manage global system parameters');

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
                        'roles' => ['superadmin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Parameters list
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ParameterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Validate if there is a editable input saved via AJAX
        if (Yii::$app->request->post('hasEditable')) {
            
            $model = Parameter::findOne(Yii::$app->request->post('editableKey'));

            // store a default json response as desired by editable
            $out = Json::encode(['output' => '', 'message' => '']);

            $post = [];
            $posted = current($_POST['Parameter']);
            $post['Parameter'] = $posted;

            // load model like any single model validation
            if ($model->load($post)) {

                $output = '';

                // can save model or do something before saving model
                $model->save();

                $out = Json::encode(['output' => $output, 'message' => '']);
            }

            return $out;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Parameter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Parameter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Parameter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
<?php

namespace backend\controllers;

use Yii;
use common\models\Menu;
use common\models\MenuSearch;
use backend\controllers\Controller;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->title = Yii::t('admin', 'Public Menu');
        $this->titleSmall = Yii::t('admin', 'Manage public menu');

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
     * Menu tree
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'menuTree' => Menu::getTree(false, null, null, 'menu-editor')
        ]);
    }

    /**
     * Displays a single Menu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Saves menu items order
     * @return mixed
     */
    public function actionSaveOrder()
    {
        $menuItems = Yii::$app->request->post('menuItems');
        $response = [
            'status' => 'success',
            'errors' => [],
            'data' => [],
        ];

        if (is_array($menuItems)) {
            foreach ($menuItems as $key => $value) {
                try {
                    $model = $this->findModel($value['id']);
                    $model->rank = $key * 10;

                    // If parent was changed
                    if (!empty($value['parentId'])) {
                        if ($value['parentId'] !== $model->parentID) {
                            $parentModel = $this->findModel($value['parentId']);
                            $model->parentID = $parentModel->id;
                        }
                    } else {
                        if ($model->parentID > 0) {
                            $model->parentID = null;
                        }
                    }

                    if (!$model->save(false)) {
                        $response['status'] = 'error';
                        $response['errors'][] = 'Database error';
                    }
                } catch(Exception $e) {
                    $response['status'] = 'error';
                    $response['errors'][] = 'Menu item or its parent do not exist';
                }
            }
        } else {
            $response['status'] = 'error';
            $response['errors'][] = 'Nothing to save';
        }

        $response['data'] = Menu::getTree(false, null, null, 'menu-editor');

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();
        $model->rank = 100;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id, true);
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Menu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param boolean $json
     * @return mixed
     */
    public function actionDelete($id, $json = false)
    {
        $this->findModel($id)->delete();

        if ($json) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'status' => 'success',
                'data' => Menu::getTree(false, null, null, 'menu-editor'),
            ];
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggles isActive property of Menu model.
     * @param integer $id
     * @return mixed
     */
    public function actionToggleIsActive($id)
    {
        $response = [
            'status' => 'success',
            'errors' => [],
        ];

        $model = $this->findModel($id);
        $model->isActive = 1 - $model->isActive;

        if ($model->save(false)) {
            $response['data'] = Menu::getTree(false, null, null, 'menu-editor');
        } else {
            $response['status'] = 'error';
            $response['errors'][] = 'Database error';
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @param bool $multilingual
     * @return Menu the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $multilingual = false)
    {
        $query = Menu::find();
        $query->where(['id' => $id]);

        if ($multilingual) {
            $query->multilingual();
        }

        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

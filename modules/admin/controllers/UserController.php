<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * UserController implements user management functionality.
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'block' => ['POST'],
                    'unblock' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ],
        ]);

        // Filter by status if provided
        $status = Yii::$app->request->get('status');
        if ($status !== null) {
            $dataProvider->query->andWhere(['status' => $status]);
        }

        // Search by email
        $search = Yii::$app->request->get('search');
        if ($search) {
            $dataProvider->query->andWhere(['like', 'email', $search]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'currentStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get user's recent orders
        $recentOrders = Order::find()
            ->where(['user_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('view', [
            'model' => $model,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new User();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Set password if provided
                $password = $this->request->post('User')['password'] ?? '';
                if ($password) {
                    $model->setPassword($password);
                    $model->generateAuthKey();
                }
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'User created successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Update password if provided
            $password = $this->request->post('User')['password'] ?? '';
            if ($password) {
                $model->setPassword($password);
                $model->generateAuthKey();
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Blocks a user account.
     * @param int $id User ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot block your own account.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $model->status = User::STATUS_INACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'User blocked successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to block user.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Unblocks a user account.
     * @param int $id User ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUnblock($id)
    {
        $model = $this->findModel($id);
        
        $model->status = User::STATUS_ACTIVE;
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'User unblocked successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to unblock user.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        // Check if user has orders
        if (Order::find()->where(['user_id' => $id])->exists()) {
            // Soft delete by setting status to deleted
            $model->status = User::STATUS_DELETED;
            $model->save();
            Yii::$app->session->setFlash('success', 'User account deactivated (has existing orders).');
        } else {
            // Hard delete if no orders
            $model->delete();
            Yii::$app->session->setFlash('success', 'User deleted successfully.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Export users to CSV
     * @return Response
     */
    public function actionExport()
    {
        $query = User::find();
        
        // Apply filters
        $status = Yii::$app->request->get('status');
        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }
        
        $users = $query->all();
        
        // Generate CSV
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/csv');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'ID', 'Email', 'Status', 'Created At', 'Updated At'
        ]);
        
        // CSV data
        foreach ($users as $user) {
            fputcsv($output, [
                $user->id,
                $user->email,
                $user->status,
                $user->created_at,
                $user->updated_at
            ]);
        }
        
        fclose($output);
        return Yii::$app->response;
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

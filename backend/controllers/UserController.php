<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserAddress;
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
        $searchModel = new \yii\base\DynamicModel(['email', 'status', 'created_at_from', 'created_at_to']);
        $searchModel->addRule(['email'], 'string');
        $searchModel->addRule(['status'], 'integer');
        $searchModel->addRule(['created_at_from', 'created_at_to'], 'date');

        $query = User::find();

        if ($searchModel->load(Yii::$app->request->queryParams) && $searchModel->validate()) {
            if (!empty($searchModel->email)) {
                $query->andFilterWhere(['like', 'email', $searchModel->email]);
            }
            if ($searchModel->status !== null) {
                $query->andFilterWhere(['status' => $searchModel->status]);
            }
            if (!empty($searchModel->created_at_from)) {
                $query->andFilterWhere(['>=', 'created_at', $searchModel->created_at_from]);
            }
            if (!empty($searchModel->created_at_to)) {
                $query->andFilterWhere(['<=', 'created_at', $searchModel->created_at_to . ' 23:59:59']);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 25,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        
        // Get user addresses
        $addresses = UserAddress::find()->where(['user_id' => $id])->all();
        
        // Get user orders
        $ordersProvider = new ActiveDataProvider([
            'query' => \common\models\Order::find()->where(['user_id' => $id])->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('view', [
            'model' => $model,
            'addresses' => $addresses,
            'ordersProvider' => $ordersProvider,
        ]);
    }

    /**
     * Updates an existing User model.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'User updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Blocks a user.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionBlock($id)
    {
        $model = $this->findModel($id);
        $model->status = 0; // Blocked
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'User has been blocked.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to block user.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Unblocks a user.
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUnblock($id)
    {
        $model = $this->findModel($id);
        $model->status = 1; // Active
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'User has been unblocked.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to unblock user.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Export users to CSV.
     * @return Response
     */
    public function actionExport()
    {
        $users = User::find()->all();
        
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // CSV header
        fputcsv($output, ['ID', 'Email', 'Phone', 'Status', 'Email Verified', 'Phone Verified', 'Created At']);
        
        // CSV data
        foreach ($users as $user) {
            fputcsv($output, [
                $user->id,
                $user->email,
                $user->phone,
                $user->status ? 'Active' : 'Blocked',
                $user->email_verified ? 'Yes' : 'No',
                $user->phone_verified ? 'Yes' : 'No',
                $user->created_at,
            ]);
        }
        
        fclose($output);
        exit;
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

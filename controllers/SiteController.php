<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->login()) {
                return $this->goBack();
            }
            Yii::$app->session->setFlash('error', Html::errorSummary($model));
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->user->login($model);
                return $this->goHome();
            }
            Yii::$app->session->setFlash('danger', current($model->getFirstErrors()));
        }
        return $this->render('register', ['model' => $model]);
    }

    /**
     * @param $id
     * @param $token
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionReset($id, $token)
    {
        $model = ResetForm::find()->andWhere(['id' => $id, 'reset_password_token' => $token])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('errors', 'User Not Found'));
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Password Was Changed'));
                Yii::$app->user->login($model);
                return $this->goHome();
            }
            Yii::$app->session->setFlash('danger', Html::errorSummary($model));
        }
        return $this->render('reset', ['model' => $model]);
    }

    public function actionForget()
    {
        $model = new ForgetForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('messages', 'Check Your Email'));
                return $this->refresh();
            }
            Yii::$app->session->setFlash('danger', Html::errorSummary($model));
        }
        return $this->render('forget', ['model' => $model]);
    }

    /**
     * @param $id
     * @param $confirm_token
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id, $confirm_token)
    {
        $user = User::find()->andWhere(['id' => $id, 'confirm_token' => $confirm_token])->one();
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('errors', 'User Not Found'));
        }
        $user->confirm_token = null;
        $user->confirmed_at = new Expression('NOW()');
        $user->save();

        Yii::$app->session->setFlash('success', Yii::t('messages', 'Confirmation Successful'));
        return $this->redirect(['/']);
    }
}

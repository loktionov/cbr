<?php

namespace app\controllers;

use app\models\Valutes;
use app\models\ValutesDaily;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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

    public function actionIndex()
    {
        $res = '';
        $range = [];
        if (
            !empty($_POST['v1']) AND !empty($_POST['v2']) AND !empty($_POST['date'])
        ) {
            $d = date('Y-m-d', strtotime($_POST['date']));
            $vd1 = ValutesDaily::find()
                ->where('vcode=:id and requestdate=:date', [':id' => $_POST['v1'], ':date' => $d])->one();
            $vd2 = ValutesDaily::find()
                ->where('vcode=:id and requestdate=:date', [':id' => $_POST['v2'], ':date' => $d])->one();
            $sum = empty($_POST['sum']) ? 1 : $_POST['sum'];
            $res = round(($vd1->Vcurs / $vd2->Vcurs) * $sum, 4);
        }
        if (!empty($_POST['date_range']) AND !empty($_POST['v3'])) {
            $d = explode(' - ', $_POST['date_range']);
            $query = new Query();
            $query->select("vcurs, requestdate")
                ->from('valutes_daily')
                ->where('vcode=:code and requestdate between :d1 and :d2',
                    [':code' => $_POST['v3'], ':d1' => $d[0], 'd2' => $d[1]])
                ->orderBy('requestdate');
            $command = $query->createCommand();
            $data = $command->queryAll();

            foreach ($data as $d) {
                $range[] = [(int)(strtotime($d['requestdate'])+10800)*1000, (float)$d['vcurs']];
            }
        }
        return $this->render('index', ['res' => $res, 'range' => $range]);
    }


    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}

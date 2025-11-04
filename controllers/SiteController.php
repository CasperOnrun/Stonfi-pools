<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * Обработчик ошибок
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
     * Главная страница - редирект на список пулов
     */
    public function actionIndex()
    {
        return $this->redirect(['pool/index']);
    }
}


<?php
namespace app\controllers;

use app\components\Controller;
use app\enums\Rol;

class DannyePedrabotnikaController extends Controller
{
    public function actionLichnyeDannye()
    {
        return $this->render('lichnye-dannye');
    }

    public function actionObrazovanie()
    {
        return $this->render('obrazovanie');
    }

    public function actionRabota()
    {
        return $this->render('rabota');
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::PEDAGOGICHESKIJ_RABOTNIK
        ];
    }
}

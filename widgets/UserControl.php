<?php

namespace app\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class UserControl extends Widget
{
    public $user;
    public $db;

    public function run()
    {
        ob_start();

        echo Html::beginTag('div', ['class' => 'usercontrol']);

        if (!$this->user->isGuest) {
            /**
             * @var $polzovatel \app\entities\Polzovatel
             */
            $polzovatel = $this->user->identity;
            $fiz_lico = $polzovatel->fizLicoRel;

            $username = "$fiz_lico->familiya $fiz_lico->imya $fiz_lico->otchestvo";
        } else {
            $username = "Гость";
        }

        echo Html::tag('div', $username, ['class' => 'usercontrol-username']);

        echo Html::beginTag('ul', ['class' => 'usercontrol-actions']);

        if (!$this->user->isGuest) {
            echo $this->renderAction('/polzovatel/vyhod', 'Выход');
        } else {
            echo $this->renderAction('/polzovatel/registraciya', 'Регистрация');
            echo $this->renderAction('/polzovatel/vhod', 'Вход');
        }

        echo Html::endTag('ul');

        echo Html::endTag('div');

        return ob_get_clean();
    }

    private function renderAction($actionId, $caption)
    {
        return "<li><a href=\"$actionId\">$caption</a></li>";
    }
}

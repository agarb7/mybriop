<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use app\modules\upravlenie_kursami\modules\raspisanie\widgets\PrepodavatelPeresechenieAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class PrepodavatelPeresechenieModal extends Modal
{
    /**
     * @var array
     */
    public $prepodavatelPeresechenieAction;
    
    public function init()
    {
        $this->options['class'] = 'prepodavatel-peresechenie-modal fade';
        $this->header = Html::tag('h4', 'Пересечение занятости преподавателя');
        $this->footer = Html::tag('a', 'Ок', ['class' => 'btn btn-primary ok-btn', 'data-dismiss' => 'modal']);

        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();

        return parent::run();
    }
    
    private function registerClientScript()
    {
        PrepodavatelPeresechenieAsset::register($this->getView());

        $options = Json::htmlEncode([
            'url' => Url::to($this->prepodavatelPeresechenieAction)
        ]);

        $this->getView()->registerJs('$("#' . $this->getId() . '").prepodavatelPeresechenieModal(' . $options . ');');
    }
}
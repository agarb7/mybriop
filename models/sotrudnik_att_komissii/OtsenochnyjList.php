<?php

namespace app\models\sotrudnik_att_komissii;

use \yii\base\Model;
use yii\helpers\BaseInflector;

class OtsenochnyjList extends Model
{
    public $ispytanieName;
    public $fileName;
    public $fileLink;
    public $id;
    public $rabotnikKomissii;
    public $zayavlenieNaAttestaciyu;
    public $varIspytanie_3;
    public $postoyannoeIspytanie;
    public $nazvanie;
    public $minBallPervayaKategoriya;
    public $minBallVisshayaKategoriya;
    public $otsenochnijList;
    public $struktura;
    public $list;

    public function __construct(array $config)
    {
        parent::init();
        $this->ispytanieName = $config['ispytanie_name'];
        $this->fileName = $config['file_name'];
        $this->fileLink = $config['file_link'];
        $this->struktura = $config['struktura'];
        $this->list = $config['list'];
        $this->id = $config['list']->id;
        $this->rabotnikKomissii = $config['list']->rabotnikKomissii;
        $this->zayavlenieNaAttestaciyu = $config['list']->zayavlenieNaAttestaciyu;
        $this->varIspytanie_3 = $config['list']->varIspytanie_3;
        $this->postoyannoeIspytanie = $config['list']->postoyannoeIspytanie;
        $this->nazvanie = $config['list']->nazvanie;
        $this->minBallPervayaKategoriya = $config['list']->minBallPervayaKategoriya;
        $this->minBallVisshayaKategoriya = $config['list']->minBallVisshayaKategoriya;
        $this->otsenochnijList = $config['list']->otsenochnijList;
    }


}
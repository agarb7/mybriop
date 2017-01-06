<?php

namespace app\modules\attestaciya_otchety\models;

/**
 * Class AttestaciyaItogovyjOtchet
 * @property int id,
 * @property int type_id
 * @property string fio,
 * @property string organizaciya,
 * @property string dolzhnost,
 * @property date data_rozhdeniya,
 * @property string imeushayasya_kategoriya,
 * @property string kategoriya,
 * @property int ped_stazh,
 * @property int rabota_stazh_v_dolzhnosti,
 * @property int stazh_v_dolzhnosti,
 * @property string obrazovanie,
 * @property string kursy
 * @property float avg_var_isp
 * @property float avg_portfolio
 * @property float avg_spd
 * @property string zakluchenie
 */
class AttestaciyaItogovyjOtchet extends \app\base\ActiveRecord
{
    public $id;
    public $type_id;
    public $fio;
    public $organizaciya;
    public $dolzhnost;
    public $data_rozhdeniya;
    public $imeushayasya_kategoriya;
    public $kategoriya;
    public $ped_stazh;
    public $rabota_stazh_v_dolzhnosti;
    public $stazh_v_dolzhnosti;
    public $obrazovanie;
    public $kurs;
    public $avg_var_isp;
    public $avg_portfolio;
    public $avg_sp;
    public $zaklucheni;
}
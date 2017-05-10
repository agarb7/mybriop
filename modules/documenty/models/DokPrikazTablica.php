<?php
namespace app\modules\documenty\models;

use app\records\FizLico;
use app\records\KursFizLica;
use yii\db\ActiveRecord;

class DokPrikazTablica extends ActiveRecord
{
    public function rules()
    {
        return [
            [['prikaz_id','kurs_fiz_lica_id','fiz_lico_id'],'integer'],
        ];
    }

    public function getFizLico()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico_id']);
    }

    public function getKursFizLica()
    {
        return $this->hasOne(KursFizLica::className(), ['id' => 'kurs_fiz_lica']);
    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->via('kursFizLica');
    }
}
?>
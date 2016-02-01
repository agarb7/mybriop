<?php

namespace app\models\podrazdel_kursa;

use Yii;
use app\globals\ApiGlobals;

/**
 * This is the model class for table "podrazdel_kursa".
 *
 * @property integer $id
 * @property integer $razdel
 * @property string $nomer
 * @property string $nazvanie
 * @property integer $raschitano_chasov_lekcyj
 * @property integer $raschitano_chasov_praktik
 * @property integer $raschitano_chasov_srs
 * @property integer $forma_kontrolya
 * @property integer $chasy_kontrolya
 * @property integer $rukovoditel
 * @property string $aktualnost
 * @property string $cel
 * @property string $zadachi
 * @property string $planiruemye_rezultaty
 * @property string $mesto_discipliny_v_strukture_programmy
 * @property string $informacionnye_usloviya
 * @property string $uchebnometodicheskie_usloviya
 * @property string $kadrovye_usloviya
 * @property string $materialnotehnicheskie_usloviya
 * @property string $literatura
 * @property integer $status
 * @property integer $nedelya_nachalo
 * @property integer $nedelya_konec
 * @property boolean $rukovoditel_vakansiya
 */
class PodrazdelKursa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'podrazdel_kursa';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->aktualnost = ApiGlobals::to_trimmed_text($this->aktualnost);
            $this->cel = ApiGlobals::to_trimmed_text($this->cel);
            $this->zadachi = ApiGlobals::to_trimmed_text($this->zadachi);
            $this->planiruemye_rezultaty = ApiGlobals::to_trimmed_text($this->planiruemye_rezultaty);
            $this->informacionnye_usloviya = ApiGlobals::to_trimmed_text($this->informacionnye_usloviya);
            $this->kadrovye_usloviya = ApiGlobals::to_trimmed_text($this->kadrovye_usloviya);
            $this->uchebnometodicheskie_usloviya = ApiGlobals::to_trimmed_text($this->uchebnometodicheskie_usloviya);
            $this->materialnotehnicheskie_usloviya = ApiGlobals::to_trimmed_text($this->materialnotehnicheskie_usloviya);
            $this->mesto_discipliny_v_strukture_programmy = ApiGlobals::to_trimmed_text($this->mesto_discipliny_v_strukture_programmy);
            $this->literatura = ApiGlobals::to_trimmed_text($this->literatura);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['razdel', 'nomer', 'nazvanie'], 'required'],
            //[['razdel', 'raschitano_chasov_lekcyj', 'raschitano_chasov_praktik', 'raschitano_chasov_srs', 'forma_kontrolya', 'chasy_kontrolya', 'rukovoditel'], 'integer'],
            [['aktualnost', 'cel', 'zadachi', 'planiruemye_rezultaty', 'mesto_discipliny_v_strukture_programmy', 'informacionnye_usloviya', 'uchebnometodicheskie_usloviya', 'kadrovye_usloviya', 'materialnotehnicheskie_usloviya','raschitano_chasov_lekcyj', 'raschitano_chasov_praktik', 'raschitano_chasov_srs','literatura','status'], 'safe'],
            //[['nazvanie'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'razdel' => 'Razdel',
            'nomer' => 'Номер',
            'nazvanie' => 'Название',
            'raschitano_chasov_lekcyj' => 'Количество лекционных часов',
            'raschitano_chasov_praktik' => 'Количество практических часов',
            'raschitano_chasov_srs' => 'Количество часов СРС',
            'forma_kontrolya' => 'Forma Kontrolya',
            'chasy_kontrolya' => 'Chasy Kontrolya',
            'rukovoditel' => 'Rukovoditel',
            'aktualnost' => 'Актуальность',
            'cel' => 'Цель',
            'zadachi' => 'Задачи',
            'planiruemye_rezultaty' => 'Планируемые результаты',
            'mesto_discipliny_v_strukture_programmy' => 'Место дисциплины в структуре программы',
            'informacionnye_usloviya' => 'Информационные условия',
            'uchebnometodicheskie_usloviya' => 'Учебно-методические условия',
            'kadrovye_usloviya' => 'Кадровые условия',
            'materialnotehnicheskie_usloviya' => 'Метриально-технические условия',
            'literatura'=>'Литература',
            'status'=>'Статус'
        ];
    }
}

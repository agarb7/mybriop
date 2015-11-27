<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 23.02.15
 * Time: 21:17
 */

namespace app\models\Kurs;

use app\models\Kurs\KategoriyaSlushatelyaRecord;
use yii\db\ActiveRecord;
use app\globals\ApiGlobals;

class KursRecord extends ActiveRecord {
    public static function tableName(){
        return 'kurs';
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->annotaciya = ApiGlobals::to_trimmed_text($this->annotaciya);
            $this->aktualnost = ApiGlobals::to_trimmed_text($this->aktualnost);
            $this->cel = ApiGlobals::to_trimmed_text($this->cel);
            $this->zadachi = ApiGlobals::to_trimmed_text($this->zadachi);
            $this->planiruemye_rezultaty = ApiGlobals::to_trimmed_text($this->planiruemye_rezultaty);
            $this->informacionnye_usloviya = ApiGlobals::to_trimmed_text($this->informacionnye_usloviya);
            $this->kadrovye_usloviya = ApiGlobals::to_trimmed_text($this->kadrovye_usloviya);
            $this->uchebnometodicheskie_usloviya = ApiGlobals::to_trimmed_text($this->uchebnometodicheskie_usloviya);
            $this->tehnicheskie_usloviya = ApiGlobals::to_trimmed_text($this->tehnicheskie_usloviya);
            $this->spisok_literatury = ApiGlobals::to_trimmed_text($this->spisok_literatury);
            $this->itogovaya_attestaciya = ApiGlobals::to_trimmed_text($this->itogovaya_attestaciya);
            $this->harakteristika_novoj_kvalifikacii = ApiGlobals::to_trimmed_text($this->harakteristika_novoj_kvalifikacii);
            $this->trebovaniya_k_urovnyu_podgotovki = ApiGlobals::to_trimmed_text($this->trebovaniya_k_urovnyu_podgotovki);
            $this->forma_obucheniya = ApiGlobals::to_trimmed_text($this->forma_obucheniya);
            $this->rezhim_zanyatij = ApiGlobals::to_trimmed_text($this->rezhim_zanyatij);
            $this->harakteristika_novogo_vida_deyatelnosti = ApiGlobals::to_trimmed_text($this->harakteristika_novogo_vida_deyatelnosti);
            $this->sostaviteli = ApiGlobals::to_trimmed_text($this->sostaviteli);
            $this->recenzenti = ApiGlobals::to_trimmed_text($this->recenzenti);
            $this->itogovaya_attestaciya_tekst = ApiGlobals::to_trimmed_text($this->itogovaya_attestaciya_tekst);
            return true;
        } else {
            return false;
        }
    }

    public function attributeLabels(){
        return [
            'nazvanie' => 'Название',
            'annotaciya' => 'Аннотация',
            'aktualnost' => 'Актуальность',
            'cel' => 'Цель',
            'zadachi' => 'Задачи',
            'planiruemye_rezultaty' => 'Планируемые результаты',
            'informacionnye_usloviya' => 'Информационные условия',
            'kadrovye_usloviya' => 'Кадровые условия',
            'uchebnometodicheskie_usloviya' => 'Учебно-методические условия',
            'tehnicheskie_usloviya' => 'Материально-технические условия',
            'spisok_literatury'=> 'Литература',
            'finansirovanie' => 'Финасирование',
            'raschitano_chasov' => 'Количество часов',
            'raschitano_slushatelej' => 'Количество слушателей',
            'status_programmy' => 'Подписать программу',
            'itogovaya_attestaciya' => 'Итоговая аттестация',
            'harakteristika_novoj_kvalifikacii' => 'Характеристика новой квалификации',
            'trebovaniya_k_urovnyu_podgotovki' => 'Требования к уровню подготовки',
            'forma_obucheniya' => 'Форма обучения',
            'rezhim_zanyatij' => 'Режим занятий',
            'harakteristika_novogo_vida_deyatelnosti' => 'Характеристика нового вида деятельности',
            'sostaviteli' => 'Составители',
            'recenzenti' => ' Рецензенты',
            'itogovaya_attestaciya_tekst' => 'Итоговая аттестация'
        ];
    }
    
    public function rules(){
            return [
//                [[
//                    'nazvanie','annotaciya','aktualnost','cel','zadachi','planiruemye_rezultaty',
//                    'informacionnye_usloviya','kadrovye_usloviya','uchebnometodicheskie_usloviya',
//                    'tehnicheskie_usloviya', 'spisok_literatury','itogovaya_attestaciya','harakteristika_novoj_kvalifikacii',
//                    'trebovaniya_k_urovnyu_podgotovki','forma_obucheniya','rezhim_zanyatij'
//                 ]
//                 ,'string'
//                ],
//                [
//                    [
//                        'nazvanie','annotaciya','aktualnost','cel','zadachi','planiruemye_rezultaty',
//                        'informacionnye_usloviya','kadrovye_usloviya','uchebnometodicheskie_usloviya',
//                        'tehnicheskie_usloviya', 'spisok_literatury','tip','status_programmy'
//                    ], 'default'
//                ],
                [['id'],'required'],
                [[
                    'nazvanie','annotaciya','aktualnost','cel','zadachi','planiruemye_rezultaty',
                    'informacionnye_usloviya','kadrovye_usloviya','uchebnometodicheskie_usloviya',
                    'tehnicheskie_usloviya', 'spisok_literatury','itogovaya_attestaciya','harakteristika_novoj_kvalifikacii',
                    'trebovaniya_k_urovnyu_podgotovki','forma_obucheniya','rezhim_zanyatij','id',
                    'harakteristika_novogo_vida_deyatelnosti','sostaviteli','recenzenti',
                    'itogovaya_attestaciya_tekst'
                ]
                    ,'safe'
                ],
            ];
    }

    public function getKategoriyaSlushatelyas()
    {
        return $this->hasMany(KategoriyaSlushatelyaRecord::className(), ['id' => 'kategoriya_slushatelya'])->viaTable('kategoriya_slushatelya_kursa', ['kurs' => 'id']);
    }
}
<?php
namespace app\upravlenie_kursami\raspisanie\models;

use app\behaviors\TransformationBehavior;

use app\upravlenie_kursami\models\Kurs;

class KursForm extends Kurs
{
    public function behaviors()
    {
        return [
            'transformations' => [
                'class' => TransformationBehavior::className(),
                'transformations' => [
                    [['raspisanie_nachalo', 'raspisanie_konec'], 'date']
                ]
            ]
        ];
    }

    //todo
    public function rules()
    {
        return [
            ['raspisanie_nachalo_input', 'required'],
            ['raspisanie_nachalo_input', 'date'],
            ['raspisanie_nachalo_input', 'validateNoZanyatij', 'params' => 'before'],

            ['raspisanie_konec_input', 'required'],
            ['raspisanie_konec_input', 'date'],
            ['raspisanie_konec_input', 'validateNoZanyatij', 'params' => 'after'],

            ['auditoriya_po_umolchaniyu', 'integer'] //todo
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'raspisanie_nachalo_input' => 'Проводится с',
            'raspisanie_konec_input' => 'по',
            'auditoriya_po_umolchaniyu' => 'Аудитория по умолчанию'
        ];
    }

    public function ensureRaspisanieDates()
    {
        if ($this->raspisanie_nachalo && $this->raspisanie_konec)
            return true;

        $nachalo = $this->raspisanie_nachalo;
        $konec = $this->raspisanie_konec;

        if (!$nachalo)
            $nachalo = $this->ochnoe_nachalo ?: $this->zaochnoe_nachalo;

        if (!$konec)
            $konec = $this->ochnoe_konec ?: $this->zaochnoe_konec;

        if (!$nachalo || !$konec)
            return false;

        $this->raspisanie_nachalo = $nachalo;
        $this->raspisanie_konec = $konec;

        return $this->save(false);
    }

    public function validateNoZanyatij($attribute, $params)
    {
        $op = $params === 'after' ? '>' : '<';
        $sourceAttribute = $this->getSourceAttribute($attribute);
        $date = $this->$sourceAttribute;

        $exists = $this->getZanyatiya_rel()
            ->where([$op, 'data', $date])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'За пределами этой даты назначены занятия.');
        }
    }
}

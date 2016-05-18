<?php
namespace app\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use app\enums2\Rol;

class Nav extends \yii\bootstrap\Nav
{
    public function init()
    {
        parent::init();

        $userId = Yii::$app->user->id;
        $roles = $userId ? Yii::$app->authManager->getRolesByUser($userId) : [];
        foreach ($roles as $role) {
            $items = $this->rolesMenuItems()[$role->name];
            $this->items = ArrayHelper::merge($this->items, $items);
        }
    }

    private function rolesMenuItems()
    {
        return [
//            Rol::ADMINISTRATOR,
            Rol::PEDAGOGICHESKIJ_RABOTNIK => $this->pedagogicheskijRabotnikMenuItems(),
            Rol::PREPODAVATEL_KURSOV => $this->prepodavatelKursovMenuItems(),
//            Rol::PROREKTOR_PO_OOD,
//            Rol::REKTOR,
            Rol::RUKOVODITEL_KURSOV => $this->rukovoditelKursovMenuItems(),
//            Rol::RUKOVODITEL_OBRAZOVATELNOGO_UCHREZHDENIYA,
//            Rol::RUKOVODITEL_STRUKTURNOGO_PODRAZDELENIYA,
            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA => $this->sotrudnikUchebnogoOtdelaMenuItems(),
            Rol::SOTRUDNIK_OTDELA_ATTESTACII => $this->sotrudnikOtdelaAttestaciiMenuItems(),
            ROL::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII => $this->rukovoditelAttestacionnojKomissiiMenuItems(),
            Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII => $this->sotrudnikAttestacionnojKomissiiMenuItems(),
            Rol::SOTRUDNIK_OTDELA_KADROV => $this->sotrudnikOtdelaKadrovMenuItems(),
        ];
    }

    private function myDataMenuItem()
    {
        return [
            'label' => 'Мои данные',
            'items' => [
                'common' => ['label' => 'Общие', 'url' => ['/lichnye-dannye-obschie/index']],
                'education' => ['label' => 'Образование', 'url' => ['/lichnye-dannye-obrazovanie/index']],
                'job' => ['label' => 'Работа', 'url' => ['/lichnye-dannye-rabota/index']],
                'password' => ['label' => 'Сменить пароль', 'url' => ['/lichnye-dannye-obschie/password']]
            ]
        ];
    }

    private function pedagogicheskijRabotnikMenuItems() {
        return [
            [
                'label' => 'Запись на курсы',
                'items' => [
                    ['label' => 'Курсы повышения квалификации', 'url' => ['/kurs-slushatelyu/zapis-na-kurs-pk']],
                    ['label' => 'Курсы профессиональной переподготовки', 'url' => ['/kurs-slushatelyu/zapis-na-kurs-pp']],
                    ['label' => 'Курсы профессионального обучения', 'url' => ['/kurs-slushatelyu/zapis-na-kurs-po']]
                ]
            ],
            ['label' => 'Мои курсы', 'url' => ['/kurs-slushatelyu/moi-kursy']],
            ['label' => 'Аттестация', 'url' => ['/attestaciya/']],
            'myData' => $this->myDataMenuItem()
        ];
    }

    private function prepodavatelKursovMenuItems() {
        return [
            ['label' => 'Мои курсы', 'url' => ['/kursy-rukovoditelya/spisok']],
            ['label' => 'Список дисциплин', 'url' => ['/kurs/spisok-discipline']],
            'myData' => $this->myDataMenuItem()
        ];
    }

    private function rukovoditelKursovMenuItems() {
        return [
            ['label' => 'Мои курсы', 'url' => ['/kursy-rukovoditelya/spisok']],
            ['label' => 'Список дисциплин', 'url' => ['/kurs/spisok-discipline']],
            'myData' => $this->myDataMenuItem()
        ];
    }

    private function sotrudnikUchebnogoOtdelaMenuItems() {
        return [
            [
                'label' => 'Курсы',
                'items' => [
                    ['label' => 'Курсы повышения квалификации', 'url' => ['/kursy/spisok-pk']],
                    ['label' => 'Курсы профессиональной переподготовки', 'url' => ['/kursy/spisok-pp']],
                    ['label' => 'Курсы профессионального обучения', 'url' => ['/kursy/spisok-po']]
                ]
            ],
            'dolzhnostiEditor' => ['label' => 'Справочник должностей', 'url' => ['/dolzhnost/index']],
            'planProspektEditor' => [
                'label' => 'Редактор план-проспекта',
                'items' => [
                    ['label' => '2015', 'url' => ['/plan-prospekt/editor/index?year=2015']],
                    ['label' => '2016', 'url' => ['/plan-prospekt/editor/index?year=2016']],
                ]
            ],
            'myData' => $this->myDataMenuItem()
        ];
    }

    private function sotrudnikOtdelaAttestaciiMenuItems()
    {
        return [
            ['label' => 'Аттестация (регистрация)', 'url' => ['/attestaciya/']],
            ['label' => 'Аттестация (список заявлений)', 'url' => ['/attestaciya/list/']],
            ['label' => 'Экспертно-профильные группы', 'url' => ['/attestacionnaya-komissiya/']],
            ['label' => 'Оценочные листы', 'url' => ['/otsenochnyj-list/']],
            'dolzhnostiEditor' => ['label' => 'Справочник должностей', 'url' => ['/dolzhnost/index']],
            ['label' => 'Руководство комиссией', 'url' => ['/rukovoditel-komissii/']],
            'myData' => $this->myDataMenuItem()
        ];
    }

    private function rukovoditelAttestacionnojKomissiiMenuItems()
    {
        return [
            ['label' => 'Руководство комиссией', 'url' => ['/rukovoditel-komissii/']],
        ];
    }

    private function sotrudnikAttestacionnojKomissiiMenuItems()
    {
        return [
            ['label' => 'Оценивание аттестующихся', 'url' => ['/sotrudnik-att-komissii/']]
        ];
    }
    private function sotrudnikOtdelaKadrovMenuItems()
    {
        return [
            ['label' => 'Справочники',
                'items' => [
                    ['label' => 'Организация', 'url' => ['/organizaciya/']],
                    ['label' => 'Подразделение', 'url' => ['/strukturnoe-podrazdelenie/']],
                    ['label' => 'Кадры', 'items' =>[
                            ['label' =>'Регистрация', 'url' => ['/kadry/registraciya']]
                        ]
                    ],
                ],
            ],
        ];
    }
}

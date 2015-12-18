<?php
namespace app\rbac;

use app\enums\Rol;
use app\enums\TipKursa;

class AuthManager extends StaticAuthManager
{
    public function init()
    {
        parent::init();

        $this->rolesConfig = array_map(
            [$this, 'makeItemConfig'],
            $this->rolesMenuItems()
        );

        $this->permissionsConfig = [

        ];

        $this->dag = [

        ];
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
            Rol::SOTRUDNIK_OTDELA_ATTESTACII => $this->sotrudnikOtdelaAttestaciiMenuItems()
        ];
    }

    private function makeItemConfig($menuItems)
    {
        return ['data' => ['menuItems' => $menuItems]];
    }

    private function pedagogicheskijRabotnikMenuItems() {
        return [
            [
                'label' => 'Запись на курсы',
                'items' => [
                    ['label' => 'Курсы повышения квалификации', 'url' => ['kurs-slushatelyu/zapis-na-kurs-pk']],
                    ['label' => 'Курсы профессиональной переподготовки', 'url' => ['kurs-slushatelyu/zapis-na-kurs-pp']],
                    ['label' => 'Курсы профессионального обучения', 'url' => ['kurs-slushatelyu/zapis-na-kurs-po']]
                ]
            ],
//            ['label' => 'Прохождение аттестации', 'url' => ['attestaciya/']],
            ['label' => 'Мои курсы', 'url' => ['kurs-slushatelyu/moi-kursy']],
            [
                'label' => 'Мои данные',
                'items' => [
                    ['label' => 'Просмотреть', 'url' => ['/dannye-pedrabotnika/lichnye-dannye']],
                    ['label' => 'Изменить', 'url' => ['/lichnye-dannye/index']],
                    ['label' => 'Образование', 'url' => ['/lichnye-dannye/obrazovaniya']]
                ]
            ]
        ];
    }

    private function prepodavatelKursovMenuItems() {
        return [
            ['label' => 'Мои курсы', 'url' => ['kursy-rukovoditelya/spisok']],
            ['label' => 'Список дисциплин', 'url' => ['kurs/spisok-discipline']],
            ['label' => 'Мои данные', 'url' => ['/lichnye-dannye/index']]
        ];
    }

    private function rukovoditelKursovMenuItems() {
        return [
            ['label' => 'Мои курсы', 'url' => ['kursy-rukovoditelya/spisok']],
            ['label' => 'Список дисциплин', 'url' => ['kurs/spisok-discipline']],
            ['label' => 'Мои данные', 'url' => ['/lichnye-dannye/index']]
        ];
    }

    private function sotrudnikUchebnogoOtdelaMenuItems() {
        //    Курсы без дат
        //    Информация о курсах
        //    План-проспект
        return [
            [
                'label' => 'Курсы',
                'items' => [
                    ['label' => 'Курсы повышения квалификации', 'url' => ['kursy/spisok-pk']],
                    ['label' => 'Курсы профессиональной переподготовки', 'url' => ['kursy/spisok-pp']],
                    ['label' => 'Курсы профессионального обучения', 'url' => ['kursy/spisok-po']]
                ]
            ],
            ['label' => 'Мои данные', 'url' => ['/lichnye-dannye/index']]
        ];
    }

    private function sotrudnikOtdelaAttestaciiMenuItems()
    {
        return [
            ['label' => 'Аттестация (регистрация)', 'url' => ['/attestaciya/']],
            ['label' => 'Аттестация (список заявлений)', 'url' => ['/attestaciya/list/']],
            ['label' => 'Аттестациионные комиссии', 'url' => ['/attestacionnaya-komissiya/']],
            ['label' => 'Мои данные', 'url' => ['lichnye-dannye/index']]
        ];
    }
}
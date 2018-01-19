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
            Rol::ADMINISTRATOR => $this->adminMenuItems(),
            Rol::PEDAGOGICHESKIJ_RABOTNIK => $this->pedagogicheskijRabotnikMenuItems(),
            Rol::PREPODAVATEL_KURSOV => $this->prepodavatelKursovMenuItems(),
//            Rol::PROREKTOR_PO_OOD,
            Rol::REKTOR => $this->rektorMenuItems(),
            Rol::RUKOVODITEL_KURSOV => $this->rukovoditelKursovMenuItems(),
//            Rol::RUKOVODITEL_OBRAZOVATELNOGO_UCHREZHDENIYA,
//            Rol::RUKOVODITEL_STRUKTURNOGO_PODRAZDELENIYA,
            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA => $this->sotrudnikUchebnogoOtdelaMenuItems(),
            Rol::SOTRUDNIK_OTDELA_ATTESTACII => $this->sotrudnikOtdelaAttestaciiMenuItems(),
            Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII => $this->rukovoditelAttestacionnojKomissiiMenuItems(),
            Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII => $this->sotrudnikAttestacionnojKomissiiMenuItems(),
            Rol::SOTRUDNIK_OTDELA_KADROV => $this->sotrudnikOtdelaKadrovMenuItems(),
            Rol::MUNICIPALNYJ_OTVESTVENNYJ => $this->municipalnyjOtvestvennyjMenuItems(),
        ];
    }

    /**
     * Меню роли
     */
    private function rektorMenuItems() {
        return [
            'myData' => $this->myDataMenuItem(),
            'dok' => $this->dokMenuItem(),
            'brs' => $this->brsMenuItem(),
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
            'spisokDiscipline' => ['label' => 'Список дисциплин', 'url' => ['/kurs/spisok-discipline']],
            'myData' => $this->myDataMenuItem(),
            'brs' => $this->brsMenuItem(),
        ];
    }

    private function rukovoditelKursovMenuItems() {
        return [
            'myKursy' => ['label' => 'Мои курсы', 'url' => ['/kursy-rukovoditelya/spisok']],
            'spisokDiscipline' => ['label' => 'Список дисциплин', 'url' => ['/kurs/spisok-discipline']],
            'myData' => $this->myDataMenuItem(),
            'dok' => $this->dokMenuItem(),
            'brs' => $this->brsMenuItem(),
            //'newPlanProspekt' => [
            //    'label' => 'План-проспект', 'url' => ['/plan-prospekt/editor/index?year=2018']
            //],
        ];
    }

    private function sotrudnikUchebnogoOtdelaMenuItems() {
        return [
            [
                'label' => 'Курсы',
                'items' => [
                    ['label' => 'Курсы повышения квалификации', 'url' => ['/kursy/spisok-pk']],
                    ['label' => 'Курсы профессиональной переподготовки', 'url' => ['/kursy/spisok-pp']],
                    ['label' => 'Курсы профессионального обучения', 'url' => ['/kursy/spisok-po']],
                    ['label' => 'Потоки', 'url' => ['/upravlenie-kursami/potok/potok/index']],
                ]
            ],
            'planProspektEditor' => [
                'label' => 'Редактор план-проспекта',
                'items' => [
                    ['label' => '2015', 'url' => ['/plan-prospekt/editor/index?year=2015']],
                    ['label' => '2016', 'url' => ['/plan-prospekt/editor/index?year=2016']],
                    ['label' => '2017', 'url' => ['/plan-prospekt/editor/index?year=2017']],
                    ['label' => '2018', 'url' => ['/plan-prospekt/editor/index?year=2018']]
                ]
            ],
            'myData' => $this->myDataMenuItem(),
            'dok' => $this->dokMenuItem(),
            'brs' => $this->brsMenuItem(),
        ];
    }

    private function sotrudnikOtdelaAttestaciiMenuItems()
    {
        return [
            [
                'label' => 'Аттестация',
                'items' => [
                    ['label' => 'Регистрация', 'url' => ['/attestaciya/']],
                    ['label' => 'Список заявлений', 'url' => ['/attestaciya/list/']],
                    ['label' => 'Экспертно-профильные группы', 'url' => ['/attestacionnaya-komissiya/']],
                    ['label' => 'Оценочные листы', 'url' => ['/otsenochnyj-list/']],
                    ['label' => 'Муниципальные ответственные', 'url' => ['/municipanyj-otvetstvennyj/sostav']]
                ]
            ],
            [
                'label' => 'Отчеты аттестации',
                'items' => [
                    ['label' => 'Итоговый отчет атт. ком.', 'url' => ['/attestaciya-otchety/list/itogovyj']],
                    ['label' => 'По вариативным формам', 'url' => ['/attestaciya-otchety/list/var-isp']],
                    ['label' => 'По должностям', 'url' => ['/attestaciya-otchety/list/otchet-by-dolzhnost']],
                    ['label' => 'По районам', 'url' => ['/attestaciya-otchety/list/otchet-by-rajon']],
                    ['label' => 'По сотрудникам комиссий', 'url' => ['/attestaciya-otchety/list/sotrudnik-komissii']],
                    ['label' => 'По портфолио', 'url' => ['/attestaciya-otchety/list/otchet-by-portfolio']],
                    ['label' => 'По ИК', 'url' => ['/attestaciya-otchety/list/otchet-by-ik']],
                ]
            ],
            ['label' => 'Руководство комиссией', 'url' => ['/rukovoditel-komissii/']],
            'myData' => $this->myDataMenuItem(),
            [
                'label' => 'Администрирование',
                'items' => [
                    'dolzhnosti' => $this->dolzhnostiMenuItem(),
                    'registracija' => $this->registracijaMenuItem(),
                ]
            ],
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
            ['label' => 'Оценивание аттестующихся', 'url' => ['/sotrudnik-att-komissii/']],
        ];
    }

    private function sotrudnikOtdelaKadrovMenuItems()
    {
        return [
            'myData' => $this->myDataMenuItem(),
            [
                'label' => 'Управление кадрами',
                'items' => [
                    ['label' => 'Регистрация нового сотрудника', 'url' => '/upravlenie-kadrami/registraciya/'],
                    ['label' => 'Редактор состава подразделения', 'url' => '/upravlenie-kadrami/sostav-podrazdelenija/']
                ],
            ],
            [
            'label' => 'Справочники',
            'items' => [
                'podrazdelenie' => $this->podrazdelenieMenuItem(),
            ]
        ]
        ];
    }

    private function municipalnyjOtvestvennyjMenuItems(){
        return [
            ['label' => 'Списки заявлений', 'url' => ['/municipanyj-otvetstvennyj/list']]
        ];
    }

    private function adminMenuItems()
    {
        return [
            [
                'label' => 'Управление кадрами',
                'items' => [
                    ['label' => 'Регистрация нового сотрудника', 'url' => '/upravlenie-kadrami/registraciya/'],
                    ['label' => 'Редактор состава подразделения', 'url' => '/upravlenie-kadrami/sostav-podrazdelenija/']
                ],
            ],
            [
                'label' => 'Справочники',
                'items' => [
                    'organizaciya' => $this->organizaciyaMenuItem(),
                    'podrazdelenie' => $this->podrazdelenieMenuItem(),
                    'dolzhnosti' => $this->dolzhnostiMenuItem(),
                ]
            ]
        ];
    }

    /**
     * Общие элементы меню
     */
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

    private function dokMenuItem()
    {
        return [
            'label' => 'Документы', 'url' => ['/documenty/process/index']
        ];
    }

    private function organizaciyaMenuItem()
    {
        return ['label' => 'Организации', 'url' => ['/organizaciya/']];
    }

    private function podrazdelenieMenuItem()
    {
        return ['label' => 'Подразделения', 'url' => ['/strukturnoe-podrazdelenie/']];
    }

    private function dolzhnostiMenuItem()
    {
        return [
            'label' => 'Должности',
            'items' =>[
                    ['label' => 'Редактирование', 'url' => ['/dolzhnost/index']],
                    ['label' => 'Учитель', 'url' => ['/dolzhnost/uchitel']],
            ]
        ];
    }

    private function registracijaMenuItem()
    {
        return [
            'label' => 'Регистрация пользователя', 'url' => ['/kadry/registraciya'],
        ];
    }

    private function brsMenuItem()
    {
        return [
            'label' => 'БРС', 'url' => ['/brs/'],
        ];
    }
}

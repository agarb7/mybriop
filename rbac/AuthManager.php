<?php
namespace app\rbac;
use app\enums\Rol;
class AuthManager extends StaticAuthManager
{
    public function init()
    {
        parent::init();
        $this->rolesConfig = array_map(
            [$this, 'makeItemConfig'],
            $this->rolesMenuItems()
        );
        $this->permissionsConfig = [];
        $this->dag = [];
    }
    private function rolesMenuItems()
    {
        return [
            Rol::ADMINISTRATOR => null,
            Rol::PEDAGOGICHESKIJ_RABOTNIK => null,
            Rol::PREPODAVATEL_KURSOV => null,
//            Rol::PROREKTOR_PO_OOD => null,
//            Rol::REKTOR => null,
            Rol::RUKOVODITEL_KURSOV => null,
//            Rol::RUKOVODITEL_OBRAZOVATELNOGO_UCHREZHDENIYA => null,
//            Rol::RUKOVODITEL_STRUKTURNOGO_PODRAZDELENIYA => null,
            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA => null,
            Rol::SOTRUDNIK_OTDELA_ATTESTACII => null,
            ROL::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII => null,
            Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII =>  null,
            Rol::SOTRUDNIK_OTDELA_KADROV => null,
            Rol::MUNICIPALNYJ_OTVESTVENNYJ => null,
        ];
    }
    private function makeItemConfig($menuItems)
    {
        return ['data' => ['menuItems' => $menuItems]];
    }
}
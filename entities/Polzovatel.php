<?php

namespace app\entities;

use app\behaviors\ParolBehavior;
use app\enums\Rol;
use app\transformers\EnumArrayTransformer;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use Yii;

/**
 * Class Polzovatel
 * @package app\models
 *
 * @property int $id
 * @property string $fizLico bigint NOT NULL,
 * @property string $login login NOT NULL,
 * @property string $klyuchAutentifikacii character varying NOT NULL,
 * @property string $heshParolya character varying NOT NULL,
 * @property string $solParolya character varying NOT NULL,
 * @property string $kodPodtverzhdeniyaEmail character varying,
 * @property string $aktiven boolean NOT NULL, -- Включает/отключает пользователя, обычно используется для премодерации пользователей.
 * @property string $roli roli NOT NULL,
 * @property array $roliAsArray
 *
 * @property string $parol
 * @method boolean validateParol(string $parol)
 */
class Polzovatel extends EntityBase implements IdentityInterface
{
    public function transformations()
    {
        return [
            ['roliAsArray' => 'roli', EnumArrayTransformer::className(), ['enum' => Rol::className()]]
        ];
    }

    public function behaviors()
    {
        return [ParolBehavior::className()];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($login)
    {
        return static::findOne(['login' => $login]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Аутентификация по токену недоступна');
    }

    public function getId()
    {
        return $this->login;
    }

    public function getAuthKey()
    {
        return $this->klyuchAutentifikacii;
    }

    public function validateAuthKey($auth_key)
    {
        return $this->klyuchAutentifikacii === $auth_key;
    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->inverseOf('polzovatelRel');
    }

    public function generateKlyuchAutentifikacii()
    {
        $this->klyuchAutentifikacii = Yii::$app->security->generateRandomString(255);
    }

    public function generateKodPodtverzhdeniyaEmail()
    {
        $this->kodPodtverzhdeniyaEmail = Yii::$app->security->generateRandomString(32);
    }

    public function deleteRol($rol){
        $roli = $this->roliAsArray;
        $rol_index = array_search($rol,$roli);
        if ($rol_index !== false){
            unset($roli[$rol_index]);
        }
        $this->roliAsArray = $roli;
    }
}

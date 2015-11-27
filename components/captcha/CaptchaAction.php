<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.05.15
 * Time: 23:02
 */

namespace app\components\captcha;


use app\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

class CaptchaAction extends \yii\captcha\CaptchaAction
{
    public function __construct($id, $controller, $config = [])
    {
        $fontFile = '@app/components/captcha/gtw.ttf';

        $config = ArrayHelper::merge(compact('fontFile'), $config);
        parent::__construct($id, $controller, $config);
    }

    public function run()
    {
        if (Yii::$app->request->getQueryParam(self::REFRESH_GET_VAR) !== null) {
            $code = $this->getVerifyCode(true);

            return json_encode([
                'hash1' => $this->generateValidationHash($code),
                'hash2' => $this->generateValidationHash(mb_strtolower($code)),
                'url' => Url::to([$this->id, 'v' => uniqid()]),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $this->setHttpHeaders();
            Yii::$app->response->format = Response::FORMAT_RAW;
            return $this->renderImage($this->getVerifyCode());
        }
    }

    public function generateValidationHash($code)
    {
        $code_len = mb_strlen($code);
        for ($h = 0, $i = $code_len-1; $i >= 0; --$i) {
            $char = mb_substr($code, $i, 1);
            $h += StringHelper::charCode($char);
        }

        return $h;
    }

    protected function generateVerifyCode()
    {
        if ($this->minLength > $this->maxLength)
            $this->maxLength = $this->minLength;

        if ($this->minLength < 3)
            $this->minLength = 3;

        if ($this->maxLength > 20)
            $this->maxLength = 20;

        $length = mt_rand($this->minLength, $this->maxLength);

        $letters = 'бвгджзйклмнпрстфхцчшщ';
        $letters_cnt = mb_strlen($letters);

        $vowels = 'аеёиоуыэюя';
        $vowels_cnt = mb_strlen($vowels);

        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            if ($i % 2 && mt_rand(0, 99) < 90 || !($i % 2) && mt_rand(0, 99) < 10) {
                $pos = mt_rand(0, $vowels_cnt-1);
                $code .= mb_substr($vowels, $pos, 1);
            } else {
                $pos = mt_rand(0, $letters_cnt-1);
                $code .= mb_substr($letters, $pos, 1);
            }
        }

        return $code;
    }

    public function validate($input, $caseSensitive)
    {
        $code = $this->getVerifyCode();

        $valid = $caseSensitive
            ? ($input === $code)
            : mb_strtolower($input) === mb_strtolower($code);

        $session = Yii::$app->getSession();
        $session->open();
        $name = $this->getSessionKey() . 'count';
        $session[$name] = $session[$name] + 1;
        if ($valid || $session[$name] > $this->testLimit && $this->testLimit > 0) {
            $this->getVerifyCode(true);
        }

        return $valid;
    }

    protected function renderImageByGD($code)
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        $backColor = imagecolorallocate(
            $image,
            (int) ($this->backColor % 0x1000000 / 0x10000),
            (int) ($this->backColor % 0x10000 / 0x100),
            $this->backColor % 0x100
        );
        imagefilledrectangle($image, 0, 0, $this->width, $this->height, $backColor);
        imagecolordeallocate($image, $backColor);

        if ($this->transparent) {
            imagecolortransparent($image, $backColor);
        }

        $foreColor = imagecolorallocate(
            $image,
            (int) ($this->foreColor % 0x1000000 / 0x10000),
            (int) ($this->foreColor % 0x10000 / 0x100),
            $this->foreColor % 0x100
        );

        $length = mb_strlen($code);
        $box = imagettfbbox(30, 0, $this->fontFile, $code);
        $w = $box[4] - $box[0] + $this->offset * ($length - 1);
        $h = $box[1] - $box[5];
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);
        for ($i = 0; $i < $length; ++$i) {
            $fontSize = (int) (rand(26, 32) * $scale * 0.8);
            $angle = rand(-10, 10);
            $letter = mb_substr($code, $i, 1);
            $box = imagettftext($image, $fontSize, $angle, $x, $y, $foreColor, $this->fontFile, $letter);
            $x = $box[2] + $this->offset;
        }

        imagecolordeallocate($image, $foreColor);

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return ob_get_clean();
    }

    protected function renderImageByImagick($code)
    {
        $backColor = $this->transparent ? new \ImagickPixel('transparent') : new \ImagickPixel('#' . dechex($this->backColor));
        $foreColor = new \ImagickPixel('#' . dechex($this->foreColor));

        $image = new \Imagick();
        $image->newImage($this->width, $this->height, $backColor);

        $draw = new \ImagickDraw();
        $draw->setFont($this->fontFile);
        $draw->setFontSize(30);
        $fontMetrics = $image->queryFontMetrics($draw, $code);

        $length = mb_strlen($code);
        $w = (int) ($fontMetrics['textWidth']) - 8 + $this->offset * ($length - 1);
        $h = (int) ($fontMetrics['textHeight']) - 8;
        $scale = min(($this->width - $this->padding * 2) / $w, ($this->height - $this->padding * 2) / $h);
        $x = 10;
        $y = round($this->height * 27 / 40);
        for ($i = 0; $i < $length; ++$i) {
            $draw = new \ImagickDraw();
            $draw->setFont($this->fontFile);
            $draw->setFontSize((int) (rand(26, 32) * $scale * 0.8));
            $draw->setFillColor($foreColor);
            $letter = mb_substr($code, $i, 1);
            $image->annotateImage($draw, $x, $y, rand(-10, 10), $letter);
            $fontMetrics = $image->queryFontMetrics($draw, $letter);
            $x += (int) ($fontMetrics['textWidth']) + $this->offset;
        }

        $image->setImageFormat('png');
        return $image->getImageBlob();
    }
}
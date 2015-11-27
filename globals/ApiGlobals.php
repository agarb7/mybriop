<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 01.03.15
 * Time: 15:53
 */

namespace app\globals;


use app\entities\FizLico;
use app\entities\Polzovatel;

class ApiGlobals {

    public static function first_letter_up($string, $coding='utf-8') {
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) {
            preg_match('#(.)#us', mb_strtoupper(mb_strtolower($string, $coding), $coding), $matches);
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $coding), $coding);
        }
        else {
            $string = ucfirst($string);
        }
        return $string;
    }

    public static function get_first_letter($string=''){
        if ($string){
            return mb_substr($string,0,1,'UTF-8');
        }
        return '';
    }

    public static function to_trimmed_text($str)
    {
        if ($str) {
            $trimmed = mb_ereg_replace('^\s*(.*?)\s*$', '\1', $str);
            $nl_squeezed = mb_ereg_replace('\s*\n\s*', "\n", $trimmed);
            $space_squeezed = mb_ereg_replace('[^\n\S]+', ' ', $nl_squeezed);
        }
        else $space_squeezed = null;
        return $space_squeezed;
    }

    public static function translit($st){
        $st=str_replace(
            array(' ','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ы','Ъ','Э','Ю','Я'),
            array('-','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я'),$st);

        $st=str_replace(
            array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я'),
            array('a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sch','','i',"'",'e','yu','ya'),$st);
        return $st;
    }

    public static function get_file_ext($f) {
        return mb_strtolower(substr(strrchr($f, '.'), 1));
    }

    public static function file_row($type,$file_name,$file_id,$form_id,$is_check=false){
        $html='<label class="file-row" id="file_row'.$file_id.'" for="'.$form_id.'file_checkbox_'.$file_id.'">\
                    <span class="file-checkbox">\
                        <input onchange="check_file('.$file_id.')" '.($is_check ? 'checked="checked"' : '').' type="'.$type.'" class="file-chbx" name="file_checkbox_'.$form_id.'" id="'.$form_id.'file_checkbox_'.$file_id.'" value="'.$file_id.'">\
                    </span>\
                    <span class="file-name" id="file-name'.$file_id.'">'.$file_name.'</span>\
                </label>\\';
        return $html;
    }

    public static function getPolzovatelId()
    {
        return \Yii::$app->user->identity->id;
    }

    public static function getFizLicoPolzovatelyaId()
    {
        //return 1;
        if (isset($_SESSION['fiz_lico_id'])) return $_SESSION['fiz_lico_id'];
        $user = \Yii::$app->user->identity;
        if ($user) {
            $_SESSION['fiz_lico_id'] = $user->fiz_lico;
            return $user->fiz_lico;
        }
        return null;
    }

    public static function get_user_dir_url()
    {
        $id = self::getPolzovatelId();
        if (!$id)
            throw new \Exception;
        return '/data/'.$id.'/';
    }

    public static function isEven($value)
    {
        return ($value%2 == 0);
    }

    public static function is_posistive($chasy){
        return preg_match('/^\+?\d+$/', $chasy);
    }

    public static function parse_plain_text_to_html($text){
        $strings=explode("\n",$text);
        //var_dump($strings);
        $content = '';
        foreach($strings as $line) {
            $content.="\n<p>".$line.'</p>';
        }
        return $content;
    }

    private static function getPolzovatel()
    {
        return \Yii::$app->user->id;
    }



    public static function parse_text($text=''){
        $result = '';
        $paragraphs = explode("\n",$text);
        foreach ($paragraphs as $k=>$v) {
            $result .= '<p class="myp">'.$v.'</p>';
        }
        return $result;
    }

}
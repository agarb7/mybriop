<?php

namespace app\controllers;


use app\globals\ApiGlobals;
use yii\base\Controller;

class FilesController extends Controller
{
    public function actionUpload()
    {
//        $user_id = $_POST['user_id'];
//        $folder="data/".$user_id.'/';
//to do

       $polzovatel_id = ApiGlobals::getPolzovatelId();
       if (!$polzovatel_id)
            throw new \Exception;

       $folder="data/".$polzovatel_id.'/';


        $input_name = 'load_file';
       // var_dump($_FILES);die();
        if (!is_dir($folder)) mkdir($folder,0750,true);
        if($uploads=$_FILES[$input_name]){
            //foreach($uploads['name'] as $upload_id=>$val){
                if($uploads['error']==0){
                    $file_ext = ApiGlobals::get_file_ext($uploads['name']);
                    $fname = md5(time().'_'.ApiGlobals::translit($uploads['name'])).'.'.$file_ext;//md5_file($uploads['tmp_name']).'.'.$file_ext;//md5(time().'_'.ApiGlobals::translit($uploads['name'])).'.'.$file_ext;
                    if(move_uploaded_file($uploads['tmp_name'], $folder.'/'.$fname)){
                        $sql = 'INSERT INTO fajl (vneshnee_imya_fajla, vnutrennee_imya_fajla, vladelec)
                                VALUES (:vneshnee_imya_fajla, :vnutrennee_imya_fajla, :vladelec)';
                        $res = \Yii::$app->db->createCommand($sql)
                                             ->bindValue(':vneshnee_imya_fajla',$uploads['name'])
                                             ->bindValue(':vnutrennee_imya_fajla',$fname)
                                             ->bindValue(':vladelec', $polzovatel_id)
                        ->execute();
                        if ($res){
                            $file_id = \Yii::$app->db->getLastInsertID('fajl_id_seq');
                            $item = [
                                'id'=>$file_id,
                                'vneshnee_imya_fajla'=>$uploads['name'],
                                'vnutrennee_imya_fajla'=>$fname,
                                'vladelec'=>$polzovatel_id
                            ];
                            $html['html']=static::file_row('radio',$item,false);//ApiGlobals::file_row('radio',$uploads['name'],$file_id,$_POST['widget_id'],true);
                        }
                    }

                }
            //}
        }
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        if (!$xhr){
            $res=json_encode($html);
            $result='<textarea>'.$res.'</textarea>';
            echo $result;
            die();
        }
        else{
            $result=$html;
        }
        echo json_encode($result);
    }

    public static function file_row($type,$item,$is_check=false){
        $html='<label class="file-row" id="file_row'.$item['id'].'" for="file_checkbox_'. $item['id'].'">
                    <span class="file-checkbox">
                        <input onchange="check_file('.$item['id'].')" '.($is_check ? 'checked="checked"' : '').' type="'.$type.'" class="file-chbx" name="file_checkbox" id="file_checkbox_'.$item['id'].'" value="'.$item['id'].'">
                    </span>
                    <span class="file-name" id="file-name'.$item['id'].'">'.$item['vneshnee_imya_fajla'].'</span>
                </label>';
        return $html;
    }

    public function actionGetUserFiles(){
        $user_id = '';
        //var_dump(\Yii::$app->user->identity->id);die();
        if (isset($_REQUEST['uid'])) $user_id = $_REQUEST['uid'];
        else $user_id = \Yii::$app->user->identity->id;
        $result = ['files'=>[],'html'=>''];
        $files = [];
        $files_html = '';
        $sql = 'select * from fajl where vladelec = :user_id order by id';
        if ($res = \Yii::$app->db->createCommand($sql)->bindValue(':user_id', $user_id)->queryAll()) {
            foreach ($res as $k => $v) {
                $files[$v['id']] = $v;
                $files_html .= static::file_row('radio', $v, false);
            }
        }
        $html = '
            <div class="dark_fon hidden" tabindex="10" onkeydown="darkKeyDown(event)" id="files_table">
            <div class="add_files_form">
                <div class="file_add_section">
                    <form id="add_files"  method="POST" action="" enctype="multipart/form-data">
                        <label for="load_file" id="load_file_lable" class="add-file-button btn btn-primary">Загрузить файл</label>
                        <input onchange="change_file()" type="file" class="file-input" id="load_file" name="load_file">
                        <div class="myprogress hidden" id="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div >
                        </div>
                    </form>
                </div>
                <div class="center_files_form">
                    <div class="file-headers">
                        <span class="file-name-header">Имя файла</span>
                    </div>
                    <div class="file-list">
                    '.$files_html.'
                    </div>
                    <div class="file-bottom">
                        <input type="hidden" value="" id="element_id">
                        <span class="btn btn-primary" onclick="select_file()">Продолжить</span>
                        <span class="slink" onclick="hide_add_file()">Отмена</span>
                    </div>
                </div>
            </div>
            </div>
            ';

        $result['files'] = $files;
        $result['html'] = $html;
        echo json_encode($result);
        die();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 06.12.15
 * Time: 12:26
 */

namespace app\components;

/**
 * Class JsResponse
 * @package app\components
 *
 * @property string $type
 * @property string $msg
 * @property array|string $data
 */

class JsResponse
{
    const ERROR = 'error';
    const WARNING = 'warning';
    const SUCCESS = 'success';

    const MSG_OPERATION_SUCCESS = 'Операция выполнена успешно';
    const MSG_OPERATION_ERROR = 'Ошибка при выполнении операции.';

    public $type;
    public $msg;
    public $data;

    function __construct($type=JsResponse::SUCCESS,$msg='Операция успешно выполнена',$data=[]){
        $this->type = $type;
        $this->msg = $msg;
        $this->data = $data;
    }
}
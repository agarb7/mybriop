<?php
namespace app\validators;

class LoginValidator extends RegularExpressionStringValidator
{
    public $pattern = '/^[a-z0-9]*$/u';
    public $min = 2;
    public $max = 20;
    public $patternMessage = '«{attribute}» должен состоять из строчных латинских букв и/или цифр.';
}

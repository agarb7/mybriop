<?php
namespace app\validators;

class ImyaChelovekaValidator extends RegularExpressionStringValidator
{
    public $pattern = '/^\S+( \S+)*$/u';
    public $max = 60;
    public $patternMessage = '«{attribute}» не должен содержать более одного пробела подряд.';
}

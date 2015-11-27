<?php
namespace app\validators;

class NazvanieValidator extends RegularExpressionStringValidator
{
    public $pattern = '/^\S+( \S+)*$/u';
    public $max = 400;
    public $patternMessage = '«{attribute}» не должен содержать более одного пробела подряд.';
}

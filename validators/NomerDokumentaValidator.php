<?php
namespace app\validators;

class NomerDokumentaValidator extends RegularExpressionStringValidator
{
    public $pattern = '/^\S+( \S+)*$/u';
    public $max = 40;
    public $patternMessage = '«{attribute}» не должен содержать более одного пробела подряд.';
}
<?php
use app\entities\RazdelKursa;
use app\helpers\Val;
use yii\web\View;

/**
 * @var $razdelRecord RazdelKursa
 * @var $this View
 */

$prefixNo = Val::of($razdelRecord, 'nomer');

?>
<h2><?= Val::asText($razdelRecord, 'nazvanieDlyaRazdelaKursaRel', 'nazvanie') ?></h2>
<?php
$query = $razdelRecord->getPodrazdelyKursaRel()->orderBy('nomer');
foreach ($query->all() as $podrazdelRecord)
    echo $this->render('_podrazdel-kursa', compact('podrazdelRecord', 'prefixNo'));

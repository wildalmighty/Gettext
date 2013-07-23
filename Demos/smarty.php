<?php
//composer autoloader
include('../vendor/autoload.php');

use \Smarty;


$lang = isset($_GET['lang']) ? $_GET['lang'] : 'bg';
$file_to_translate = 'smarty.html';
$file_with_translations = 'locale/' . $lang . '/' . $file_to_translate . '.php';
Gettext\Translator::loadTranslations($file_with_translations);

$smarty = new Smarty();
//$smarty->setTemplateDir("View");
$smarty->setCompileDir("templates_c");
$smarty->setCacheDir("cache");
//$smarty->setConfigDir(APPPATH."config/smarty");

//register
$smarty->registerPlugin("function", "__", 'smarty__');
$smarty->registerPlugin("function", "__n", 'smartyn__');
$smarty->registerPlugin("function", "__p", 'smartyp__');
$smarty->registerPlugin("function", "__np", 'smartynp__');

function smarty__($params){
	$original = $params['original'];
	return __($original);
}

function smartyn__($params){
	$original = $params['original'];
	$plural =  $params['plural'];
	$n =  $params['count'];
	return n__($original, $plural, $n );
}

function smartyp__($params){
	$original = $params['original'];
	$context =  $params['context'];
	$n =  $params['count'];
	return p__($context , $original , $n);
}
function smartynp__($params){
	$original = $params['original'];
	$plural =  $params['plural'];
	$context =  $params['context'];
	$n =  $params['count'];
	return np__($context , $original , $plural, $n);
}


$tpl = $smarty->createTemplate('smarty.html');
$tpl->assign('apples',4);

echo $tpl->fetch();
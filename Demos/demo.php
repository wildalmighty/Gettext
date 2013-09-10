<?php
//Simple autoload function
ini_set('display_errors', 'On');
//composer autoloader
include('../vendor/autoload.php');

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'bg';

//$file_to_translate = 'javascript.js';
$file_to_translate = 'gettext.html.php';
//$file_to_translate = 'smarty.html';
$file_extension = substr($file_to_translate, stripos($file_to_translate, '.') + 1);
$file_with_translations = 'locale/' . $lang . '/' . $file_to_translate . '.php';

Gettext\Translator::setLanguage($lang);
Gettext\Translator::loadTranslations($file_with_translations);

//$entries = Gettext\Translator::getTranslationsAsEntries();

switch ($file_extension){
	case 'js': 
		$translations = Gettext\Extractors\JsCode::extract($file_to_translate);
		break;
	case 'html': 
		$translations = Gettext\Extractors\Smarty::extract($file_to_translate);
		break;
	default :
		$file_extension = 'php';
		$translations = Gettext\Extractors\PhpCode::extract($file_to_translate);
		break;
}


$entries = Gettext\Translator::getTranslationsAsEntries(false,$translations);


if (isset($_POST['submit'])) {
	foreach ($_POST as $trans) {
		$context = isset($trans[0]) ? $trans[0] : '';
		$original = isset($trans[1]) ? $trans[1] : false;

		$original_translation = isset($trans[2]) ? html_entity_decode($trans[2]) : false;
		$plural = (isset($trans[3])) ? $trans[3] : '';
		$plural_translation = isset($trans[4]) ? html_entity_decode($trans[4]) : false;

		$translation = $entries->find($context, $original, $plural);


		if ($translation) {
			$translation->setTranslation($original_translation);
			if($plural){
				$translation->setPlural($plural);
				if($plural_translation){
					$translation->setPluralTranslation($plural_translation,0);
				}
			}
		}
	}
	if (!file_exists(dirname($file_with_translations))) {
		mkdir(dirname($file_with_translations), 0775, true);
		chmod(dirname($file_with_translations), 0775);
	}
	Gettext\Generators\PhpArray::generateFile($entries, $file_with_translations);
	if($file_extension == 'js'){
		Gettext\Generators\Jed::generateFile($entries, 'locale/' . $lang . '/' . $file_to_translate );
	}
	?><div style="color: green">Saved Successfully</div><?
}
?>
<!DOCTYPE html>
<h1><?=$file_to_translate?></h1>
<h4><?=$lang?></h4>

<form method="post"><?
	if ($entries) {
		foreach ($entries AS $key => $objTranslation) {
			?>
			<div>
				<label>
					<span style="color: green"><?= $objTranslation->context ?></span> 
					<?= htmlspecialchars($objTranslation->original) ?>
				</label>
				<input type="hidden" name="translation<?= $key ?>[]" value="<?= htmlspecialchars($objTranslation->context) ?>" />
				<input type="hidden" name="translation<?= $key ?>[]" value="<?= htmlspecialchars($objTranslation->original) ?>" />
				<textarea disabled="disabled"><?= htmlspecialchars($objTranslation->original) ?></textarea>
				<textarea name="translation<?= $key ?>[]"><?= htmlspecialchars($objTranslation->translation) ?></textarea>
				<?if($objTranslation->hasPlural()){?>
					<textarea disabled="disabled name="translation<?= $key ?>[]"><?= htmlspecialchars($objTranslation->plural) ?></textarea>
					<input type="hidden" name="translation<?= $key ?>[]" value="<?= htmlspecialchars($objTranslation->plural) ?>" />
					<textarea name="translation<?= $key ?>[]"><?= (!empty($objTranslation->pluralTranslation)) ? htmlspecialchars($objTranslation->pluralTranslation[0]) : '' ?></textarea>
				<?}?>
			</div><?
		}
	}
	?>
	<input type="submit" name="submit" value="save"/>
</form>

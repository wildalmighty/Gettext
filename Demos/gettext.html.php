<?php
ini_set('display_errors', 'On');

//composer autoloader
include('../vendor/autoload.php');

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'bg';
$file_with_translations = 'locale/' . $lang . '/' . basename(__FILE__) . '.php';
$file_with_translationsJS = 'locale/' . $lang . '/javascript.js';
Gettext\Translator::loadTranslations($file_with_translations);
//Gettext\Translator::loadTranslations($file_with_translationsJS);
$date = date('d.m.Y');
?>

<header>
	<h1><?php __e('This is a text'); ?></h1>
	<script src="jed.js"></script>
	<script src="javascript.js"></script>
</header>

<div>
	
	<p><?= __('Today is :date',[':date'=> $date]); ?></p>
	<p><?= __('This is another text'); ?></p>
	<p><?= __('Това е българкси текст! Много обичам пуканки.'); ?></p>
	<p><?= __('This is another text (with parenthesis)'); ?></p>
	<p><?= __('This is another text "with double quotes"'); ?></p>
	<p><?= htmlspecialchars(__('This is another text \'with escaped quotes\'')); ?></p>
	<p><?= n__('%s point', '%s points', rand(1,3)); ?></p>
	<p><?= p__('dsadasd', 'Text with prefix'); ?></p>
	<?= foo() ?>
	<?php
	function foo(){
		$date = time();
		$date_str_parts_arr[] = __('on :date at :clock',
				array(':date'=> date('d.m.Y',$date) ,':clock'=> date('H:i',$date))
		);
		return $date_str_parts_arr[0];
	}
	?>

	<br>	
	<script>
		var i18n = new Jed({
			// Generally output by a .po file conversion
			locale_data: <? include $file_with_translationsJS;?>,
			"domain": "messages"
		});
		test();
	</script>
</div>



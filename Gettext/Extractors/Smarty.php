<?php

//TODO: smarty template extractor

namespace Gettext\Extractors;

use Gettext\Entries;

class Smarty extends Extractor
{

	static public $functions = array(
		'n__' => 'n__',
		'__n' => 'n__',
		'p__' => 'p__',
		'__p' => 'p__',
		'__np' => 'np__',
		'__' => '__'
	);

	static public function parse($file, Entries $entries)
	{

		$content = file_get_contents($file);
		$keywords = implode("|", array_keys(self::$functions));
		
		$ps_trans_pattern = '(.*[^\\\\])';
		
		$regex = '/\{(' . $keywords . ')
			(
				\s*(original)\=[\'\"]' . $ps_trans_pattern . '[\'\"]?|
				\s*(plural)\=[\'\"]' . $ps_trans_pattern . '[\'\"]?|
				\s*(context)\=[\'\"]' . $ps_trans_pattern . '[\'\"]?|
				\s*[a-z0-9]+\=[\'\"]*' . $ps_trans_pattern . '[\'\"]*?
			)+
			\s*\}/ixU';

		preg_match_all($regex, $content, $matches, PREG_SET_ORDER);

		if(!empty($matches)){
			foreach ($matches as $match) {
				if (!isset(self::$functions[$match[1]])) {
					continue;
				}

				switch (self::$functions[$match[1]]) {
					case '__':
						$original = $match[4];
						$translation = $entries->find('', $original) ? : $entries->insert('', $original);
						break;

					case 'n__':
						$original = $match[4];
						$plural = $match[6];
						$translation = $entries->find('', $original, $plural) ? : $entries->insert('', $original, $plural);
						break;

					case 'p__':
						$original = $match[4];
						$context = $match[8];
						$translation = $entries->find($context, $original) ? : $entries->insert($context, $original);
						break;
					case 'np__':
						$original = $match[4];
						$plural = $match[6];
						$context = $match[8];
						$translation = $entries->find($context, $original, $plural) ? : $entries->insert($context, $original, $plural);
						break;
				}
			}
		}
	}

}

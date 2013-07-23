<?php

namespace Gettext\Extractors;

use Gettext\Entries;

class JsCode extends Extractor
{

	static public $functions = array(
		'gettext' => '__',
		'n__' => 'n__',
		'__n' => 'n__',
		'p__' => 'p__',
		'__' => '__'
	);

	static public function parse($file, Entries $entries)
	{
		$strings = $regs = array();

		$content = file_get_contents($file);
		$encoding = mb_detect_encoding($content, array('UTF-8', 'ISO-8859-1', 'WINDOWS-1252'), true);

		if ($encoding && (($encoding !== 'UTF-8') || mb_check_encoding($content, 'UTF-8') === false)) {
			$content = utf8_encode($content);
		}

		$content = htmlspecialchars($content, ENT_NOQUOTES);

		$content = preg_replace_callback('# ( / (?: (?>[^/\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\/ )+ (?<!\\\\)/ ) [a-z]* \b #ix', function ($match) use (&$regs) {
					$counter = count($regs);
					$regs[$counter] = $match[1];

					return "<<reg{$counter}>>";
				}, $content);

		$content = preg_replace_callback(array(
			'# " ( (?: (?>[^"\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\" )* ) (?<!\\\\)" #ix',
			"# ' ( (?: (?>[^'\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\' )* ) (?<!\\\\)' #ix"
				), function ($match) use (&$regs, &$strings) {
					$counter = count($strings);

					$strings[$counter] = preg_replace_callback("#<<reg(\d+)>>#", function ($match) use ($regs) {
								return $regs[$match[1]];
							}, $match[0]);

					return "<<s{$counter}>>";
				}, $content);
		
		//delete line comments
		$content = preg_replace("#(//.*?)$#m", '', $content);
		//delete multiline comments
		$content = preg_replace('#/\*(.*?)\*/#is', '', $content);

		$content = preg_replace_callback("#<<s(\d+)>>#", function ($match) use ($strings) {
					return $strings[$match[1]];
				}, $content);

		var_dump($content);

		$keywords = implode('|', array_keys(self::$functions));
//TODO: to exclude function definitions
//		preg_match_all("#(?!.*function\s*)($keywords)\s*\((.*?)\)#ix", $content, $matches, PREG_SET_ORDER);
//TODO: try to exclude function definitions
//		preg_match_all("#(?!( function\s*\(.*\) )) ($keywords)\s*\(( .* ) \)#sixU", $content, $matches, PREG_SET_ORDER);
		//
		#
		/**/
		
		//prestashop
//		preg_match_all('/\{l\s*s=[\'\"] (.*[^\\\\]) [\'\"](\s*sprintf=.*)?(\s*js=1)?\s*\}/U', $content, $matches, PREG_SET_ORDER);
		
//		$regex = '/('.$keywords.')\s*\(\s*s=[\'\"] (.*[^\\\\]) [\'\"]\s*\)/ixU';		
//		$regex = '/('.$keywords.')\s*\(\s*[\'\"] (.*[^\\\\]) [\'\"]\s*\)/ixU';
//				
//		$regex = "#
//			(?<!function\s{1})
//			($keywords)
//			\s*\(\s*
//				
//					(?:
//						((?:
//							\"(?: \\\\\"| [^\"] )+\"
//						),*)*
//						|
//						((?:
//							'((?: \\\' | [^'] | )+)'
//						),*)*
//					)+				
//				
//			\)
//			#ixU";
		
		$regex = '#
			(?<!function\s{1})
			('.$keywords.')				
			\s*\(\s*
				(				
					(
					
						[\'\"]
							(
								(?!\)).*
							)
						[\'\"]
						
					)
					[,]*
					[0-9]*\w*
				)+

			\s*\)
			#ixU';

var_dump($regex);

		preg_match_all($regex, $content, $matches, PREG_SET_ORDER);
		
var_dump($matches);

		foreach ($matches as $match) {
			if (!isset(self::$functions[$match[1]])) {
				continue;
			}

			switch (self::$functions[$match[1]]) {
				case '__':
					$original = $match[2];
					$translation = $entries->find('', $original) ? : $entries->insert('', $original);
					break;

				case 'n__':
					$original = $match[2];
					$plural = $match[3];
					$translation = $entries->find('', $original, $plural) ? : $entries->insert('', $original, $plural);
					break;

				case 'p__':
					$context = $match[2];
					$original = $match[3];
					$translation = $entries->find($context, $original) ? : $entries->insert($context, $original);
					break;
			}
		}
	}

	static private function stripQuotes($match, $quote)
	{
		if (is_array($match)) {
			foreach ($match as &$value) {
				$value = self::stripQuotes($value, $quote);
			}

			return $match;
		}

		if ($quote === '"') {
			return str_replace('\\"', '"', $match);
		}

		return str_replace("\\'", "'", $match);
	}
}

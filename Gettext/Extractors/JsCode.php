<?php
namespace Gettext\Extractors;

use Gettext\Entries;

class JsCode extends Extractor {
	static public $functions = array(
		'n__' => 'n__',
		'__n' => 'n__',
		'p__' => 'p__',
		'__p' => 'p__',
		'__' => '__'
	);

	static public function parse ($file, Entries $entries) {
		$content = file_get_contents($file);
		$encoding = mb_detect_encoding($content, array('UTF-8', 'ISO-8859-1', 'WINDOWS-1252'), true);

		if ($encoding && (($encoding !== 'UTF-8') || mb_check_encoding($content, 'UTF-8') === false)) {
			$content = utf8_encode($content);
		}

		$functions = implode('|', array_keys(self::$functions));
		$content = htmlspecialchars($content, ENT_NOQUOTES);
		$length = strlen($content);
		$index = 0;

		while ($index < $length) {
			if (($index = strpos($content, '(', $index)) === false) {
				break;
			}

			if (preg_match('/(^|[^\w-])('.$functions.')$/', substr($content, 0, $index), $matches) !== 1) {
				$index++;
				continue;
			}

			$function = $matches[2];
			$start = $index - strlen($function);
			$quote = null;
			$buffer = '';
			$args = array();
			$l = $p = null;
			$index++;

			for ($in = 0; $index < $length; $index++) {
				$p = $l;
				$l = $content[$index];

				switch ($l) {
					case '"':
						if (($quote === '"') && ($p !== '\\')) {
							$quote = null;
						} else if ($quote === null) {
							$quote = '"';
						} else {
							$buffer .= $l;
						}
						break;

					case "'":
						if (($quote === "'") && ($p !== '\\')) {
							$quote = null;
						} else if ($quote === null) {
							$quote = "'";
						} else {
							$buffer .= $l;
						}
						break;

					case ',':
						if ($quote === null) {
							$args[] = $buffer;
							$buffer = '';
						}
						break;

					case ')':
						if ($quote === null) {
							$args[] = $buffer;
							break 2;
						}
						$buffer .= $l;
						break;

					case ' ':
						if ($quote !== null) {
							$buffer .= $l;
						}
						break;

					default:
						$buffer .= $l;
						break;
				}
			}

			foreach ($args as &$arg) {
				$arg = str_replace('\\', '', $arg);
			}

			switch (self::$functions[$function]) {
				case '__':
					$original = $args[0];
					$translation = $entries->find('', $original) ?: $entries->insert('', $original);
					break;

				case 'n__':
					$original = $args[0];
					$plural = isset($args[1]) ? $args[1] : '';
					$translation = $entries->find('', $original, $plural) ?: $entries->insert('', $original, $plural);
					break;

				case 'p__':
					$context = $args[0];
					$original = $args[1];
					$translation = $entries->find($context, $original) ?: $entries->insert($context, $original);
					break;
			}
		}
	}
}

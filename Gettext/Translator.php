<?php

namespace Gettext;

use Gettext\Entries;

class Translator
{

	static public $lang = 'en';
	static private $dictionary = array();
	static private $domain = 'messages';
	/**
	* Contexts are keys that are just prefixed with a context string
	* with a unicode \u0004 as the delimiter.
	* You can use it for anything. Usually it's just for being content aware
	* in some way (e.g. male vs. female, product vs. category)
	* static public $context_glue = "\u0004";	* 
	*/
	static public $context_glue = "\u0004";
	
	public static function setLanguage($lang)
	{
		self::$lang = $lang;
	}

	public static function loadTranslations($file)
	{
		if (is_file($file)) {
			$dictionary = include($file);

			if (is_array($dictionary)) {
				$domain = isset($dictionary['messages']['']['domain']) ? $dictionary['messages']['']['domain'] : null;
				unset($dictionary['messages']['']);
				self::addTranslations($dictionary['messages'], $domain);
			}
		}
	}

	public static function getTranslationsAsEntries($domain = null,$entries = false)
	{
		//TODO: debug
		//return new Entries;
		
		if(!$entries)
			$entries = new Entries;
		
		if (empty($domain))
			$domain = self::$domain;
		//try to get translations from dictionary
		if(!empty($entries)){
			foreach ($entries as $translator){
				if(isset(self::$dictionary[$domain][$translator->original][1])){
					$translator->setTranslation(self::$dictionary[$domain][$translator->original][1]);
				}
				elseif(isset(self::$dictionary[$domain][$translator->context.self::$context_glue.$translator->original][1])){
					$translator->setTranslation(self::$dictionary[$domain][$translator->context.self::$context_glue.$translator->original][1]);
				}
				if(isset(self::$dictionary[$domain][$translator->original][2])){
					$translator->setPluralTranslation(self::$dictionary[$domain][$translator->original][2],0);
				}
			}
		}
		elseif (!empty(self::$dictionary[$domain])) {

			foreach (self::$dictionary[$domain] as $original => $array_trans) {
				$context = '';
				$entries->insert($context, $original, $array_trans[0]);
				if ($translation = $entries->find($context, $original)) {
					$translation->setTranslation($array_trans[1]);
					if(!empty($array_trans[2]))
						$translation->setTranslation($array_trans[2]);
				}
			}
		}
		return $entries;
	}

	public static function addTranslations(array $dictionary, $domain = null)
	{
		if ($domain === null) {
			$domain = self::$domain;
		}

		if (!isset(self::$dictionary[$domain])) {
			self::$dictionary[$domain] = array();
		}

		self::$dictionary[$domain] = array_replace_recursive(self::$dictionary[$domain], $dictionary);
	}

	public static function clearTranslations()
	{
		self::$dictionary = array();
	}

	public static function getTranslation($domain, $context, $original)
	{
		$key = isset($context) ? $context . self::$context_glue . $original : $original;

		return isset(self::$dictionary[$domain][$key]) ? self::$dictionary[$domain][$key] : false;
	}

	public static function gettext($original)
	{
		return self::dpgettext(self::$domain, null, $original);
	}

	public static function ngettext($original, $plural, $value)
	{
		return self::dnpgettext(self::$domain, null, $original, $plural, $value);
	}

	public static function dngettext($domain, $original, $plural, $value)
	{
		return self::dnpgettext($domain, null, $original, $plural, $value);
	}

	public static function npgettext($context, $original, $plural, $value)
	{
		return self::dnpgettext(self::$domain, $context, $original, $plural, $value);
	}

	public static function pgettext($context, $original)
	{
		return self::dpgettext(self::$domain, $context, $original);
	}

	public static function dgettext($domain, $original)
	{
		return self::dpgettext($domain, null, $original);
	}

	public static function dpgettext($domain, $context, $original)
	{
		$translation = self::getTranslation($domain, $context, $original);

		if (isset($translation[1]) && $translation[1] !== '') {
			return $translation[1];
		}

		return $original;
	}

	public static function dnpgettext($domain, $context, $original, $plural, $value)
	{
		$key = self::isPlural($value);

		$translation = self::getTranslation($domain, $context, $original);

		if (isset($translation[$key]) && $translation[$key] !== '') {
//			return $translation[$key];
			return sprintf($translation[$key], $value);
		}

		return ($key === 1) ? sprintf($original, $value) : sprintf($original, $plural, $value);
	}

	public static function isPlural($n)
	{
		return ($n === 1) ? 1 : 2;
	}

}

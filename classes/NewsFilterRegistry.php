<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 * @package AVV
 * @author Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsPlus;


class NewsFilterRegistry
{
	protected static $instance = null;
	protected $arrValues = array();

	public static function getInstance()
	{
		if (self::$instance === null) self::$instance = new NewsFilterRegistry();
		return self::$instance;
	}

	protected function  __construct() {}

	private function  __clone() {}

	public static function set($index, $value)
	{
		$instance = self::getInstance();

		if (isset($instance->arrValues[$index]))
		{
			throw new \Exception("There is already an entry for key '$index'");
		}

		$instance->arrValues[$index] = $value;
	}

	public static function get($index)
	{
		$instance = self::getInstance();

		if (!isset($instance->arrValues[$index]))
		{
			throw new \Exception("There is no entry for key '$index'");
		}

		return $instance->arrValues[$index];
	}

	public static function reset($index)
	{
		$instance = self::getInstance();

		if (!isset($instance->arrValues[$index]))
		{
			throw new \Exception("There is no entry for key '$index'");
		}

		unset($instance->arrValues[$index]);
	}
}
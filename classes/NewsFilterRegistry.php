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

	public function set($index, $value)
	{
		if (isset($this->arrValues[$index]))
		{
			return false;
		}

		$this->arrValues[$index] = $value;
	}

	public function get($index)
	{
		if (isset($this->arrValues[$index]))
		{
			return $this->arrValues[$index];
		}
	}

	public function reset($index)
	{
		if (isset($this->arrValues[$index]))
		{
			unset($this->arrValues[$index]);
		}
	}
}
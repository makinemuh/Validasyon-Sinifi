<?php

namespace DemirPHP;

/**
 * Validasyon Sınıfı
 * Validasyon sınıfı ile verileri doğrulayabilirsiniz
 * @author Yılmaz Demir <demiriy@gmail.com>
 * @link http://demirphp.com
 * @package DemirPHP\Validation
 * @version 2.0
 */
class Validation
{
	/**
	 * @var array
	 */
	public static $fields = [];

	/**
	 * @var array
	 */
	public static $errors = [];

	/**
	 * @var string
	 */
	public static $field = null;

	/**
	 * @var string
	 */
	public static $title = null;

	/**
	 * @var array
	 */
	public static $messages = [
		'required' => '%s alanı gereklidir',
		'empty' => '%s alanı boş bırakılamaz',
		'email' => '%s alanı geçerli bir e-posta adresi içermiyor',
		'url' => '%s alanı geçerli bir URL içermiyor',
		'same' => '%s alanı diğeriyle uyuşmuyor, alanlar aynı değerleri içermelidir',
		'ip' => '%s alanı geçerli bir IP adresi içermiyor',
		'min' => '%s alanı çok kısa, en az %s karakter içermelidir',
		'max' => '%s alanı çok uzun, en fazla %s karakter girilebilir',
		'alpha' => '%s alanı sadece harf içerebilir (Türkçe karakter hariç)',
		'alnum' => '%s alanı sadece harf ve sayı içeriebilir (Türkçe karakter hariç)',
		'numeric' => '%s alanı sadece rakam içerebilir (0-9)',
		'float' => '%s alanı sadece kesirli/ondalık sayı içerebilir',
		'time' => '%s alanı geçerli bir tarih/zaman içermiyor',
		'upper' => '%s alanı yalnızca büyük harfler içerebilir (Türkçe karakter hariç)',
		'lower' => '%s alanı yalnızca küçük harfler içerebilir (Türkçe karakter hariç)',
	];

	/**
	 * @param array $fields
	 * @return void
	 */
	public static function fields(array $fields)
	{
		self::$fields = $fields;
		return new self;
	}

	/**
	 * @param string $name
	 * @param mixed $title
	 * @return void
	 */
	public static function field($name, $title = FALSE)
	{
		self::$field = $name;
		self::$title = null;

		if ($title !== FALSE) {
			self::$title = $title;
		}
		return new self;
	}

	/**
	 * @return mixed
	 */
	private static function getField()
	{
		return isset(self::$fields[self::$field]) ? self::$fields[self::$field] : FALSE;
	}

	/**
	 * @return boolean
	 */
	private static function hasField()
	{
		return isset(self::$fields[self::$field]) && !empty(self::$fields[self::$field]);
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function required($message = FALSE)
	{
		if (!isset(self::$fields[self::$field])) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['required'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function empty($message = FALSE)
	{
		if (isset(self::$fields[self::$field]) && empty(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['empty'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function email($message = FALSE)
	{
		if (self::hasField() && !filter_var(self::getField(), FILTER_VALIDATE_EMAIL)) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['email'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function url($message = FALSE)
	{
		if (self::hasField() && !filter_var(self::getField(), FILTER_VALIDATE_URL)) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['url'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function ip($message = FALSE)
	{
		if (self::hasField() && !filter_var(self::getField(), FILTER_VALIDATE_IP)) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['ip'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function float($message = FALSE)
	{
		if (self::hasField() && !filter_var(self::getField(), FILTER_VALIDATE_FLOAT)) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['float'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param integer $max
	 * @param mixed $message
	 * @return void
	 */
	public static function max($max = 255, $message = FALSE)
	{
		if (self::hasField() && mb_strlen(self::getField(), 'UTF-8') > $max) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['max'],
				empty(self::$title) ? self::$field : self::$title, $max
			);
		}
		return new self;
	}

	/**
	 * @param integer $min
	 * @param mixed $message
	 * @return void
	 */
	public static function min($min = 3, $message = FALSE)
	{
		if (self::hasField() && mb_strlen(self::getField(), 'UTF-8') < $min) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['min'],
				empty(self::$title) ? self::$field : self::$title, $min
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function time($message = FALSE)
	{
		$dateArr = date_parse(self::getField());
		if (self::hasField() && $dateArr['error_count'] > 0) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['time'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param string $field
	 * @param mixed $message
	 * @return void
	 */
	public static function same($field, $message = FALSE)
	{
		if (self::hasField() && self::getField() !== (isset(self::$fields[$field]) ? self::$fields[$field] : false)) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['same'],
				empty(self::$title) ? self::$field : self::$title, $field
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function alpha($message = FALSE)
	{
		if (self::hasField() && !ctype_alpha(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['alpha'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function upper($message = FALSE)
	{
		if (self::hasField() && !ctype_upper(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['upper'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function lower($message = FALSE)
	{
		if (self::hasField() && !ctype_lower(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['lower'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function alnum($message = FALSE)
	{
		if (self::hasField() && !ctype_alnum(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['alnum'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @param mixed $message
	 * @return void
	 */
	public static function numeric($message = FALSE)
	{
		if (self::hasField() && !is_numeric(self::getField())) {
			self::$errors[self::$field] = sprintf(
				$message ? $message : self::$messages['numeric'],
				empty(self::$title) ? self::$field : self::$title
			);
		}
		return new self;
	}

	/**
	 * @return boolean
	 */
	public static function valid()
	{
		return empty(self::$errors);
	}

	/**
	 * @param string $field
	 * @return array|boolean
	 */
	public static function getError($field)
	{
		return isset(self::$errors[$field]) ? self::$errors[$field] : false;
	}

	/**
	 * @return array
	 */
	public static function getErrors()
	{
		return self::$errors;
	}

	/**
	 * @return string
	 */
	public static function getErrorsAsString()
	{
		$result = null;
		if (!empty(self::$errors)) {
			foreach (self::$errors as $key => $error) {
				$result .= $error . '<br>';
			}
		}
		return $result;
	}
}

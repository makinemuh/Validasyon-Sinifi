<?php

namespace DemirPHP;

class Validation
{
	/**
	 * @var array
	 */
	public static $rules = [];

	/**
	 * @var array
	 */
	public static $data = [];

	/**
	 * @var array
	 */
	public static $errors = [];

	/**
	 * @var array
	 */
	public static $names = [];

	/**
	 * @var array
	 */
	public static $messages = [
		'required' => '%s alanı gereklidir',
		'notNull' => '%s alanı boş bırakılamaz',
		'max' => '%s alanının uzunluğu en fazla %s karakter olabilir',
		'min' => '%s alanının uzunluğu en az %s karakter olabilir',
		'same' => '%s alanı %s alanıyla aynı olmalıdır',
		'time' => '%s alanı geçerli bir tarih/saat içermiyor',
		'email' => '%s alanı geçerli bir e-posta adresi değil',
		'alphanumeric' => '%s alanı yalnızca harf ve rakam içermelidir',
		'alpha' => '%s alanı yalnızca harf içermelidir',
		'numeric' => '%s alanı yalnızca nümerik olabilir',
		'ip' => '%s alanı geçerli bir IP adresi içermiyor',
		'upper' => '%s alanı yalnızca büyük harfler içerebilir',
		'lower' => '%s alanı yalnızca küçük harfler içerebilir'
	];

	/**
	 * Kuralları belirler
	 * @param array $rules
	 * @return null
	 */
	public static function setRules(array $rules)
	{
		foreach ($rules as $field => $rule) {
			self::$rules[$field] = self::separateRules($rule);
		}
	}

	/**
	 * İsimleri belirler
	 * @return null
	 */
	private static function setNames()
	{
		foreach (self::$rules as $field => $rules) {
			if (in_array('name', $rules['rules'])) {
				self::$names[$field] = $rules['options']['name'];
			}
		}
	}

	/**
	 * Kuralları dizgeden diziye ayrıştırır
	 * @param string $rules
	 * @return array
	 */
	private static function separateRules($rules)
	{
		$rules = explode('|', $rules);
		$arrayRules = [];

		foreach ($rules as $key => $rule) {
			$option = explode(':', $rule);
			if (isset($option[1])) {
				$arrayRules['rules'][] = $option[0];
				$arrayRules['options'][$option[0]] = $option[1];
			} else {
				$arrayRules['rules'][] = $option[0];
			}
		}
		return $arrayRules;
	}

	/**
	 * Validasyondan geçecek veriyi belirler
	 * @param array $data
	 * @return array
	 */
	public static function setData(array $data)
	{
		return self::$data = $data;
	}

	/**
	 * Validasyon sağlar
	 * @param array $rules
	 * @param array $data
	 * @return void
	 */
	public static function validate(array $rules, array $data)
	{
		self::setRules($rules);
		self::setData($data);
		return self::isValid();
	}

	/**
	 * Gerekli alanı kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function required($field)
	{
		if (isset(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'required');
			return false;
		}
	}

	/**
	 * Alanın boş olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function notNull($field)
	{
		if (isset(self::$data[$field]) && !empty(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'notNull');
			return false;
		}
	}

	/**
	 * Alanın en fazla içereceği karakteri kontrol eder
	 * @param string $field
	 * @param string $option
	 * @return boolean
	 */
	private static function max($field, $option)
	{
		if (isset(self::$data[$field]) && mb_strlen(self::$data[$field]) <= $option) {
			return true;
		} else {
			self::setError($field, 'max', $option);
			return false;
		}
	}

	/**
	 * Alanın en az içereceği karakteri kontrol eder
	 * @param string $field
	 * @param string $option
	 * @return boolean
	 */
	private static function min($field, $option)
	{
		if (isset(self::$data[$field]) && mb_strlen(self::$data[$field]) >= $option) {
			return true;
		} else {
			self::setError($field, 'min', $option);
			return false;
		}
	}

	/**
	 * Alanın diğer bir alanla aynı mı kontrol eder
	 * @param string $field
	 * @param string $option
	 * @return boolean
	 */
	private static function same($field, $option)
	{
		if (isset(self::$data[$field]) && isset(self::$data[$option]) && self::$data[$field] === self::$data[$option]) {
			return true;
		} else {
			self::setError($field, 'same', $option);
			return false;
		}
	}

	/**
	 * Geçerli bir zaman dizgesi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function time($field)
	{
		$dateArr = date_parse(self::$data[$field]);
		if (isset(self::$data[$field]) && $dateArr['error_count'] <= 0) {
			return true;
		} else {
			self::setError($field, 'time');
			return false;
		}
	}

	/**
	 * Geçerli bir e-posta adresi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function email($field)
	{
		if (isset(self::$data[$field]) && filter_var(self::$data[$field], FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			self::setError($field, 'email');
			return false;
		}
	}

	/**
	 * Geçerli bir alfanumerik dizgesi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function alphanumeric($field)
	{
		if (isset(self::$data[$field]) && ctype_alnum(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'alphanumeric');
			return false;
		}
	}

	/**
	 * Geçerli bir alfa dizgesi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function alpha($field)
	{
		if (isset(self::$data[$field]) && ctype_alpha(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'alpha');
			return false;
		}
	}

	/**
	 * Değerin nümerik olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function numeric($field)
	{
		if (isset(self::$data[$field]) && is_numeric(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'numeric');
			return false;
		}
	}

	/**
	 * Geçerli bir IP adresi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function ip($field)
	{
		if (isset(self::$data[$field]) && filter_var(self::$data[$field], FILTER_VALIDATE_IP)) {
			return true;
		} else {
			self::setError($field, 'ip');
			return false;
		}
	}

	/**
	 * Geçerli bir zaman kesirli sayı olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function float($field)
	{
		if (isset(self::$data[$field]) && filter_var(self::$data[$field], FILTER_VALIDATE_FLOAT)) {
			return true;
		} else {
			self::setError($field, 'float');
			return false;
		}
	}

	/**
	 * Geçerli bir url dizgesi olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function url($field)
	{
		if (isset(self::$data[$field]) && filter_var(self::$data[$field], FILTER_VALIDATE_URL)) {
			return true;
		} else {
			self::setError($field, 'url');
			return false;
		}
	}

	/**
	 * Dizgenin küçük harfli olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function lower($field)
	{
		if (isset(self::$data[$field]) && ctype_lower(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'lower');
			return false;
		}
	}

	/**
	 * Dizgenin küçük harfli olup olmadığını kontrol eder
	 * @param string $field
	 * @return boolean
	 */
	private static function upper($field)
	{
		if (isset(self::$data[$field]) && ctype_upper(self::$data[$field])) {
			return true;
		} else {
			self::setError($field, 'upper');
			return false;
		}
	}

	/**
	 * Validasyon yapar
	 * @return boolean
	 */
	public static function isValid()
	{
		self::setNames();
		foreach (self::$rules as $field => $rule) {
			$rules = $rule['rules'];
			$options = @$rule['options'];

			foreach ($rules as $_rule) {
				if (method_exists(new self, $_rule)) {
					if (isset($options[$_rule])) {
						self::$_rule($field, $options[$_rule]);
					} else {
						self::$_rule($field);
					}
				}
			}
		}
		return empty(self::$errors);
	}

	/**
	 * Validasyon yapar
	 * @param string $field
	 * @param string $rule
	 * @param string $option
	 * @return null
	 */
	private static function setError($field, $rule, $option = null)
	{
		if (isset(self::$names[$field])) {
			self::$errors[$field] = sprintf(
				self::$messages[$rule], 
				self::$names[$field], 
				isset(self::$names[$option]) ? self::$names[$option] : $option
			);
		} else {
			self::$errors[$field] = sprintf(
				self::$messages[$rule], 
				$field, 
				isset(self::$names[$option]) ? self::$names[$option] : $option
			);
		}
	}

	/**
	 * Hataları döndürür
	 * @return array
	 */
	public static function getErrors()
	{
		return self::$errors;
	}

	/**
	 * Hataları döndürür
	 * @return string
	 */
	public static function getErrorsAsString()
	{
		return implode('<br>', self::$errors);
	}

	/**
	 * Özel hata ekler
	 * @param string $error
	 * @param string $field
	 * @return null
	 */
	public static function addError($error, $field = null)
	{
		if (is_null($field)) {
			self::$errors[] = $error;
		} else {
			self::$errors[$field] = $error;
		}
	}

	/**
	 * Hızlı validasyon
	 * @param mixed $data
	 * @param string $rule
	 * @return void
	 */
	public static function check($data, $rule)
	{
		self::$data['_data'] = $data;
		$rule = explode(':', $rule);
		$option = isset($rule[1]) ? $rule[1] : false;
		$rule = $rule[0];

		if (method_exists(new self, $rule)) {
			if ($option !== false) {
				return self::$rule('_data', $option);
			} else {
				return self::$rule('_data');
			}
		} else {
			throw new \Exception('Böyle bir kural tanımlı değil: ' . $rule);
		}
	}
}

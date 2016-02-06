<?php

/**
 * Validasyon Sınıfı
 * Validasyon sınıfı ile verileri doğrulayabilirsiniz
 * @author Yılmaz Demir <demiriy@gmail.com>
 * @link http://demirphp.com
 * @package Validation
 * @version 1.0
 */
class Validation
{
	/**
	 * Başlatıcıdan gelen alanları tutar
	 * @var array
	 * @access private
	 */
	private $fields = [];

	/**
	 * Hata mesajlarını tutar
	 * @var array
	 * @access private
	 */
	private $errors = [];

	/**
	 * Anahtar alanını tutar
	 * @var string
	 * @access private
	 */
	private $field;

	/**
	 * Anahtarın değerini tutar
	 * @var string
	 * @access private
	 */
	private $value;

	/**
	 * Anahtarın adını tutar
	 * @var string
	 * @access private
	 */
	private $name;

	/**
	 * Hata mesajları sabitleri
	 */
	const _EMPTY = 'Lütfen <strong>%s</strong> alanını doldurunuz';
	const _EMAIL = '<strong>%s</strong> alanına geçerli bir e-posta adresi giriniz';
	const _URL = '<strong>%s</strong> alanına geçerli bir URL giriniz';
	const _SAME = '<strong>%s</strong> alanı diğeriyle uyuşmuyor, lütfen aynı değeri giriniz';
	const _IP = '<strong>%s</strong> alanına geçerli bir IP adresi giriniz';
	const _MIN = '<strong>%s</strong> alanı çok kısa, en az <strong>%s</strong> karakter girilebilir';
	const _MAX = '<strong>%s</strong> alanı çok uzun, en fazla <strong>%s</strong> karakter girilebilir';
	const _ALPHA = '<strong>%s</strong> alanına sadece harf girilebilir (Türkçe karakterler hariç)';
	const _ALPHANUM = '<strong>%s</strong> alanına sadece harf ve sayı girilebilir (Türkçe karakterler hariç)';
	const _INT = '<strong>%s</strong> alanına sadece rakam girilebilir (0-9)';
	const _FLOAT = '<strong>%s</strong> alanına sadece kesirli/ondalık sayılar girilebilir';
	const _TIME = '<strong>%s</strong> alanı geçerli bir tarih/zaman içermiyor';

	/**
	 * Sınıf başlatıcı
	 * @param array [$fields]
	 * @access public
	 * @example (new Validation($_POST))
	 */
	public function __construct(array $fields = [])
	{
		if (!empty($fields)) $this->fields = $fields;
	}

	/**
	 * Validasyondan geçirilecek alanları belirler
	 * @param array [$fields]
	 * @access public
	 * @example $obj->fields($_POST);
	 */
	public function fields(array $fields)
	{
		$this->fields = $fields;
		return $this;
	}

	/**
	 * Alan belirteci
	 * @param string [$field]
	 * @param string [$name]
	 * @access public
	 * @example $obj->field('name', 'İsim Soyisim')
	 */
	public function field($field, $name = null)
	{
		if ($this->checkField($field)) {
			$this->field = $field;
			$this->value = $this->fields[$field];
			$this->name = $name;
		} else {
			$this->errors['_invalidField'] = "Validasyondan geçecek veriler arasında <b>{$name}</b> alanı yok";
			$this->field = $field;
			$this->value = null;
			$this->name = $name;
		}

		if (is_null($name)) throw new \Exception('Validasyon alanının ismini giriniz: ' . $field);

		return $this;
	}

	/**
	 * Validasyonun geçerliliğini döndürür
	 * @return boolean
	 * @access public
	 * @example $obj->valid()
	 */
	public function valid()
	{
		if (empty($this->errors)) return true;
		return false;
	}

	/**
	 * Alanın boş olup olmadığnı kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('name', 'Ad')->required()
	 */
	public function required()
	{
		if (empty($this->value)) {
			$this->errors[$this->field] = sprintf(self::_EMPTY, $this->name);
		}
		return $this;
	}

	/**
	 * E-posta adresi kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('email', 'E-posta')->isEmail()
	 */
	public function email()
	{
		if (!empty($this->value) && !filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
			$this->errors[$this->field] = sprintf(self::_EMAIL, $this->name);
		}
		return $this;
	}

	/**
	 * URL kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('url', 'Web sitesi')->url()
	 */
	public function url()
	{
		if (!empty($this->value) && !filter_var($this->value, FILTER_VALIDATE_URL)) {
			$this->errors[$this->field] = sprintf(self::_URL, $this->name);
		}
		return $this;
	}

	/**
	 * Eşleştirme yapar
	 * @param string [$filed]
	 * @return void
	 * @access public
	 * @example $obj->field('pass', 'Şifre')->same('repass')
	 */
	public function same($field)
	{
		if ($this->checkField($field)) {
			if ($this->value != $this->fields[$field]) {
				$this->errors[$this->field] = sprintf(self::_SAME, $this->name);
			}
		} else {
			throw new \Exception('Validasyondan geçirilmek istenen alan dizi içerisinde yok: ' . $field);
		}
		return $this;
	}

	/**
	 * IP adresi kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('ip', 'IP Adresi')->ip()
	 */
	public function ip()
	{
		if(!empty($this->value) && !filter_var($this->value, FILTER_VALIDATE_IP)) {
			$this->errors[$this->field] = sprintf(self::_IP, $this->name);
		}
		return $this;
	}

	/**
	 * En az girilebilecek karakteri belirler
	 * @param integer|null [$legth]
	 * @return void
	 * @access public
	 * @example $obj->field('username', 'Kullanıcı Adı')->min(5)
	 */
	public function min($length = null)
	{
		if (is_null($length)) $length = 5;
		if (strlen($this->value) < $length) {
			$this->errors[$this->field] = sprintf(self::_MIN, $this->name, $length);
		}
		return $this;
	}

	/**
	 * En fazla girilebilecek karakteri belirler
	 * @param integer|null [$legth]
	 * @return void
	 * @access public
	 * @example $obj->field('username', 'Kullanıcı Adı')->max(15)
	 */
	public function max($length = null)
	{
		if (is_null($length)) $length = 255;
		if (strlen($this->value) > $length) {
			$this->errors[$this->field] = sprintf(self::_MAX, $this->name, $length);
		}
		return $this;
	}

	/**
	 * Girdi sadece harflerden mi oluşuyor, kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('username', 'Kullanıcı Adı')->alpha()
	 */
	public function alpha()
	{
		if(!empty($this->value) && !ctype_alpha($this->value)) {
			$this->errors[$this->field] = sprintf(self::_ALPHA, $this->name);
		}
		return $this;
	}

	/**
	 * Girdi rakam ve nümerik karakterlerden mi oluşuyor kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('username', 'Kullanıcı Adı')->alphanumeric()
	 */
	public function alphanumeric()
	{
		if(!empty($this->value) && !ctype_alnum($this->value)) {
			$this->errors[$this->field] = sprintf(self::_ALPHANUM, $this->name);
		}
		return $this;
	}

	/**
	 * Girdi sayı mı kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('age', 'Yaş')->numeric()
 	 */
	public function numeric()
	{
		if(!empty($this->value) && !is_numeric($this->value)) {
			$this->errors[$this->field] = sprintf(self::_INT, $this->name);
		}
		return $this;
	}

	/**
	 * Girdi kesirli/ondalık sayı mı kontrol eder
	 * @return void
	 * @access public
	 * @example $obj->field('cost', 'Tutar')->float()
 	*/
	public function float()
	{
		if(!empty($this->value) && !filter_var($this->value, FILTER_VALIDATE_FLOAT)) {
			$this->errors[$this->field] = sprintf(self::_FLOAT, $this->name);
		}
		return $this;
	}

	/**
	 * Girdi geçerli bir tarih/zaman mı bunu doğrular
	 * Sadece tarih veya sadece saat de doğrulayabilir
	 * @return void
	 * @access public
	 * @example $obj->field('created', 'Tarih')->time()
	 */
	public function time()
	{
		$dateArr = date_parse($this->value);

		if (!empty($this->value) && $dateArr['error_count'] > 0) {
			$this->errors[$this->field] = sprintf(self::_TIME, $this->name);
		}
		return $this;
	}

	/**
	 * Hataları döndürür
	 * @return array
	 * @access public
	 * @example print_r($obj->getErrors())
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Alan için hata varsa döndürür
	 * @param string [$field]
	 * @return string|boolean
	 * @access public
	 * @example $obj->getError('name')
	 */
	public function getError($field)
	{
		if ($this->checkField($field)) {
			return isset($this->errors[$field]) ? $this->errors[$field] : false;
		}
		return false;
	}

	/**
	 * Hataları string olarak yazdırır
	 * @return string [$errors]
	 * @access public
	 * @example echo $obj->getErrorsAsString
	 */
	public function getErrorsAsString()
	{
		$errors = null;
		foreach ($this->errors as $error) {
			$errors .= $error . "<br>\n";
		}
		return $errors;
	}

	/**
	 * Alanın olup olmadığını kontrol eder
	 * @param string [$key]
	 * @return boolean
	 * @access private
	 */
	private function checkField($key)
	{
		return array_key_exists($key, $this->fields);
	}
}

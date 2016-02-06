# Validasyon Sınıfı
Veri doğrulama sınıfı. Bu sınıf genelde, formdan veya dizeden gelen verilerin geçerliliğini ve istediğimiz gibi olup olmadığını kontrol etmek amacıyla kullanılır.

## Kullanımı
Kullanımı çok basittir. Örnek olarak göstereceğim senaryoda, formdan gelen veriyi validasyondan geçireceğiz.

```php
$v = new Validation($_POST);
// veya şu şekilde de kullanılabilir:
$v = new Validation;
$v->fields($_POST);
```
Burada sınıfımızı başlattık, şimdi doğrulamak ve kontrol etmek istediğimiz verileri gözden geçirelim.
```php
$v->field('title', 'Başlık')->required()->max(255);
$v->field('body', 'İçerik')->required()->min(10);
```
Burada, formdan gelen `title` isimli form alanını gerekli bir alan ve en fazla 255 karakterden oluşabilecek bir alan yaptık. Bunun geçerli olup olmadığını şu şekilde kontrol ediyoruz:
```php
if ($v->valid()) {
  // Gelen form verisi istediğimiz gibi, örn. veritabanına yazdırabiliriz
} else {
  // Gelen form verisi istediğimiz gibi değil, hataları yazdıralım:
  echo $v->getErrorsAsString();
}
```
## Özellikler
- `fields($array)` Kontrol edilecek alanları belirler
- `fields($field, $name)` Kontrol edilecek alanı ve adını tanımlar
- `valid()` Kontrol sonrası validasyonun geçerli olup olmadığını boolean döndürür
- `required()` Alanı gerekli ve doldurulması zorunlu yapar
- `email()` Geçerli bir e-posta adresi girilmiş mi kontrol eder
- `url()` Geçerli bir URL mi kontrol eder
- `same($field)` Girilen değerin, belirtilenle aynı olup olmadığının kontrolünü yapar
- `ip()` Geçerli bir IP adresi mi kontrol eder
- `min($length = null)` En az kaç karakterden oluşacağını kontrol eder, parametre verilmezse, 5 belirler
- `max($length = null)` En fazla kaç karakterden oluşacağını kontrol eder, parametre verilmezse, 255 belirler
- `alpha()` Girilen değerin, sadece harflerden oluşup oluşmadığını kontrol eder
- `alphanumeric()` Girilen değerin, sadece harf ve rakamlardan oluşup oluşmadığını kontrol eder
- `numeric()` Girilen değer, rakam/sayı mı kontrol eder
- `float()` Girilen değerin, kesirli bir sayı olup olmadığını kontrol eder
- `time()` Girilen değerin bir tarih/saat dizgesi olup olmadığını kontrol eder
- `getErrors()` Hatalar varsa, dize halinde döndürür
- `getError($name)` Belirli alana ait varsa hatayı döndürür
- `getErrorsAsString()` Dizge biçiminde hataları döndürür

## Örnek Kullanımı
```php
require 'Validation.php';
$v = new Validation($_POST);
$v->field('username', 'Kullanıcı Adı')->required()->alphanumeric()->max(50)->min(4);
$v->field('password', 'Şifre')->required()->same('password2');
$v->field('birthday', 'Doğum Tarihi')->required()->time();
$v->field('email', 'E-posta')->required()->email();

if ($v->valid()) {
  // Doğrulama başarılı
  $sql = 'INSERT INTO members ...';
} else {
  // Hataları göster
  echo $v->getErrorsAsString();
}
```

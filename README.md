# holidays

日本の休日・祝日判定・祝日の名称を取得

<br>

## packagist

https://packagist.org/packages/kanagama/holidays

<br>


## 使い方

composer でインストールします

```bash
# install の場合
composer require kanagama/holidays

# update の場合
composer update -w kanagama/holidays
```

使いたいクラスで use するだけです

```php
use Kanagama\Holidays\Holidays;
```

<br>

## メソッド一覧


### checkPublicHoliday(int $year, int $month, int $day): bool

指定日が祝日であれば true を返却します

```php
$holidays = new Holidays();
// true （春分の日）
$holiday = $holidays->checkPublicHoliday(2023, 3, 21);
```

<br>

### getPublicHolidayName(int $year, int $month, int $day): ?string

指定日の祝日名を返却します。指定日が祝日でない場合は null を返却します。


```php
$holidays = new Holidays();
// 春分の日
$holidayName = $holidays->getPublicHolidayName(2023, 3, 21);
```

<br>

### checkHoliday(int $year, int $month, int $day): bool

指定日が祝日もしくは土日であれば true を返却します。

```php
$holidays = new Holidays();
// true （土曜）
$holiday = $holidays->checkHoliday(2023, 3, 19);
```

<br>

### checkDayBeforePublicHoliday(int $year, int $month, int $day): bool

指定日が祝前日であれば true を返却します。

```php
$holidays = new Holidays();
// true （翌日が春分の日）
$holiday = $holidays->checkDayBeforePublicHoliday(2023, 3, 20);
```

<br>

### checkDayAfterPublicHoliday(int $year, int $month, int $day): bool

指定日が祝後日であれば true を返却します。

```php
$holidays = new Holidays();
// true （前日が春分の日）
$holiday = $holidays->checkDayAfterPublicHoliday(2023, 3, 22);
```

<br>

### addPublicHoliday(int $year, int $month, int $day, string $holidayName): void

指定日をオレオレ祝日に設定します。既に祝日設定されている場合は上書きされます。


```php
$holidays = new Holidays();
// true （前日が春分の日）
$holiday = $holidays->addPublicHoliday(2023, 3, 20, '設立記念日');
```

※他のファンクションでも同様に、指定した日が祝日判定されます。
getPublicHolidayName() でも名称が取得されます。

<br>
<br>

## 全てのメソッドが静的に呼び出せます

```php
# example
$result = Holidays::checkPublicHoliday(2023, 3, 21);
$result = Holidays::getPublicHolidayName(2023, 3, 21);
$result = Holidays::checkHoliday(2023, 3, 21);
$result = Holidays::checkDayBeforePublicHoliday(2023, 3, 21);
$result = Holidays::checkDayAfterPublicHoliday(2023, 3, 21);
$result = Holidays::addPublicHoliday(2023, 3, 20, '設立記念日');
```


<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Запуск юнит-тестов
 *
 * @run php run.php tests - Запуск всех тестов (запускает тесты из директории "tests")
 * @run php run.php tests.php - Запуск теста из конкретного файла (например, "tests/Classes/ObjectToolsTest.php")
 */

$vendorPath = dirname(__DIR__) . '/vendor';

require_once("{$vendorPath}/autoload.php");

/**
 * Каталог, в котором размещен PhpUnit
 * @var string $phpUnitPath
 */
$phpUnitPath = dirname(__DIR__) . '/vendor/phpunit/phpunit/phpunit';

if ($phpUnitPath !== '')
{
    // получаем PHP код "консольного приложения PhpUnit" и выбрасываем из него declare(strict_types=1);
    $phpUnitCodeExecutor = explode("\n", file_get_contents($phpUnitPath));
    unset($phpUnitCodeExecutor[0], $phpUnitCodeExecutor[1]);

    /**
     * Установка UTC часового пояса
     * функции по работе с временем, оттестированы для работы в UTC
     *
     * @link https://en.wikipedia.org/wiki/List_of_tz_database_time_zones Список часовых поясов
     *
     * @todo При доработке функций для работы с датой-временем, под часовые пояса, необходимо будет убрать эту настройку
     *       Необходимо будет проверять все функции в разных часовых поясах, а также в летнем и зимне времени
     */
    date_default_timezone_set('UTC'); // UTC (0-вой часовой пояс)
    //date_default_timezone_set('Europe/Moscow'); // Москва (+3-вой часовой пояс)

    eval(implode($phpUnitCodeExecutor));
}
else
{
    die("Not found phpUnit library: {$phpUnitPath}");
}

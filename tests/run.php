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

$vendorAutoloaderPath = dirname(__DIR__) . '/vendor/autoload.php';
require_once($vendorAutoloaderPath);

/**
 * Каталог, в котором размещен PhpUnit (файл сборки фреймворка)
 * @var string $phpUnitPath
 */
$phpUnitPath = dirname(__DIR__) . '/vendor/phpunit/phpunit/phpunit';

if (file_exists($phpUnitPath))
{
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

    // Загрузка и запуск PHPUnit - Начало

    // TODO PHP8.2 при переходе на более новую версию PHP, надо попробовать удалить exit((new PHPUnit\TextUI\Application)->run($_SERVER['argv']));
    //             и define('PHPUNIT_COMPOSER_INSTALL', $file) так как сейчас не срабатывают проверки указанные в /vendor/phpunit/phpunit/phpunit

    define('PHPUNIT_COMPOSER_INSTALL', $vendorAutoloaderPath);
    /** @psalm-suppress PossiblyUndefinedArrayOffset TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run() в этом месте кода */
    $consoleArgs = $_SERVER['argv'];
    /** @psalm-suppress InternalMethod TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run()в этом месте кода */
    $application = new PHPUnit\TextUI\Application();
    /** @psalm-suppress InternalMethod TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run() в этом месте кода */
    exit($application->run($consoleArgs));

    // Получаем PHP код "консольного приложения PhpUnit" и выбрасываем из него declare(strict_types=1);
    // Нужно, чтобы запустить юнит-тесты через eval().
    // Запускаем их через eval() для того, что бы была возможность обернуть своим кодом
    /** @psalm-suppress UnevaluatedCode TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run() выше по коду */
    $phpUnitCodeExecutor = explode("\n", file_get_contents($phpUnitPath));
    /** @psalm-suppress UnevaluatedCode TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run() выше по коду */
    unset($phpUnitCodeExecutor[0], $phpUnitCodeExecutor[1]);

    /** @psalm-suppress UnevaluatedCode TODO PHP8.2 Можно будет удалить, как только уберем вызов (new PHPUnit\TextUI\Application)->run() выше по коду */
    eval(implode($phpUnitCodeExecutor));

    // Загрузка и запуск PHPUnit - Конец
}
else
{
    die("Not found phpUnit library: {$phpUnitPath}");
}

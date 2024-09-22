<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Code;

use DraculAid\PhpTools\tests\Code\ScriptLoaderTest;

/**
 * Функции загрузки кода (`include()` и `require()`) с изоляцией переменных и перехватом потока вывода
 *
 * @see ObHelper Позволяет перехватить поток вывода для функции или языковой конструкции
 * @link https://www.php.net/manual/ru/ref.outcontrol.php Функии PHP для контроля вывода
 *
 * Оглавление
 * <br>--- Изолированный вызов
 * <br>{@see ScriptLoader::exeRequire()} с помощью `require()`
 * <br>{@see ScriptLoader::exeRequireOnce()} с помощью `require_once()`
 * <br>{@see ScriptLoader::exeInclude()} с помощью `include()`
 * <br>{@see ScriptLoader::exeIncludeOnce()} с помощью `include_once()`
 * <br>{@see ScriptLoader::exeEval()} с помощью `eval()`
 * <br>--- Изолированный вызов и перехват потока вывода
 * <br>{@see ScriptLoader::obRequire()} с помощью `require()`
 * <br>{@see ScriptLoader::obRequireOnce()} с помощью `require_once()`
 * <br>{@see ScriptLoader::obInclude()} с помощью `include()`
 * <br>{@see ScriptLoader::obIncludeOnce()} с помощью `include_once()`
 * <br>{@see ScriptLoader::obEval()} с помощью `eval()`
 *
 * Test cases for class {@see ScriptLoaderTest}
 */
final class ScriptLoader
{
    /**
     * Изолированно загрузит файл с помощью `require()`, перехватит поток вывода
     *
     * @param   string   $path        Путь к загружаемому скрипту
     * @param   array    $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array    $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     *
     * @return  mixed   Вернет результат выполнения `require()`
     *
     * @todo PHP8 Типизация ответа функции
     */
    public static function exeRequire(string $path, array $arguments = [], array $use = [])
    {
        $___dracul_aid_php_tools___script_loader___arguments__path___ = $path;
        unset($path);

        if (count($arguments) > 0) extract($arguments);
        if (count($use) > 0) extract($use, EXTR_REFS);

        return require($___dracul_aid_php_tools___script_loader___arguments__path___);
    }

    /**
     * Изолированно загрузит файл с помощью `require_once()`, перехватит поток вывода
     *
     * @param   string   $path        Путь к загружаемому скрипту
     * @param   array    $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array    $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     *
     * @return  mixed   Вернет результат выполнения `require_once()`
     *
     * @todo PHP8 Типизация ответа функции
     */
    public static function exeRequireOnce(string $path, array $arguments = [], array $use = [])
    {
        $___dracul_aid_php_tools___script_loader___arguments__path___ = $path;
        unset($path);

        if (count($arguments) > 0) extract($arguments);
        if (count($use) > 0) extract($use, EXTR_REFS);

        return require_once($___dracul_aid_php_tools___script_loader___arguments__path___);
    }

    /**
     * Изолированно загрузит файл с помощью `include()`, перехватит поток вывода
     *
     * @param   string   $path        Путь к загружаемому скрипту
     * @param   array    $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array    $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     *
     * @return  mixed   Вернет результат выполнения `include()`
     *
     * @todo PHP8 Типизация ответа функции
     */
    public static function exeInclude(string $path, array $arguments = [], array $use = [])
    {
        $___dracul_aid_php_tools___script_loader___arguments__path___ = $path;
        unset($path);

        if (count($arguments) > 0) extract($arguments);
        if (count($use) > 0) extract($use, EXTR_REFS);

        return include($___dracul_aid_php_tools___script_loader___arguments__path___);
    }

    /**
     * Изолированно загрузит файл с помощью `include_once()`, перехватит поток вывода
     *
     * @param   string   $path        Путь к загружаемому скрипту
     * @param   array    $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array    $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     *
     * @return  mixed   Вернет результат выполнения `include_once()`
     *
     * @todo PHP8 Типизация ответа функции
     */
    public static function exeIncludeOnce(string $path, array $arguments = [], array $use = [])
    {
        $___dracul_aid_php_tools___script_loader___arguments__path___ = $path;
        unset($path);

        if (count($arguments) > 0) extract($arguments);
        if (count($use) > 0) extract($use, EXTR_REFS);

        return include_once($___dracul_aid_php_tools___script_loader___arguments__path___);
    }

    /**
     * Изолированно выполнит PHP код с помощью `eval()`, перехватит поток вывода
     *
     * @param   string   $phpCode     Выполняемый PHP код
     * @param   array    $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array    $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     *
     * @return  mixed   Вернет результат выполнения `eval()`
     *
     * @todo PHP8 Типизация ответа функции
     *
     * @psalm-suppress UnusedParam Аргументы функции используются, просто псалм вообще не понимает, что тут происходит
     */
    public static function exeEval(string $phpCode, array $arguments = [], array $use = [])
    {
        $___dracul_aid_php_tools___script_loader___arguments__php_code___ = $phpCode;
        unset($phpCode);

        if (count($arguments) > 0) extract($arguments);
        if (count($use) > 0) extract($use, EXTR_REFS);

        return eval($___dracul_aid_php_tools___script_loader___arguments__php_code___);
    }

    /**
     * Изолированно загрузит файл с помощью `require()`, перехватит поток вывода
     *
     * @param   string       $path        Путь к загружаемому скрипту
     * @param   array        $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array        $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     * @param   mixed        $_return     Результат выполнения `require()`
     *
     * @return  string   Вернет перехваченный поток вывода
     */
    public static function obRequire(string $path, array $arguments = [], array $use = [], &$_return = null): string
    {
        ob_start();

        $_return = self::exeRequire($path, $arguments, $use);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Изолированно загрузит файл с помощью `require_once()`, перехватит поток вывода
     *
     * @param   string       $path        Путь к загружаемому скрипту
     * @param   array        $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array        $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     * @param   mixed        $_return     Результат выполнения `require_once()`
     *
     * @return  string   Вернет перехваченный поток вывода
     */
    public static function obRequireOnce(string $path, array $arguments = [], array $use = [], &$_return = null): string
    {
        ob_start();

        $_return = self::exeRequireOnce($path, $arguments, $use);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Изолированно загрузит файл с помощью `include()`, перехватит поток вывода
     *
     * @param   string       $path        Путь к загружаемому скрипту
     * @param   array        $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array        $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     * @param   mixed        $_return     Результат выполнения `include()`
     *
     * @return  string   Вернет перехваченный поток вывода
     */
    public static function obInclude(string $path, array $arguments = [], array $use = [], &$_return = null): string
    {
        ob_start();

        $_return = self::exeInclude($path, $arguments, $use);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Изолированно загрузит файл с помощью `include_once()`, перехватит поток вывода
     *
     * @param   string       $path        Путь к загружаемому скрипту
     * @param   array        $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array        $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     * @param   mixed        $_return     Результат выполнения `include_once()`
     *
     * @return  string   Вернет перехваченный поток вывода
     */
    public static function obIncludeOnce(string $path, array $arguments = [], array $use = [], &$_return = null): string
    {
        ob_start();

        $_return = self::exeIncludeOnce($path, $arguments, $use);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }

    /**
     * Изолированно загрузит файл с помощью `include_once()`, перехватит поток вывода
     *
     * @param   string       $phpCode     Выполняемый PHP код
     * @param   array        $arguments   Переменные для передачи в скрипт (ключи массива - имена переменных)
     * @param   array        $use         Переменные для передачи в скрипт по ссылке (ключи массива - имена переменных)
     * @param   mixed        $_return     Результат выполнения `eval()`
     *
     * @return  string   Вернет перехваченный поток вывода
     */
    public static function obEval(string $phpCode, array $arguments = [], array $use = [], &$_return = null): string
    {
        ob_start();

        $_return = self::exeEval($phpCode, $arguments, $use);

        $function_return = ob_get_contents();
        ob_end_clean();

        return $function_return;
    }
}

<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Console;

use DraculAid\PhpTools\Strings\Objects\StringIterator\StringIteratorInterface;
use DraculAid\PhpTools\Strings\Objects\StringIterator\StringIteratorObject;
use DraculAid\PhpTools\Strings\Objects\StringIterator\Utf8IteratorObject;

/**
 * Создает объект с параметрами консольных команд на основе строки
 *
 * Используйте для работы {@see ConsoleArgumentsFromString::exe()} вернет объект-аргумент {@see ConsoleArgumentsObject}
 *
 * Test cases for class {@see ConsoleArgumentsFromPhpArgvCreatorTest}
 *
 * @see ConsoleArgumentsFromPhpArgvCreator Получит объект-аргументов из `$_SERVER['argv']`
 */
final class ConsoleArgumentsFromString
{
    /** Итератор разбираемой строки аргументов */
    private StringIteratorInterface $stringIterator;

    /** Объект с аргументами консольных команд */
    private ConsoleArgumentsObject $argumentsObject;

    /** Если находимся внутри кавычек, то содержит символ кавычек (' или "), если находимся вне кавычек хранит пустую строку */
    private string $inQuote = '';

    /** Для накопления прочитанной строки, которая будет выступать именем аргумента */
    private string $tmpStringName = '';

    /** Для накопления прочитанной строки, которая будет выступать значением аргумента */
    private string $tmpStringValue = '';

    /** Сохраняет указание, что именованный аргумент будет иметь значение */
    private bool $tmpNameWithValue = false;

    /**
     * @param   string      $argumentString    Строка аргументов
     * @param   false|int   $charSize          FALSE если передана UTF8 строка или число указывающее на кол-во байт на символ
     *                                         (например, 1 для windows-1251)
     *
     * @return ConsoleArgumentsObject
     *
     * @todo PHP 8.2 Поменять `bool|int $charSize` на `false|int $charSize`
     */
    public static function exe(string $argumentString, bool|int $charSize = false): ConsoleArgumentsObject
    {
        $converter = new self();

        if ($charSize === false) $converter->stringIterator = new Utf8IteratorObject($argumentString);
        else $converter->stringIterator = new StringIteratorObject($argumentString, (int)$charSize);

        $converter->argumentsObject = new ConsoleArgumentsObject();

        // * * *

        foreach ($converter->stringIterator as $char)
        {
            $converter->runForChar($char);
        }

        $converter->saveArgument();

        // * * *

        return $converter->argumentsObject;
    }

    /** Это класс-функция: Для выполнения работы класса используйте {@see ConsoleArgumentsFromString::exe()} */
    private function __construct() {}

    /**
     * Разбирает очередной символ строки аргументов
     *
     * @param   string   $char   Разбираемый символ
     *
     * @return void
     */
    private function runForChar(string $char): void
    {
        // Если внутри кавычек
        if ($this->inQuote)
        {
            // закрытие кавычек и сохранение аргумента
            if ($char === $this->inQuote)
            {
                $this->saveArgument();
                return;
            }

            $this->tmpStringValue .= $char;
            return;
        }

        // вне кавычек - пробельный символ
        if (trim($char) === '')
        {
            // если было накоплено имя - сохраняем именованный аргумент
            // если было накоплено значение - сохраняем безымянный аргумент
            if ($this->tmpStringName !== '' || $this->tmpStringValue !== '')
            {
                $this->saveArgument();
                return;
            }

            // (!) Все остальные варианты означают, что пробельный символ можно игнорировать
            return;
        }

        // встретили кавычку
        if ($char === '"' || $char === "'" || $char === '`')
        {
            $this->inQuote = $char;
            return;
        }

        // если набираем имя аргумента
        if ($this->tmpStringName !== '' && !$this->tmpNameWithValue)
        {
            // встретили равно - начало значения аргумента
            if ($char === '=')
            {
                $this->tmpNameWithValue = true;
                return;
            }

            // (!) пока не встретили равно - продолжаем набирать имя аргумента
            $this->tmpStringName .= $char;
            return;
        }

        // Вне кавычек - если встретили "-" это начало набора имени команды
        if ($char === '-')
        {
            $this->tmpStringName .= $char;
            return;
        }

        // (!) Во всех остальных случаях набираем значение аргумента
        $this->tmpStringValue .= $char;
    }

    /**
     * Сохраняет найденный аргумент
     *
     * @return void
     */
    private function saveArgument(): void
    {
        if ($this->tmpStringValue === '' && $this->tmpStringName === '') return;

        // * * *

        /** @var int<0, max> $newArgumentNumber Номер нового аргумента */
        $newArgumentNumber = $this->argumentsObject->count();

        /** @var true|string $value Значение аргумента */
        $value = match ($this->tmpStringName === '') {
            true  => $this->tmpStringValue,
            false => $this->tmpNameWithValue ? $this->tmpStringValue : true,
        };

        $this->argumentsObject->setArgument($newArgumentNumber, $value);
        if ($this->tmpStringName) $this->argumentsObject->setName($newArgumentNumber, $this->tmpStringName);

        // * * * Очищаем временно накопленные данные

        $this->tmpStringName = '';
        $this->tmpStringValue = '';
        $this->inQuote = '';
        $this->tmpNameWithValue = false;
    }
}

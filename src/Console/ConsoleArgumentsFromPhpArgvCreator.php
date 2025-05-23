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

use DraculAid\PhpTools\Strings\Objects\StringIterator\Utf8IteratorObject;
use DraculAid\PhpTools\tests\Console\ConsoleArgumentsFromPhpArgvCreatorTest;

/**
 * Создает объект с параметрами консольных команд на основе `$_SERVER['argv']`
 *
 * Используйте для работы {@see ConsoleArgumentsFromPhpArgvCreator::exe()} вернет объект-аргумент {@see ConsoleArgumentsObject}
 *
 * (!) В результате разбора именованными аргументами будут только аргументы начинавшиеся с `-`
 *
 * Test cases for class {@see ConsoleArgumentsFromPhpArgvCreatorTest}
 *
 * @see ConsoleArgumentsFromString Для получения объекта-аргументов из строки
 */
final class ConsoleArgumentsFromPhpArgvCreator
{
    /**
     * Создает объект с параметрами консольных команд на основе `$_SERVER['argv']`
     *
     * @return ConsoleArgumentsObject
     * @throws \LogicException Если не была найдена `$_SERVER['argv']` или если он не является массивом (обычно это означает, что скрипт был запущен не из консоли)
     */
    public static function exe(): ConsoleArgumentsObject
    {
        if (empty($_SERVER['argv'])) throw new \LogicException("`\$_SERVER['argv']` not found");
        /** @psalm-suppress TypeDoesNotContainType да, мы реально хотим оставить `!is_array($_SERVER['argv'])` это защита "от дурака" */
        if (!is_array($_SERVER['argv'])) throw new \LogicException("`\$_SERVER['argv']` is not a array, it is a " . gettype($_SERVER['argv']));

        // * * *

        $consoleParam = new ConsoleArgumentsObject();
        $consoleParam->script = array_slice($_SERVER['argv'], 0, 1)[0];

        // * * *

        foreach (array_slice($_SERVER['argv'], 1) as $position => $paramRowValue)
        {
            self::parseParamRowValue($position, $paramRowValue, $consoleParam);
        }

        // * * *

        return $consoleParam;
    }

    /**
     * Парсит строку значения аргумента, создает аргумент, и сохраняет его в объекте списка аргументов
     *
     * @param   int<0, max>              $position        Порядковый номер аргумента
     * @param   string                   $paramRowValue   Строка с значением аргумента
     * @param   ConsoleArgumentsObject   $consoleParam    Объект "список аргументов"
     *
     * @return  void
     */
    private static function parseParamRowValue(int $position, string $paramRowValue, ConsoleArgumentsObject $consoleParam): void
    {
        /** Итератор, для перебора строки с значением аргумента */
        $utf8Iterator = new Utf8IteratorObject($paramRowValue);

        /** Для накопления разобранной строки */
        $tmpString = '';

        // * * * Анализ 1-го символа

        /** Очередной прочитанный символ */
        $char = $utf8Iterator->currentValueAndNext();

        // если первый символ не указатель флага - значит считаем, что это аргумент без имени
        if ($char !== '-')
        {
            $consoleParam->setArgument($position, $paramRowValue);

            return;
        }

        $tmpString .= $char;

        // * * * Анализ последующих символов

        // анализируем строку, если в ней найдем `=` значит мы нашли "именной" аргумент
        while ($char = $utf8Iterator->currentValueAndNext())
        {
            // Если встретили равенство, значит это был параметр вида `name=value` - есть имя и значение
            if ($char === '=')
            {
                $value = substr($paramRowValue, $utf8Iterator->key(true)) ?: '';
                $consoleParam->setArgument($position, $value);
                $consoleParam->setName($position, $tmpString);

                return;
            }

            $tmpString .= $char;
        }

        // если дошли сюда - то именованный аргумент без значения
        $consoleParam->setArgument($position, true)->setName($position, $tmpString);
    }
}

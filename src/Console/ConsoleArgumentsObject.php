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

use DraculAid\PhpTools\Arrays\Objects\Interfaces\ArrayInterface;
use DraculAid\PhpTools\Arrays\Objects\ListObject;

/**
 * Объект для работы с аргументами консольных команд.
 *
 * Поддерживает получение аргументов, как по позиции, так и по "имени":
 * <br>- Аргумент `blablabla` имеет номер и значение (`blablabla`)
 * <br>- Аргумент `age=18` имеет имя (`age`) и значение (`18`)
 * <br>- Флаги `-h` или `--help` имеет имя (`-h` или `--help`), а в качестве значения имеет TRUE или строку (если было `-h=abc`)
 *
 * См также {@see ConsoleArgumentsFromPhpArgvCreator} Вернет параметры текущего скрипта (т.е. из `$_SERVER['argv']`)
 * и {@see ConsoleArgumentsFromString} для получения объекта аргументов из строки
 *
 * Оглавление:
 * <br>- {@see self::$script} Имя запущенного скрипта
 * <br>- {@see self::count()} Вернет кол-во аргументов
 * <br>- {@see self::countNames()} Вернет кол-во аргументов с именем
 * <br>- {@see self::getIterator()} Переберет или все аргументы, или только аргументы по имени
 * <br>- {@see self::commandNameCount()} Вернет кол-во аргументов до первого именованного аргумента
 * <br>- {@see self::commandNameIterator()} Итератор, перебирающие аргументы до первого именованного аргумента
 * <br>--- Операции записи
 * <br>- {@see self::setArgument()} Установит значение аргумента по номеру позиции
 * <br>- {@see self::setName()} Установит значение аргумента по имени
 * <br>- {@see self::offsetUnset()} Удалит аргумент по имени или позиции
 * <br>- {@see self::offsetSet()} Установит значение аргументу по имени или позиции
 * <br>--- Операции чтения
 * <br>- {@see self::getNameByPosition()} Имя для аргумента конкретного позиции, может выбросить исключение
 * <br>- {@see self::getPositionByName()} Вернет позицию для имени, может выбросить исключение
 * <br>- {@see self::getByPosition()} Значение для аргумента конкретного позиции, может выбросить исключение
 * <br>- {@see self::getByName()} Значение для аргумента по имени, может выбросить исключение
 * <br>- {@see self::offsetGet()} Вернет значение аргумента по имени или позиции
 * <br>- {@see self::offsetExists()} Проверит, существует ли аргумент по имени или позиции
 * <br>--- Прочее
 * <br>- {@see self::__toString()}
 *
 * Test cases for class {@see ConsoleArgumentsObjectTest}
 *
 * @todo Реализовать IteratorInterface
 */
class ConsoleArgumentsObject implements ArrayInterface, \IteratorAggregate, \Stringable
{
    /** Скрипт, из под которого была запущена команда */
    public string $script = '';

    /** Список аргументов */
    protected ListObject $arguments;

    /** @var array<string, int<0, max>> Соответствия "имен" и позиций в {@see self::$arguments} */
    protected array $nameAndPosition = [];

    /** @var array<int<0, max>, string> Соответствия позиций в {@see self::$arguments} и установленных "имен" */
    protected array $positionAndName = [];

    /**
     * Создание Объекта для работы с параметрами консольных команд
     */
    public function __construct()
    {
        $this->arguments = new class() extends ListObject {
            /** @inheritdoc */
            protected bool $warningOn = false;
        };
    }

    /**
     * Добавляет / изменяем аргумент
     *
     * @param   int<0, max>   $position   Позиция аргумента
     * @param   true|string   $value      Значение аргумента (TRUE аргумент передан, но без значения)
     *
     * @return  $this
     * @throws  \RangeException   Если указана "позиция" нарушающая принцип массива-списка
     *
     * @todo PHP8 убрать возможность передачи в $value NULL - такая возможность была для создания аргумента без значения
     *       (исключительно для проверки работы "объекта как массива" в юнит-тестах)
     */
    public function setArgument(int $position, null|bool|string $value): static
    {
        if ($position < 0 || $position > $this->arguments->count()) throw new \RangeException("\$position must be into the list, but \$position = {$position}");

        $this->arguments->offsetSet($position, $value);

        return $this;
    }

    /**
     * Установит аргументу имя, если имя уже занято, оно изменит свою привязку
     *
     * @param   int<0, max>   $position   Позиция аргумента
     * @param   string        $name       Имя аргумента, или пустая строка (''), если у аргумента нужно удалить имя
     *
     * @return  $this
     * @throws  \RangeException   Если нет элемента с такой позицией
     */
    public function setName(int $position, string $name): static
    {
        if ($position < 0 || !$this->arguments->keyExists($position)) throw new \RangeException("Element with number {$position} not found");

        // если у элемента уже есть имя
        if (isset($this->positionAndName[$position]))
        {
            // если имя не меняет своей привязки
            if ($this->positionAndName[$position] === $name) return $this;

            unset($this->nameAndPosition[$this->positionAndName[$position]]);
            unset($this->positionAndName[$position]);
        }

        // если это удаление имени
        if ($name === '')
        {
            return $this;
        }

        // если имя уже используется
        if (isset($this->nameAndPosition[$name]))
        {
            unset($this->positionAndName[$this->nameAndPosition[$name]]);
        }

        $this->nameAndPosition[$name] = $position;
        $this->positionAndName[$position] = $name;

        return $this;
    }

    /**
     * Вернет имя по указанной позиции, если не найдет - выбросит исключение (отключаемая опция)
     *
     * @param   int    $position      Позиция
     * @param   bool   $orException   Нужно ли выбрасывать исключение, если не было найдено имя
     *
     * @return  null|string
     * @throws  \RangeException   Может быть выброшен, если не было найдено имя (см аргумент $orException)
     */
    public function getNameByPosition(int $position, bool $orException = true): null|string
    {
        if ($orException && !isset($this->positionAndName[$position])) throw new \RangeException("Name for position #{$position} not found");

        return $this->positionAndName[$position] ?? null;
    }

    /**
     * Вернет позицию для указанного имени. Если имя не существует - выбросит исключение (отключаемая опция)
     *
     * @param   string   $name          Имя
     * @param   bool     $orException   Нужно ли выбрасывать исключение, если не было найдено имя
     *
     * @return  null|int<0, max>
     * @throws  \RangeException   Может быть выброшен, если имя не существует (см аргумент $orException)
     */
    public function getPositionByName(string $name, bool $orException = true): null|int
    {
        if ($orException && !isset($this->nameAndPosition[$name])) throw new \RangeException("Name >{$name}< not found");

        return $this->nameAndPosition[$name] ?? null;
    }

    /**
     * Вернет значение по указанной позиции, если позиция не существует - выбросит исключение (отключаемая опция)
     *
     * @param   int<0, max>   $position      Позиция
     * @param   bool          $orException   Нужно ли выбрасывать исключение, если не было найдено имя
     *
     * @return  null|true|string  TRUE аргумент был без значения (например ключ `-h`), NULL - аргумент не существует
     * @throws  \RangeException   Может быть выброшен, если имя не существует (см аргумент $orException)
     *
     * @todo PHP8.2 типизация ответа функции
     */
    public function getByPosition(int $position, bool $orException = true): null|bool|string
    {
        if ($position < 0 || $position >= $this->arguments->count())
        {
            if ($orException) throw new \RangeException("Position #{$position} not found");
            else return null;
        }

        return $this->arguments->offsetGet($position);
    }

    /**
     * Вернет значение по указанному имени, если имя не существует - выбросит исключение (отключаемая опция)
     *
     * @param   string   $name          Имя
     * @param   bool     $orException   Нужно ли выбрасывать исключение, если не было найдено имя
     *
     * @return  null|true|string  TRUE аргумент был без значения (например ключ `-h`), NULL - аргумент не существует
     * @throws  \RangeException   Может быть выброшен, если имя не существует (см аргумент $orException)
     *
     * @todo PHP8.2 типизация ответа функции
     */
    public function getByName(string $name, bool $orException = true): null|bool|string
    {
        /** @psalm-suppress InvalidArgument Специально тут можем передать невалидное значение, что бы было падение (что бы не будлировать проверки) */
        return $this->getByPosition(
            $this->getPositionByName($name, $orException) ?? -1, // если не нашли позицию для имени, передадим "-1", так как это невалидный номер позиции
            $orException
        );
    }

    /**
     * Вернет кол-во аргументов
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return $this->arguments->count();
    }

    /**
     * Вернет кол-во аргументов c именами
     *
     * @return int<0, max>
     */
    public function countNames(): int
    {
        return count($this->nameAndPosition);
    }

    /**
     * Итератор, для обхода аргументов
     *
     * В качестве "индекса" будет или позиция или имя аргумента (см $withName), в качестве значения может быть string или
     * TRUE (аргумент был без значения, такое может быть у "флагов").
     *
     * @param   bool   $withName   Только элементы с именами (индексы будут именами, в противном случае номера аргументов)
     *
     * @return \Generator<int|string, true|string>
     */
    public function getIterator(bool $withName = false): \Generator
    {
        if ($withName)
        {
            foreach ($this->nameAndPosition as $name => $position)
            {
                yield $name => $this->arguments->offsetGet($position);
            }

            return;
        }

        // * * *

        foreach ($this->arguments as $position => $value)
        {
            yield $position => $value;
        }
    }

    /**
     * Вернет кол-во аргументов до первого именованного аргумента, обычно это имя/путь к функции/классу,
     * внутри какого-то фреймворка
     *
     *  Например: `command.php alfa beta -f=123` это будут `['alfa', 'beta']`, значит функция вернет 0
     *
     * @return int<0, max>
     */
    public function commandNameCount(): int
    {
        if ($this->arguments->count() === 0 || $this->arguments->count() === count($this->nameAndPosition)) return 0;

        if (count($this->nameAndPosition) === 0) return $this->arguments->count();
        else return min($this->nameAndPosition);
    }

    /**
     * Итератор, перебирающие аргументы до первого именованного аргумента, обычно это имя/путь к функции/классу,
     * внутри какого-то фреймворка
     *
     * Например: `command.php alfa beta -f=123` переберет `['alfa', 'beta']`
     *
     * @return \Generator<int, string>
     */
    public function commandNameIterator(): \Generator
    {
        $stopPosition = $this->commandNameCount();

        foreach ($this->arguments as $position => $value)
        {
            if ($position >= $stopPosition) return;

            yield $position => $value;
        }
    }

    /**
     * Проверит, существует ли аргумент, по его имени или позиции
     *
     * @param   int<0, max>|string   $offset   Позиция или имя аргумента
     *
     * @return  bool
     */
    public function offsetExists(mixed $offset): bool
    {
        if (is_string($offset)) return isset($this->nameAndPosition[$offset]);

        return $this->arguments->offsetExists($offset);
    }

    /**
     * Вернет значение аргумента, по его номеру или имен
     *
     * @param   int<0, max>|string   $offset   Позиция или имя аргумента
     *
     * @return  null|true|string   TRUE аргумент был без значения (например ключ `-h`), NULL - аргумент не существует
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        if (is_string($offset))
        {
            if (!isset($this->nameAndPosition[$offset])) return null;

            $offset = $this->nameAndPosition[$offset];
        }

        // * * *

        return $this->arguments->offsetGet($offset);
    }

    /**
     * Добавление нового аргумента
     *
     * @param   null|int<0, max>|string   $offset   Позиция или имя аргумента
     *                                              <br>- NULL: будет добавлено без имени, в конец списка
     *                                              <br>- int: позиция элемента (некорректное значение - будет добавлено в конец списка)
     *                                              <br>- string: имя элемента (если такого элемента нет, будет добавлен в конец списка)
     * @param   true|string               $value    Значение аргумента (TRUE аргумент передан, но без значения)
     *
     * @return $this
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): static
    {
        $name = null;

        // * * *

        if ($offset === null)
        {
            $position = $this->arguments->count();
        }
        elseif (is_string($offset))
        {
            $name = $offset;
            $position = $this->getPositionByName($offset, false);
            if ($position === null) $position = $this->arguments->count();
        }
        elseif ($offset < 0 || $offset > $this->arguments->count())
        {
            $position = $this->arguments->count();
        }
        else
        {
            $position = $offset;
        }

        // * * *

        $this->setArgument($position, $value);
        if ($name !== null) $this->setName($position, $name);

        return $this;
    }

    /**
     * Удалить аргумент
     *
     * @param   int|string   $offset   Позиция или имя аргумента
     *
     * @return  $this
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): static
    {
        return $this;
    }

    /** {@inheritdoc} */
    public function __toString(): string
    {
        $_return = $this->script === '' ? [] : [$this->script];

        foreach ($this->arguments as $position => $value)
        {
            // если именованный аргумент
            if (array_key_exists($position, $this->positionAndName))
            {
                $_return[] = "{$this->positionAndName[$position]}" . (is_string($value) ? "={$value}" : '');
                continue;
            }

            $_return[] = $value;
        }

        return implode(' ', $_return);
    }
}

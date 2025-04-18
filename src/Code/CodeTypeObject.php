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

use DraculAid\PhpTools\tests\Code\CodeTypeObjectTest;

/**
 * Класс, для хранения типов данных (совместим с DocBlock/PhpBlock)
 *
 * (!) Является интератором, перебирает все типы (см {@see self::$types})
 * (!) При преобразовании в строку, вернет строку с типами пригодными к использованию в PHP коде
 *
 * Оглавление
 * <br>--- Создание объекта
 * <br>{@see CodeTypeObject::createFromPhp()} Создает с переданными PHP типами
 * <br>{@see CodeTypeObject::createFromSql()} Создает с переданным SQL типом
 * <br>{@see CodeTypeObject::createFromDocBlock()} Установит тип(ы) данных принятых в DocBlock / PhpDoc
 * <br>--- Установка типов
 * <br>{@see self::set()} Установит тип данных
 * <br>{@see self::setFromSql()} Установит тип по SQL типу
 * <br>{@see self::setFromDocBlock()} Установит тип(ы) данных принятых в DocBlock / PhpDoc
 * <br>--- Проверка типов
 * <br>{@see self::isWithType()} В типе данных, есть указанный тип или нет
 * <br>{@see self::isWithNull()} В типе данных, есть NULL или нет
 * <br>{@see self::isWithBool()} В типе данных, есть булевы варианты (bool, true, false)
 * <br>{@see self::isWithNumber()} В типе данных, есть числа
 * <br>--- Проверка типов
 * <br>{@see self::getIterator()} Позволит перебрать все типы
 * <br>{@see self::getType()} Вернет все типы ввиде массива
 *
 * @method static CodeTypeObject createFromPhp(string|string[] $type) Создает с переданными PHP типами
 * @method static CodeTypeObject createFromSql(string $type, bool $isNull) Создает с переданным SQL типом
 * @method static CodeTypeObject createFromDocBlock(string|string[] $type) Создает с переданными DocBlock типами
 *
 * Test cases for class {@see CodeTypeObjectTest}
 */
class CodeTypeObject implements \IteratorAggregate, \Stringable
{
    /**
     * Хранит список типов
     * (Индекс - тип, значение всегда TRUE)
     *
     * @var array<string, true>
     */
    protected array $types = [];

    /**
     * @param   string   $name
     * @param   array    $arguments
     *
     * @return  mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        // получение имени функции, которая будет использована для установки списка типов
        $name = match ($name) {
            'createFromPhp'      => 'set', /** {@see self::set()} */
            'createFromSql'      => 'setFromSql', /** {@see self::setFromSql()} */
            'createFromDocBlock' => 'setFromDocBlock', /** {@see self::setFromDocBlock()} */
            default              => throw new \TypeError("Magic method for {$name} not found"),
        };

        return (new static())->{$name}(... $arguments);
    }

    public function __toString(): string
    {
        return implode('|', array_keys($this->types));
    }

    /**
     * Используется для перебора всех типов данных
     *
     * @return \Generator<string>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->types as $type => $true)
        {
            yield $type;
        }
    }

    /**
     * Вернет все типы ввиде массива
     *
     * @see self::getIterator() Используется для перебора всех типов данных
     *
     * @return string[]
     */
    public function getType(): array
    {
        return array_keys($this->types);
    }

    /**
     * Установит тип(ы) данных
     *
     * @param   string|string[]   $type    Строка с типом данных (разделитель |) или массив с типами данных
     *
     * @return  $this
     */
    public function set(string|array $type): self
    {
        if (is_string($type))
        {
            $type = explode('|', $type);
        }

        // * * *

        $this->types = array_fill_keys($type, true);

        return $this;
    }

    /**
     * Установит тип по SQL типу
     *
     * @param   string   $type      SQL тип данных
     * @param   bool     $isNull    TRUE если может быть NULL
     *
     * @return  $this
     *
     * @todo у функции нет юнит-теста
     */
    public function setFromSql(string $type, bool $isNull): self
    {
        $type = strtolower($type);

        $type = match ($type)
        {
            'tinyint',
            'smallint',
            'int',
            'bigint',
            'mediumint' => ['int'],
            'float',
            'double'    => ['float'],
            default     => ['string'],
        };

        if ($isNull) $type[] = 'null';

        return $this->set($type);
    }

    /**
     * Установит тип(ы) данных принятых в DocBlock / PhpDoc
     *
     * @param   string|string[]   $type    Строка с типом данных (разделитель |) или массив с типами данных
     *
     * @return  $this
     */
    public function setFromDocBlock(string|array $type): self
    {
        if (is_string($type))
        {
            $type = explode('|', $type);
        }

        // * * *

        // приведение типов к PHP стандарту
        foreach ($type as &$value)
        {
            $value = match ($value) {
                'integer' => 'int',
                'double'  => 'float',
                'boolean' => 'bool',
                'str'     => 'string',
                default   => $value,
            };
        }

        // * * *

        return $this->set($type);
    }

    /**
     * В типе данных, есть указанный тип или нет
     *
     * @return bool
     */
    public function isWithType(string $type): bool
    {
        return isset($this->types[$type]);
    }

    /**
     * В типе данных, есть NULL или нет
     *
     * @return bool
     */
    public function isWithNull(): bool
    {
        return isset($this->types['null']);
    }

    /**
     * В типе данных, есть булевы варианты (bool, true, false)
     *
     * @return bool
     */
    public function isWithBool(): bool
    {
        return isset($this->types['bool']) || isset($this->types['false']) || isset($this->types['true']);
    }

    /**
     * В типе данных, есть числа
     *
     * @return bool
     */
    public function isWithNumber(): bool
    {
        return isset($this->types['int']) || isset($this->types['float']);
    }
}

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

use DraculAid\PhpTools\tests\Code\TypeValidatorTest;

/**
 * Класс для валидации составных типов данных
 *
 * (!) Этот функционал не может корректно отработать с типами 'never', 'void', 'self', 'static', 'parent'
 *
 * Оглавление:
 * <br>{@see TypeValidator::validate()} Соответствует ли значение PHP типу данных (включая составные с объединением и пересечением)
 * <br>{@see TypeValidator::validateOrError()} Соответствует ли значение PHP типу данных, если не соответствует - выбрасывает исключение
 * <br>{@see TypeValidator::validateOr()} Валидирует по "Одному из" типов данных (т.е. A|B|C)
 * <br>{@see TypeValidator::validateAnd()} Валидирует объекты по точному совпадению со всеми типами данных (т.е. A&B&C)
 *
 * Ранее функционал валидатора был частью https://github.com/dracul-aid/Php8forPhp7
 *
 * @link https://www.php.net/manual/ru/language.types.declarations.php#language.types.declarations.composite Документация PHP о составных типах данных
 *
 *  Test cases for class {@see TypeValidatorTest}
 */
final class TypeValidator
{
    /** Базовые типы данных PHP */
    public const BASIC_TYPE = ['null', 'bool', 'int', 'float', 'string', 'array', 'resource', 'object'];

    /** Псевдотипы PHP */
    public const SIMULATOR_TYPE = ['false', 'true', 'callable', 'iterable', 'mixed'];

    /**
     * Проверит, соответствует ли значение переданному типу данных (включая Объединения (Union) и Пересечения (Intersection)).
     *
     * (!) Функция не поддерживает Объединения, совмещенные с пересечением, например `A|(B&C)`, в случае, если в $type будет
     * указан такой тип, будет выброшено исключение {@see \LogicException}
     *
     * @param   mixed    $value   Значение для проверки
     * @param   string   $type    Тип данных (как в PHP), может быть составным с объединением (A|B|C) или пересечением (A&B&C)
     *
     * @return  bool
     * @throws  \LogicException   Если в качестве типа передан составной тип с объединением и пересечением
     */
    public static function validate(mixed $value, string $type): bool
    {
        // если есть скобки - значит составной тип, с таким не работаем(((
        if (str_contains($type, '(')) throw new \LogicException("Not supported composite type");

        if (str_contains($type, '|'))
        {
            $typeList = explode('|', $type);
            return self::validateOr($value, $typeList, false);
        }

        if (str_contains($type, '&'))
        {
            $typeList = explode('&', $type);
            return self::validateAnd($value, $typeList, false);
        }

        return get_debug_type($value) === $type;
    }

    /**
     * Проверит, соответствует ли значение переданному типу данных (включая Объединения (Union) и Пересечения (Intersection)).
     * Если не соответствует - выбросит исключение
     *
     * (!) Функция не поддерживает Объединения, совмещенные с пересечением, например `A|(B&C)`, в случае если в $type будет
     * указан такой тип, будет выброшено исключение {@see \LogicException}
     *
     * @param   mixed                      $value        Значение для проверки
     * @param   string                     $type         Тип данных (как в PHP), может быть составным с объединением (A|B|C) или пересечением (A&B&C)
     * @param   class-string<\Throwable>   $throwClass   Класс исключения, которое будет выброшено в случае провала проверки (по умолчанию - {@see \TypeError}).
     *                                                   Если передан некорректный класс - будет использован {@see \TypeError}
     *
     * @return  true
     * @throws  \TypeError        В случае если значение не удовлетворяет переданному типу данных
     * @throws  \LogicException   Если в качестве типа передан составной тип с объединением и пересечением
     */
    public static function validateOrError(mixed $value, string $type, string $throwClass = ''): bool
    {
        $test = self::validate($value, $type);

        if ($test) return true;

        if ($throwClass === '' || $throwClass instanceof \Throwable === false) $throwClass = \TypeError::class;

        throw new $throwClass("Value is not correct type (value is a " . get_debug_type($value) . ", expected type: {$type})");
    }

    /**
     * Проверяет, что переданное значение удовлетворяет одному из переданных типов данных. В случае провала может
     * выбросить {@see \TypeError}. Т.е. функция используется для проверки вида <code>A|B|C</code>
     *
     * @param   mixed      $value      Значение для проверки
     * @param   string[]   $typeList   Типы (как в PHP), значение должно соответствовать одному из указанных типов
     * @param   bool       $throw      TRUE, если при провале проверки - нужно выбросить исключение, или FALSE если нет
     *
     * @return  bool
     *
     * @throws  \TypeError   В случае, если значение не удовлетворяет переданному типу данных
     */
    public static function validateOr(mixed $value, array $typeList, bool $throw = true): bool
    {
        // для объекта вернет имя класса (без стартового слеша)
        $varType = get_debug_type($value);

        // если базовый тип, есть в списке возможных типов
        if (in_array($varType, $typeList))
        {
            return true;
        }

        // Для объектов также проверим имена классов
        if (is_object($value) && (in_array('object', $typeList) || in_array("\\{$varType}", $typeList)))
        {
            return true;
        }

        // логические псевдотипы
        if (is_bool($value) && ((!$value && in_array('false', $typeList)) || ($value && in_array('true', $typeList))))
        {
            return true;
        }

        // проверка callable
        if (is_callable($value) && in_array('callable', $typeList))
        {
            return true;
        }

        // проверка iterable
        if (is_iterable($value) && in_array('iterable', $typeList))
        {
            return true;
        }

        // для объектов поиск родительских классов
        if (is_object($value))
        {
            foreach ($typeList as $type)
            {
                if (
                    in_array($type, self::BASIC_TYPE)
                    || in_array($type, self::SIMULATOR_TYPE)
                ) continue;

                if (is_subclass_of($value, $type)) return true;
            }
        }

        // * * *

        if ($throw) throw new \TypeError("Value is not correct type");

        return false;
    }

    /**
     * Проверяет, что переданное значение удовлетворяет всем типам данных. В случае провала может
     * выбросить {@see \TypeError}. Т.е. функция используется для проверки вида <code>A&B&C</code>
     *
     * (!) Проверка имеет смысл только для объектов, поэтому все НЕ ОБЪЕКТЫ провалят проверку
     *
     * @param   mixed      $value      Значение для проверки
     * @param   string[]   $typeList   Типы (как в PHP), значение должно соответствовать всем перечисленным типам
     * @param   bool       $throw      TRUE, если при провале проверки - нужно выбросить исключение, или FALSE если нет
     *
     * @return  bool
     *
     * @throws  \TypeError    В случае, если значение не удовлетворяет переданному типу данных
     */
    public static function validateAnd(mixed $value, array $typeList, bool $throw = true): bool
    {
        if (!is_object($value))
        {
            if ($throw) throw new \TypeError("Value can be the object");
            return false;
        }

        // * * *

        foreach ($typeList as $type)
        {
            if ($type === 'callable' && !is_callable($value))
            {
                if ($throw) throw new \TypeError("Value is not correct type");
                return false;
            }

            if ($type === 'iterable' && !is_iterable($value))
            {
                if ($throw) throw new \TypeError("Value is not correct type");
                return false;
            }

            if (!is_a($value, $type))
            {
                if ($throw) throw new \TypeError("Value is not correct type");
                return false;
            }
        }

        return true;
    }
}

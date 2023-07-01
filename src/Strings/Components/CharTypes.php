<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings\Components;

/**
 * Типы символов, используется при определении типа символа, см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
 *
 * <br>{@see CharTypes::IS_NOT_CHAR} - Не символ (обычно подразумевает строку)
 * <br>{@see CharTypes::IS_OTHER} - Тип символа - Неизвестный тип символа
 * <br>{@see CharTypes::IS_ABC_UPPER} - Тип символа - Заглавная латинская буква (Большая буква)
 * <br>{@see CharTypes::IS_ABC_LOWER} - Тип символа - Прописная латинская буква (Маленькая буква)
 * <br>{@see CharTypes::IS_NUMBER} - Тип символа - Десятичное число
 */
final class CharTypes
{
    /**
     * Тип символа - Прописная латинская буква (Маленькая буква), см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
     */
    public const IS_NOT_CHAR = false;

    /**
     * Тип символа - неизвестный тип символа, см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
     */
    public const IS_OTHER = 0;

    /**
     * Тип символа - Прописная латинская буква (Маленькая буква), см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
     */
    public const IS_ABC_LOWER = 1;

    /**
     * Тип символа - Заглавная латинская буква (Большая буква), см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
     */
    public const IS_ABC_UPPER = 2;

    /**
     * Тип символа - Десятичное число, см {@see \DraculAid\PhpTools\Strings\CharTools::getType()}
     */
    public const IS_NUMBER = 3;
}

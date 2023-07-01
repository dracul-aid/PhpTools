<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Singleton;

/**
 * Интерфейс, для создания Синглтонов (Классов-одиночек)
 *
 * Оглавление:
 * <br>{@see SingletonInterface::getInstance()} Вернет экземпляр класса
 */
interface SingletonInterface
{
    /**
     * Вернет экземпляр синглтон-класса
     *
     * При каждом вызове будет возвращать один и тот же экземпляр
     *
     * @return static
     */
    public static function getInstance(): self;
}

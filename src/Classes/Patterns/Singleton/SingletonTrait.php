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
 * Трейт с функционалом для создания Синглтонов (Классов-одиночек)
 *
 * Оглавление:
 * <br>{@see SingletonTrait::getInstance()} Вернет экземпляр класса
 */
trait SingletonTrait
{
    /**
     * Массив для хранения созданных объектов классов-синглтонов
     *
     * Представляет собой массив
     * <br>+ Ключи - полные имена классов
     * <br>+ Значения - Созданные объекты
     *
     * @var array<string, self> $___dracul_aid_singletone_objects___
     */
    private static array $___dracul_aid_singletone_objects___ = [];

    /**
     * Вернет экземпляр синглтон-класса
     *
     * При каждом вызове будет возвращать один и тот же экземпляр
     *
     * @return self
     *
     * @todo PHP8 Возвращаемое значение должно быть self
     */
    final public static function getInstance(): object
    {
        if (!array_key_exists(static::class, static::$___dracul_aid_singletone_objects___))
        {
            static::$___dracul_aid_singletone_objects___[static::class] = new static();
        }

        return static::$___dracul_aid_singletone_objects___[static::class];
    }
}

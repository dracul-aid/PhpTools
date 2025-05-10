<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\StaticDi;

/**
 * События "Di для статических классов"
 *
 * <br>{@see StaticDiEvents::$eventDefaultCreate} Произойдет создание контейнера "по умолчанию"
 * <br>{@see self::$eventSearchBefore} Перед началом поиска класса
 * <br>{@see self::$eventSearchAfter} После поиска класса
 * <br>{@see self::$notFoundExceptionClassName} Имя класса исключения, которое может быть выброшено - если не удалось найти класс для ответа
 */
final class StaticDiEvents
{
    /**
     * Событие: Произойдет создание контейнера "по умолчанию"
     *
     * В качестве аргумента получит созданный объект-контейнер, используется в {@see StaticDi::getDefaultInstance()}
     *
     * @var array<array-key, callable(StaticDi):void>
     */
    public static array $eventDefaultCreate = [];

    /**
     * Событие: Перед началом поиска класса
     *
     * В качестве аргументов принимает:
     * <br>- Объект-контейнер, в котором была вызвана
     * <br>- ключ переданный для поиска класса
     * <br>- класс "по умолчанию" (будет использован в качестве ответа, если ничего не удалось найти)
     *
     * В качестве ответа:
     * <br>- Не пуста строка - Вернет строку с именем класса, который и должен быть результатом работы функции
     * <br>- Все остальные варианты - поиск имени класса будет производиться так, словно события не было
     *
     * Используется в {@see StaticDi::keyGetClass()}
     *
     * @var array<array-key, callable(StaticDi, string, class-string):class-string>
     */
    public array $eventSearchBefore = [];

    /**
     * Событие: После поиска класса
     *
     * В качестве аргументов принимает:
     * <br>- Объект-контейнер, в котором была вызвана
     * <br>- ключ переданный для поиска класса
     * <br>- класс "по умолчанию" (будет использован в качестве ответа, если ничего не удалось найти)
     * <br>- класс, который был найден
     *
     * В качестве ответа:
     * <br>- Не пуста строка - Вернет строку с именем класса, который и должен быть результатом работы функции
     * <br>- Все остальные варианты - поиск имени класса будет производиться так, словно события не было
     *
     * Используется в {@see StaticDi::keyGetClass()}
     *
     * @var array<array-key, callable(StaticDi, string, class-string, class-string):class-string>
     */
    public array $eventSearchAfter = [];
}

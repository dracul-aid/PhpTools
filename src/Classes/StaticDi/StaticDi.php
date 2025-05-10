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

use DraculAid\PhpTools\tests\Classes\StaticDiTest;

/**
 * Инструмент, для "реализации" инверсии зависимостей для статических классов
 *
 * Т.е. это функционал для имитация Di, для случаев, когда значительная часть проекта описана классами с статическими
 * методами и времени на рефакторинг нет, подробнее можно посмотреть [в документации](../../Documentation-ru/StaticDi.md)
 *
 * Инструмент позволяет использовать в качестве "ключа" имя класса и возвращает "имя класса", в любой момент, инструмент
 * можно сконфигурировать так, что бы возвращалось иное имя класса
 * ```php
 * // Версия класса для "прода"
 * class A {
 *     public static function isLive(): bool {
 *         return getenv('HOST') === 'LIVE_HOST';
 *     }
 * }
 * // Версия класса для "тестов"
 * class B extends A {
 *     public static function isLive(): bool {
 *         return true;
 *     }
 * }
 *
 * // в обычных условиях получаем класс A
 * StaticDi::get(A::class)::isLive();
 *
 * // Для тестов поменяем возвращаемый класс
 * StaticDi::$rules[A::class] = B::class;
 * // И теперь будем получать класс B
 * StaticDi::get(A::class)::isLive();
 * ```
 *
 * Оглавление:
 * <br>{@see StaticDi::getDefaultInstance()} Вернет Singleton объект "Контейнера"
 * <br>{@see StaticDi::getClass()} Вернет имя класса, для указанного класса-ключа
 * <br>{@see StaticDi::keyGetClass()} Вернет имя класса, для указанного строкового ключа
 * <br>---
 * <br>{@see self::$rules} Список правил
 *
 * @link https://github.com/dracul-aid/PhpTools/blob/master/Documentation-ru/StaticDi.md Докуметация (как это работает)
 *
 * Test cases for class {@see StaticDiTest}
 */
final class StaticDi
{
    /**
     * Хранит "текущий по умолчанию"
     * (В большинстве проектов предполагается, что контейнер может быть только один)
     */
    private static self $instance;

    /**
     * Содержит соответствия, для каких классов "ключей" нужно вернуть какой класс
     *
     * - В качестве ключа выступает любая строка (обычно это имя класса)
     * - В качестве значения - имя класса, или функция (получит ключ и вернет имя класса)
     *
     * @var array<string, class-string|callable(string):class-string>
     */
    public array $rules = [];

    /** Менеджер событий контейнера */
    readonly public StaticDiEvents $events;

    /**
     * Вернет Singleton объект "Контейнера" по умолчанию
     *
     * В большинстве проектов предполагается, что контейнер может быть только один
     *
     * @return self
     */
    public static function getDefaultInstance(): self
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self();

            if (count(StaticDiEvents::$eventDefaultCreate) > 0) foreach (StaticDiEvents::$eventDefaultCreate as $eventFunction)
            {
                $eventFunction(self::$instance);
            }
        }

        return self::$instance;
    }

    /**
     * Вернет имя класса, для указанного класса-ключа из "Контейнера по умолчанию"
     *
     * Является синтаксическим сахаром для:
     * <code>
     * StaticDi::getDefaultInstance()->getClass($className);
     * </code>
     *
     * @param   class-string   $className
     *
     * @return  class-string
     *
     * @psalm-template RealInstanceType of class-string
     * @psalm-param RealInstanceType $className
     * @psalm-return RealInstanceType
     *
     * @psalm-suppress InvalidReturnType Псалм не понимает заложенную хитрость, что мы хотим вернуть имя класса, переданного в $className
     */
    public static function get(string $className): string
    {
        return self::getDefaultInstance()->getClass($className);
    }

    /**
     * Создание контейнера.
     *
     * В большинстве случаев для получения контейнера следует использовать {@see StaticDi::getDefaultInstance()}
     */
    public function __construct()
    {
        $this->events = new StaticDiEvents();
    }

    /**
     * Вернет имя класса, для указанного класса-ключа
     *
     * @param   class-string   $className
     *
     * @return  class-string
     *
     * @psalm-template RealInstanceType of class-string
     * @psalm-param RealInstanceType $className
     * @psalm-return RealInstanceType
     *
     * @psalm-suppress InvalidReturnType Псалм не понимает заложенную хитрость, что мы хотим вернуть имя класса, переданного в $className
     */
    public function getClass(string $className): string
    {
        return $this->keyGetClass($className, $className);
    }

    /**
     * Вернет имя класса для указанного ключа
     *
     * Эта функция синтаксический сахар для {@see StaticDi::getClass()} для случаев, если вам нужно передать в качестве ключа
     * не "имя класса", а любую строку. Используя эту функцию, вы поможете стат-анализаторам (вроде PhpStorm или Psalm)
     * понять, метод какого класса вы будете вызывать ниже
     *
     * Кроме того, переданный $className будет использован в качестве имени класса по умолчанию, если правило для ключа
     * не было установлено
     *
     * ```
     * $key = 'my-string';
     *
     * // Psalm вернет ошибку, так как 'my-string' не является именем класса, а PhpStorm не поймет, что за метод `::run()`
     * StaticDi::get($key)::run();
     *
     * // Psalm и PhpStorm поймут, что вызывается RunnerClass::run()
     * StaticDi::keyGetClass($key, RunnerClass::class)::run();
     * ```
     *
     * @param   string   $key          Ключ для поиска правил
     * @param   string   $className    Имя "класса по умолчанию", это имя класса будет возвращено, если для ключа нет установленных правид
     *
     * @return string
     *
     * @psalm-template RealInstanceType of class-string
     * @psalm-param RealInstanceType $className
     * @psalm-return RealInstanceType
     *
     * @psalm-suppress InvalidReturnType Псалм не понимает заложенную хитрость, что мы хотим вернуть имя класса, переданного в $className
     * @psalm-suppress InvalidReturnStatement Псалм видит, что функции-событий могут вернуть любую строку, и это его пугает
     */
    public function keyGetClass(string $key, string $className): string
    {
        $_result = $className;

        // Если имя класса для ответа функции удалось найти с помощью события "до начала поиска правила по ключу"
        if (count($this->events->eventSearchBefore) > 0) foreach ($this->events->eventSearchBefore as $eventFunction)
        {
            $tmpResult = $eventFunction($this, $key, $className);
            if (is_string($tmpResult) && $tmpResult !== '') return $tmpResult;
        }

        // если для ключа есть правила
        if (isset($this->rules[$key]))
        {
            $rule = $this->rules[$key];

            /** @psalm-suppress InvalidReturnStatement к сожалению нет возможности более строго типизировать эту функцию, так что псалм тут будет ругаться всегда */
            if (is_callable($rule)) $_result = $rule($key);
            else $_result = $rule;
        }

        // Если имя класса для ответа функции удалось найти с помощью события "до начала поиска правила по ключу"
        if (count($this->events->eventSearchAfter) > 0) foreach ($this->events->eventSearchAfter as $eventFunction)
        {
            $tmpResult = $eventFunction($this, $key, $className, $_result);
            if (is_string($tmpResult) && $tmpResult !== '') return $tmpResult;
        }

        return $_result;
    }
}

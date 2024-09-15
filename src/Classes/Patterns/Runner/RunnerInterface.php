<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Runner;

/**
 * Интерфейс для "классов раннеров". Классов, имеющих функцию `run()` выполняющую основной
 * скоуп работ класса. Используется для классов выполняющих одно конкретное действие, можно сказать, что это классы-функции
 *
 * (!) Предполагается, что классы раннеры получают список аргументов через конструктор (или сеттеры), а функция `run()`
 * только выполняет какую-то работу (и возможно возвращает результат)
 *
 * Выполнить действие заложенное в класс, реализующие интерфейс можно вызывать как
 * <pre>
 * new (RunnerInterface())::run()
 * </pre>
 */
interface RunnerInterface
{
    /**
     * Выполняет функцию класса
     *
     * @return mixed|void|never
     */
    public function run();
}

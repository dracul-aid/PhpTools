<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Classes\Patterns\Iterator;

/**
 * Интерфейс для удобной работы с классами-итераторами
 *
 * (!) "Курсор" это указатель на текущий элемент массива/списка....
 *
 * Интерфейс является "миксом" из {@see \IteratorAggregate} и {@see \Iterator}, из первого берется удобный способ итерирования
 * ({@see \IteratorAggregate::getIterator()} не изменяет состояние "курсора"), а из последнего набор функций, который нужен
 * для более тонкого перебора. Дополнительно интерфейс дополнен функционалом, просто облегчающим использование {@see \Iterator}
 *
 * Оглавление:
 * <br>- {@see self::getIterator()} Итератор, проведет перебор не изменяя позицию "курсора"
 * <br>--- Поддержка {@see \Iterator}
 * <br>- {@see self::current()} Вернет значение "текущего" элемента
 * <br>- {@see self::key()} Вернет текущий ключ (т.е. текущую позицию курсора)
 * <br>- {@see self::next()} Сдвигает "курсор" на позицию вперед
 * <br>- {@see self::valid()} Проверит, вышел ли курсор за пределы содержимого
 * <br>- {@see self::rewind()} Перемотает в начало, т.е. установит "курсор" на стартовую позицию
 * <br>--- Расширение функционала
 * <br>- {@see self::next()} Позволяет передвинуть курсор вперед или назад на указанное кол-во позиций
 * <br>- {@see self::currentValueAndNext()} Вернет текущее значение и сдвинет "курсор", в случае достижения "конца", вернет NULL
 * <br>- {@see self::currentElementAndNext()} Вернет текущий ключ и значение и сдвинет "курсор"
 */
interface IteratorInterface extends \IteratorAggregate
{
    /**
     * Итератор, проведет перебор не изменяя позицию "курсора"
     *
     * @return \Traversable<int, mixed>
     */
    public function getIterator(): \Traversable;

    /**
     * Вернет значение "текущего" элемента
     *
     * (!) Если курсор находится "за пределами списка" - вернет NULL (следствие максимальной "схожести" с {@see \Iterator})
     *
     * @return mixed
     */
    public function current(): mixed;

    /**
     * Вернет текущий ключ (т.е. текущую позицию курсора)
     *
     * @return mixed
     */
    public function key(): mixed;

    /**
     * Сдвигает "курсор", на указанную позицию, обычно это значит "Переход к следующему ключу"
     *
     * (!) В ходе перемотки может выйти "за границу списка" (следствие максимальной "схожести" с {@see \Iterator})
     *
     * @param    int   $position   Сдвиг на какую позицию (можно сдвигать, в том числе и "назад")
     *
     * @return  $this
 */
    public function next(int $position = 1): self;

    /**
     * Проверит, вышел ли курсор за пределы содержимого
     *
     * @return bool
     */
    public function valid(): bool;

    /**
     * Перемотает в начало, т.е. установит "курсор" на стартовую позицию
     *
     * @return $this
     */
    public function rewind(): self;

    /**
     * Вернет текущее значение и сдвинет "курсор", в случае достижения "конца", вернет NULL
     * (Используется для облегчения работы с `while()`)
     *
     * (!) Обратите внимание, может вернуть NULL, даже не достигнув "конца", это произойдет если значение итератора
     * на одном из шагов будет NULL
     *
     * Это "сахарный" метод для {@see self::next()} и {@see self::current()}, он реализован в {@see IteratorTrait::currentValueAndNext()}
     * <code>
     * while($value = currentValueAndNext()) {echo $value;}
     * </code>
     *
     * @param   int   $position   Сдвиг на какую позицию (можно сдвигать, в том числе и "назад")
     *
     * @return  mixed
     */
    public function currentValueAndNext(int $position = 1);

    /**
     * Вернет текущий ключ и значение и сдвинет "курсор"
     *
     * Используется для облегчения работы с `while()`:
     * <br>- При попытке чтения последнего элемента вернет [$key, $value, true]
     * <br>- Если во время чтения вышли "за границу содержимого" вернет [null, null, false]
     *
     * Это "сахарный" метод для {@see self::next()}, {@see self::key()} и {@see self::current()}, он реализован в {@see IteratorTrait::currentElementAndNext()}
     * <code>
     * [$key, $value, $valid] = currentElementAndNext()
     * if (!$valid) echo 'Перебрали все варианты';
     *
     * while (true) {
     *   [$key, $value, $valid] = currentElementAndNext();
     *   if (!$valid) break;
     * }
     * </code>
     *
     * @param   int   $position    Сдвиг на какую позицию (можно сдвигать, в том числе и "назад")
     *
     * @return  array{0: mixed, 1: mixed, 2: bool}  Вернет массив вида [ключ, значение, чтение удалось или нет]
     */
    public function currentElementAndNext(int $position = 1): array;
}

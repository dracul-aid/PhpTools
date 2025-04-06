<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Arrays\Objects;

use DraculAid\PhpTools\Arrays\Objects\Interfaces\ArrayInterface;
use DraculAid\PhpTools\Classes\Patterns\Iterator\AbstractIterator;
use DraculAid\PhpTools\tests\Arrays\Objects\ListObjectTest;

/**
 * Объект, "реализующий" массив-список (т.е. массив с последовательными числовыми ключами)
 *
 * Объекты-списки максимально поддерживают совместимость с "массивами" PHP, это значит, что
 * <br>- Попытка прочтения несуществующего элемента вернет NULL (и выбросит предупреждение)
 * <br>- Попытка записать за пределами списка будет выполнена, но запись будет произведена в конец списка (и выбросит предупреждение)
 *
 * Объекты-списки имеют полный набор для поддержки {@see \Iterator}, но реализуют {@see \IteratorAggregate}, IteratorAggregate
 * выбран, так как не заставляет сбрасывать курсор после использования `foreach()` или {@see iterator_to_array()}
 *
 * Оглавление:
 * <br>--- Операции записи
 * <br>- {@see self::exchangeArray()} Установит новый массив в качестве списка
 * <br>- {@see self::insert()} Вставит в указанную позицию подмассив, раздвинув список
 * <br>- {@see self::addEnd()} Добавит элемент(ы) в конец списка
 * <br>- {@see self::offsetSet()} установит новый или отредактирует элемент
 * <br>--- Операции чтения
 * <br>- {@see self::offsetGet()} Вернет указанный элемент, если элемент не существует - вернет NULL
 * <br>- {@see self::getArrayCopy()} Вернет список ввиде массива
 * <br>- {@see self::get()} Вернет указанный элемент или выбросит исключение
 * <br>--- Проверка существования элемента
 * <br>- {@see self::offsetExists()} Проверит, существует ли указанный элемент, аналогично `isset()`
 * <br>- {@see self::keyExists()} Проверит, существует ли указанный элемент, аналогично `array_key_exists()`
 * <br>--- Перебор значений
 * <br>- {@see self::getIterator()} Итератор для перебора списка
 * <br>- {@see self::current()} Вернет "текущий" элемент
 * <br>- {@see self::key()} Вернет "текущий" ключ
 * <br>- {@see self::next()} Переход к следующему ключу
 * <br>- {@see self::rewind()} Перемотает в начало
 * <br>- {@see self::valid()} Проверит, существует ли "текущий элемент"
 * <br>- {@see self::setCursor()} Установит позицию курсора
 * <br>--- Прочее
 * <br>- {@see self::count()} Вернет кол-во элементов
 * <br>- {@see self::offsetUnset()} Удалит указанный элемент
 *
 * Test cases for class {@see ListObjectTest}
 *
 * @todo Добавить полный набор функций {@see ArrayObject}, что бы сделать максимально совместимым с ним
 */
class ListObject extends AbstractIterator implements ArrayInterface
{
    /** @var int<0, max> Курсор списка (Хранит позицию текущего элемента списка) */
    protected int $cursor = 0;

    /** @var array<int<0, max>, mixed> Массив-список хранимых значений */
    protected array $list = [];

    /** TRUE если разрешено выбрасывать "предупреждения" */
    protected bool $warningOn = true;

    /**
     * Создаст новый список
     *
     * @param   array   $newList   Массив значений для установки, в качестве списка
     */
    public function __construct(array $newList = [])
    {
        $this->exchangeArray($newList);
    }

    /**
     * Установит новый массив в качестве списка и сбросит "курсор"
     * 
     * @param   array   $newList
     *
     * @return  $this
     */
    public function exchangeArray(array $newList): static
    {
        //  TODO PHP8 array_values станет отчасти ненужной, так как во многих случаях уже передан список (см array_is_list())
        $this->list = array_values($newList);
        $this->cursor = 0;

        return $this;
    }

    /**
     * Вернет "Список" ввиде массива
     *
     * @return array<int<0, max>, mixed>
     */
    public function getArrayCopy(): array
    {
        return $this->list;
    }

    /**
     * Добавляет значения в конец списка
     *
     * @see self::insert() Добавит элемент(ы) в указанную позицию списка
     *
     * @param   mixed ...$values
     *
     * @return  $this
     */
    public function addEnd(...$values): static
    {
        array_push($this->list, ...$values);

        return $this;
    }

    /**
     * Добавит значения в указанную позицию, старые значения сдвинет в сторону конца списка
     *
     * Если передано значение, за пределами списка, то будет сгенерировано предупреждение и:
     * <br>$position положительное - будет добавлено в конец списка (т.е. в позицию count())
     * <br>$position отрицательное - будет добавлено в начало списка (т.е. в позицию 0)
     *
     * @see self::addEnd() Добавит элемент(ы) в конец списка
     *
     * @param  int        $offset   Стартовая позиция для "раздвигания" (отсчет с 0-ля), если передано отрицательное число - то позиция с конца
     * @param  mixed   ...$values   Список значений для установки
     *
     * @return $this
     */
    public function insert(int $offset, ...$values): static
    {
        if (count($this->list) === 0)
        {
            $this->list = $values;

            return $this;
        }

        if ($offset >= count($this->list))
        {
            return $this->addEnd(...$values);
        }

        // * * *

        if ($offset < 0)
        {
            $offset = count($this->list) + $offset;
            if ($offset < 0) $offset = 0;
        }

        $this->list = array_merge(
            array_slice($this->list, 0, $offset),
            $values,
            array_slice($this->list, $offset)
        );

        return $this;
    }

    /**
     * Вернет указанный элемент, или выбросит исключение, если элемент не существует
     *
     * @param   int   $offset   Номер элемента, отсчет от 0-ля (отрицательное число - позиция с конца списка)
     *
     * @return  mixed
     *
     * @throws  \TypeError          Если ключ был передан не целым числом
     * @throws  \RangeException   Если был запрошен элемент вне диапазона значений списка
     */
    public function get(int $offset): mixed
    {
        if ($this->count() === 0) throw new \RangeException("List is empty");
        elseif ($offset >= 0 && $this->count() < $offset) throw new \RangeException("Element #{$offset} not found, List size: {$this->count()}");
        elseif ($offset < 0 && $this->count() <= abs($offset)) throw new \RangeException("Element #{$offset} not found, List size: {$this->count()}");

        // * * *

        // Отрицательное число - позиция с конца списка
        if ($offset < 0) $offset = $this->count() + $offset;

        /** @psalm-suppress InvalidArgument К этому моменту $offset станет "положительным" */
        return $this->offsetGet($offset);
    }

    /**
     * Проверяет, есть ли в списке элемент с указанным номером (работает аналогично {@see array_key_exists()})
     *
     * @see self::offsetExists() Осуществляет проверку аналогичную {@see isset()}
     *
     * @param   int   $offset    Номер элемента, отсчет с 0-ля (отрицательное число - номер элемента с конца списка)
     *
     * @return  bool
     */
    public function keyExists(int $offset): bool
    {
        if ($offset < 0) $offset = count($this->list) + $offset;

        return array_key_exists($offset, $this->list);
    }

    /**
     * Вернет кол-во элементов в списке
     *
     * @return int<0, max>
     */
    public function count(): int
    {
        return count($this->list);
    }

    /**
     * Проверит, существует ли указанный элемент списка (реализует поддержку {@see isset()})
     *
     * @see self::keyExists() Осуществляет проверку аналогичную {@see array_key_exists()}
     *
     * @param   int<0, max>   $offset   Номер элемента (отрицательное число - позиция с конца списка)
     *
     * @return  bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * Вернет указанный элемент или NULL, если элемент отсутствует
     *
     * В случае отсутствия запрошенного элемента также будет выброшено предупреждение
     *
     * @see self::get() Вернет элемент с указанным номером или выбросит исключение
     *
     * @param   int<0, max>   $offset   Номер элемента (отрицательное число - позиция с конца списка)
     *
     * @return  mixed
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (is_int($offset) && array_key_exists($offset, $this->list))
        {
            return $this->list[$offset];
        }

        // * * *

        if ($this->warningOn)
        {
            $warning = 'Undefined array key';

            if (is_int($offset)) $warning .= " {$offset}";
            else $warning .= ' is a ' . gettype($offset);

            trigger_error($warning, E_USER_WARNING);
        }

        return null;
    }

    /**
     * Для записи нового значения в "стиле" массива
     *
     * Правила работы:
     * <br>- $object[] - будет добавлен в конец списка
     * <br>- $object[N] и N есть в списке - будет редактирование
     * <br>- $object[N] и N вне списка (т.е. за пределами count()) или не положительное число - указанный индекс будет
     *   проигнорирован и запись будет добавлена в "конец" списка + выбросит предупреждение
     *
     * @see self::addEnd() Добавит элемент(ы) в конец списка
     * @see self::insert() Добавит элемент(ы) в указанную позицию списка
     *
     * @param  int<0, max>   $offset   Номер позиции для вставки
     * @param  mixed         $value    Значение для установки
     *
     * @return $this
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): static
    {
        if ($offset === null)
        {
            $this->list[] = $value;
        }
        elseif (!is_int($offset))
        {
            $warning = '$offset must be int, but it is a ' . gettype($offset);
            $this->list[] = $value;
        }
        elseif ($offset < 0)
        {
            /** @psalm-suppress InvalidCast Псалм истерит на ровном месте, тут всегда будет число, и оно без проблем преобразуется в строку */
            $warning = "{$offset} must be greater than 0, but is {$offset}" . gettype($offset);
            $this->list[] = $value;
        }
        elseif ($offset > count($this->list))
        {
            $warning = '$offset must be less than the last key+1 (' . count($this->list) . "), but is {$offset}" . gettype($offset);
            $this->list[] = $value;
        }
        else {
            $this->list[$offset] = $value;
        }

        if ($this->warningOn && isset($warning)) trigger_error($warning, E_USER_WARNING);

        return $this;
    }

    /**
     * Удаляет элемент из списка, элементы за удаленным элементом, изменят свой ключ на -1
     *
     * (!) В результате удаления "курсор" может оказаться за пределами списка
     *
     * @param   int<0, max>   $offset   Номер позиции для удаления
     *
     * @return  $this
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): static
    {
        if (!is_int($offset) || count($this->list) === 0) return $this;

        if ($offset < 0 || $offset >= count($this->list)) return $this;

        if ($offset === 0 && count($this->list) === 1)
        {
            $this->list = [];

            return $this;
        }

        // * * *

        $this->list = array_merge(
            array_slice($this->list, 0, $offset),
            array_slice($this->list, $offset + 1)
        );


        return $this;
    }

    /**
     * Итератор списка.
     *
     * (!) Итерирование, в том числе и с помощью `foreach($object)` или `iterator_to_array($object)` не изменяет позицию "курсора"
     *
     * @return \Generator<int, mixed>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->list as $key => $value) yield $key => $value;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function current(): mixed
    {
        return $this->list[$this->cursor] ?? null;
    }

    /** {@inheritdoc} */
    public function next(int $position = 1): static
    {
        /** @psalm-suppress InvalidPropertyAssignmentValue Да, тут случайно можно оказаться да диапазоном списка, т.е. в отрицательной позиции */
        $this->cursor += $position;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return int<0, max>
     * @psalm-suppress ImplementedReturnTypeMismatch Псалм ругается, что функция возвращает значение (о чем не говорит базовый интерфейс функции) и мы действительно так хотим
     */
    #[\ReturnTypeWillChange]
    public function key(): int
    {
        return $this->cursor;
    }

    /** @inheritdoc */
    public function valid(): bool
    {
        return $this->count() > $this->cursor && $this->cursor >= 0;
    }

    /** {@inheritdoc} */
    public function rewind(): static
    {
        $this->cursor = 0;

        return $this;
    }

    /**
     * Установит новую позицию для курсора (может установить позицию "за пределами списка")
     *
     * @param   int<0, max>   $position
     *
     * @return  $this
     */
    public function setCursor(int $position): static
    {
        $this->cursor = $position;

        return $this;
    }

    /** {@inheritdoc} */
    public function currentElementAndNext(int $position = 1): array
    {
        return parent::currentElementAndNext($position);
    }
}

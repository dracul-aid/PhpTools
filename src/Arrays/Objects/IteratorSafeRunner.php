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

use DraculAid\Php8forPhp7\TypeValidator;
use DraculAid\PhpTools\Arrays\Objects\Components\ArrayObjectTools\IteratorSafeRunThrowableStructure;
use DraculAid\PhpTools\Classes\Patterns\Iterator\IteratorInterface;
use DraculAid\PhpTools\Classes\Patterns\Runner\StaticRunnerInterface;
use DraculAid\PhpTools\ExceptionTools\ExceptionTools;
use DraculAid\PhpTools\tests\Arrays\Objects\IteratorSafeRunnerTest;

/**
 * Обеспечивает "Безопасный" перебор объектов {@see \Iterator}
 *
 * - После перебора "курсор" будет перемещен на позицию, которую он имел до перебора
 * - Возможен перебор итератора с перехватом ошибок
 *
 * В обычной ситуации, перебирая элементы объекта {@see \Iterator}, например с помощью `foreach()` или {@see iterator_to_array()}
 * "курсор" будет перемещен в конец, и окажется за пределами содержимого, и его нужно будет перемотать в начало самостоятельно
 *
 * Функцию также можно использовать для получения массива для итератора, без изменения позиции курсор
 * <pre>
 * iterator_to_array(ArrayObjectTools::iteratorSafeRun($iterator, $cursor));
 * </pre>
 *
 * Оглавление:
 * <br>- {@see IteratorSafeRunner::exe()} Обеспечивает перебор объектов {@see \Iterator} с восстановлением позиции "курсора" после перебора
 * <br>--- Варианты взаимодействия с ошибками
 * <br>- {@see self::$errorRule} Устанавливает правило взаимодействия с ошибками
 * <br>- {@see self::$throwableList} Список накопленных исключений
 * <br>- {@see IteratorSafeRunner::EXCEPTION_RULES_NO_SAFE} Исключения Не будут перехвачены
 * <br>- {@see IteratorSafeRunner::EXCEPTION_RULES_SAFE} Исключения Будут перехвачены, но Не будут накаливаться
 * <br>- {@see IteratorSafeRunner::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE} Исключения Будут перехвачены и Будут накаливаться
 * <br>---
 * <br>- {@see self::$iterator} Перебираемый объект-итератор
 * <br>- {@see self::$cursor} Ссылка на курсор
 * <br>- {@see self::getIterator()} Безопасно переберет установленный итератор
 *
 * Test cases for class {@see IteratorSafeRunnerTest}
 */
final class IteratorSafeRunner implements StaticRunnerInterface, \IteratorAggregate
{
    /** Исключения Не будут перехвачены, обслуживает {@see self::$errorRule} */
    public const EXCEPTION_RULES_NO_SAFE = 0;
    /** Исключения Будут перехвачены, но Не будут накаливаться в {@see self::$throwableList}, обслуживает {@see self::$errorRule} */
    public const EXCEPTION_RULES_SAFE = 1;
    /** Исключения Будут перехвачены и Будут накаливаться в {@see self::$throwableList}, обслуживает {@see self::$errorRule} */
    public const EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE = 2;

    /**
     * Итератор для перебора
     * @var \Iterator|IteratorInterface
     * @readonly
     * @todo PHP8 сделать "риад онли"
     * @todo PHP8 добавить типизацию
     */
    public $iterator;

    /**
     * Ссылка на курсор
     *
     * @var mixed
     * @todo PHP8 сделать "риад онли"
     * @todo PHP8 добавить типизацию
     */
    public $cursor;

    /**
     * Правила работы с исключениями, см {@see self::EXCEPTION_RULES_NO_SAFE}, {@see self::EXCEPTION_RULES_SAFE} {@see self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE}
     * @psalm-var int-mask-of<IteratorSafeRunner::EXCEPTION_RULES_*>
     */
    public int $errorRule = self::EXCEPTION_RULES_NO_SAFE;

    /**
     * Список пойманных исключений
     * @var IteratorSafeRunThrowableStructure[]
     */
    public array $throwableList = [];

    /**
     * Создаст объект для "безопасного" перебора итераторов ({@see \Iterator})
     *
     * @param   \Iterator|IteratorInterface   $iterator    Итератор для безопасного перебора
     * @param   mixed                         $cursor      Ссылка на курсор
     * @param   int                           $errorRule   Правила работы с выпадающими исключениями, см {@see self::EXCEPTION_RULES_NO_SAFE},
     *                                                     {@see self::EXCEPTION_RULES_SAFE} {@see self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE}
     * @return void
     *
     * @psalm-param int-mask-of<IteratorSafeRunner::EXCEPTION_RULES_*> $errorRule
     *
     * @todo PHP8 добавить типизацию аргументам функции
     * @psalm-suppress UnusedParam Псалм не понимает, что мы прокидываем ссылку куда-то дальше
     */
    public function __construct(object $iterator, &$cursor, int $errorRule = self::EXCEPTION_RULES_NO_SAFE)
    {
        TypeValidator::validateOr($iterator, [\Iterator::class, IteratorInterface::class]);

        $this->iterator = $iterator;
        $this->cursor = &$cursor;
        $this->errorRule = $errorRule;
    }

    /**
     * Переберет итератор ({@see \Iterator}), не изменив ему позицию курсора
     *
     * @param   \Iterator|IteratorInterface   $iterator        Итератор
     * @param   mixed                         $cursor          Ссылка на "курсор"
     * @param   bool                          $noException     TRUE если нужно перехватывать "исключения"
     *
     * @return  \Generator
     *
     * @todo TEST покрыть функцию тестам
     */
    public static function exe(object $iterator, &$cursor, bool $noException = false): \Generator
    {
        yield from (new self($iterator, $cursor, $noException ? self::EXCEPTION_RULES_SAFE : self::EXCEPTION_RULES_NO_SAFE))->getIterator();
    }

    /**
     * Переберет все варианты установленного итератора (без изменения положения его "курсора")
     *
     * @return \Generator
     */
    public function getIterator(): \Generator
    {
        if (is_object($this->cursor)) $startPositionCursor = clone $this->cursor;
        else $startPositionCursor = $this->cursor;

        if ($this->errorRule === self::EXCEPTION_RULES_NO_SAFE) yield from $this->runNoSafe();
        else $this->runSafe();

        $this->cursor = $startPositionCursor;
    }

    /**
     * Перебирает все элементы переданного итератора
     *
     * @return \Generator
     */
    private function runNoSafe(): \Generator
    {
        $this->iterator->rewind();

        foreach ($this->iterator as $key => $value) yield $key => $value;
    }

    /**
     * Перебирает все элементы переданного итератора, в случае если будут выброшены исключения, перехватит их и
     * заполнит {@see self::$throwableList}
     *
     * @return \Generator
     *
     * @todo TEST покрыть функцию тестами (базовый тест есть, но нужны тесты для функций перехвата исключений)
     */
    private function runSafe(): \Generator
    {
        // при провале перемотки, генератор не будет выполнен
        if (!$this->runSafeRewind()) return;

        // если первая позиция сразу была не валидной - нет смысла начинать перебор
        if (!$this->runSafeValid(null, null, null)) return;

        $step = 0;
        do {
            $key = $this->runSafeKey($step);
            $value = $this->runSafeCurrent($step, $key);

            yield $key => $value;

            $step++;

            $this->runSafeNext($step, $key, $value);
            $goNextStep = $this->runSafeValid($step, $key, $value);

        } while ($goNextStep);
    }

    /**
     * Проведет безопасную перемотку "в начало" (т.е. сдвинет курсор в стартовую позицию)
     *
     * @return bool Вернет TRUE если перемотка удалась, или FALSE если провалилась
     */
    private function runSafeRewind(): bool
    {
        $error = ExceptionTools::callAndReturnException([$this->iterator, 'rewind']);

        // нет ошибок - значит перемотка удалась
        if ($error === null) return true;

        if ($this->errorRule !== self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE) return false;

        $this->throwableList[] = $this->iteratorSafeRunGetErrorStructure(
            $error,
            'rewind',
            IteratorSafeRunThrowableStructure::POSITION_UNDEFINED,
            null,
            null
        );

        return false;
    }

    /**
     * Безопасно проверит, не вышли ли за пределы значений "итератора"
     *
     * @param   null|int   $step    Номер позиции, если передан NULL - будет использована {@see IteratorSafeRunThrowableStructure::POSITION_UNDEFINED}
     * @param   mixed      $key     Последний полученный "ключ"
     * @param   mixed      $value   Последнее полученное "значение"
     *
     * @return  bool Вернет TRUE если перемотка удалась, или FALSE если провалилась
     *
     * @todo PHP8 добавить типизацию аргументам функции
     */
    private function runSafeValid(?int $step, $key, $value): bool
    {
        $error = ExceptionTools::callAndReturnException([$this->iterator, 'valid'], [], $isValid);

        // нет ошибок - значит перемотка удалась
        if ($error === null) return (bool)$isValid;

        if ($this->errorRule !== self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE) return false;

        $this->throwableList[] = $this->iteratorSafeRunGetErrorStructure(
            $error,
            'next',
            $step ?? IteratorSafeRunThrowableStructure::POSITION_UNDEFINED,
            $key,
            $value
        );

        return false;
    }

    /**
     * Вернет текущий ключ
     *
     * @param   int  $step   Позиция элемента
     *
     * @return  mixed
     *
     * @todo PHP8 добавить типизацию ответа функции
     */
    private function runSafeKey(int $step)
    {
        $error = ExceptionTools::callAndReturnException([$this->iterator, 'key'], [], $key);

        if ($error === null) return $key;

        if ($this->errorRule !== self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE) return false;

        $this->throwableList[] = $this->iteratorSafeRunGetErrorStructure(
            $error,
            'key',
            $step,
            null,
            ExceptionTools::safeCallWithResult([$this->iterator, 'current'])
        );

        return null;
    }

    /**
     * Вернет текущее значение
     *
     * @param   int     $step   Позиция элемента
     * @param   mixed   $key    Ключ элемента
     *
     * @return  mixed
     *
     * @todo PHP8 добавить типизацию ответа функции
     */
    private function runSafeCurrent(int $step, $key)
    {
        $error = ExceptionTools::callAndReturnException([$this->iterator, 'current'], [], $value);

        if ($error === null) return $value;

        if ($this->errorRule !== self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE) return false;

        $this->throwableList[] = $this->iteratorSafeRunGetErrorStructure(
            $error,
            'current',
            $step,
            $key,
            null
        );

        return null;
    }

    /**
     * Проведет безопасную перемотку "к следующему элементу"
     *
     * @param   null|int   $step    Номер позиции, если передан NULL - будет использована {@see IteratorSafeRunThrowableStructure::POSITION_UNDEFINED}
     * @param   mixed      $key     Последний полученный "ключ"
     * @param   mixed      $value   Последнее полученное "значение"
     *
     * @return  void
     *
     * @todo PHP8 добавить типизацию аргументам функции
     */
    private function runSafeNext(?int $step, $key, $value): void
    {
        $error = ExceptionTools::callAndReturnException([$this->iterator, 'next']);

        // нет ошибок - значит перемотка удалась
        if ($error === null) return;

        if ($this->errorRule !== self::EXCEPTION_RULES_SAFE_WITH_ERRORS_SAVE) return;

        $this->throwableList[] = $this->iteratorSafeRunGetErrorStructure(
            $error,
            'next',
            $step,
            $key,
            $value
        );
    }

    /**
     * Вернет структуру с описанием пойманной ошибки
     *
     * @param   \Throwable   $error
     * @param   string       $function
     * @param   int          $position
     * @param   mixed        $key
     * @param   mixed        $value
     *
     * @return  IteratorSafeRunThrowableStructure
     *
     * @todo PHP8 добавить типизацию аргументам функции
     */
    private function iteratorSafeRunGetErrorStructure(\Throwable $error, string $function, int $position, $key, $value): IteratorSafeRunThrowableStructure
    {
        $throwableElement = new IteratorSafeRunThrowableStructure();

        $throwableElement->throwable = $error;
        $throwableElement->functionName = $function;
        $throwableElement->position = $position;
        $throwableElement->key = $key;
        $throwableElement->value = $value;

        return $throwableElement;
    }
}

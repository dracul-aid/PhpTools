<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DraculAid\PhpTools\Console;

/**
 *
 */
final class ConsoleLightTools
{
    /** Максимальная длина ответа введенного в консоли "по умолчанию" при работе {@see ConsoleLightTools::printWithTextAnswer()} */
    public const ANSWER_LENGTH_DEFAULT = 1024;

    /** Кол-во попыток спросит "да/нет" ({@see ConsoleLightTools::printWithYesOrNo}) до того, как функция вернет NULL */
    public const YES_OR_NO_ATTEMPTS_DEFAULT = 3;

    /** @var string[] Варианты "Да" при вводе запроса "да/нет" ({@see ConsoleLightTools::printWithYesOrNo}) */
    public const YES_OR_NO_VARIANT_YES_DEFAULT = array('y', 'yes');

    /** @var string[] Варианты "Нет" при вводе запроса "да/нет" ({@see ConsoleLightTools::printWithYesOrNo}) */
    public const YES_OR_NO_VARIANT_NO_DEFAULT = array('n', 'no');

    /**
     * @var null|string|array|object Функция, которая будет вызвана для вывода в консоль. NULL будет использована `echo()`
     */
    public static string|array|object|null $printFunction = null;

    /**
     * Произведет вывод строки в консоль
     *
     * @param   string   $string   Строка для вывода
     *
     * @return  void
     * @throws  \RuntimeException   Если не удается вызвать функцию вывода
     */
    public function print(string $string): void
    {
        if (self::$printFunction === null)
        {
            echo $string;

            return;
        }

        if (!is_callable(self::$printFunction))
        {
            throw new \RuntimeException('ConsoleLightTools::$printFunction must be a callable');
        }

        self::$printFunction($string);
    }

    /**
     * Произведет вывод строки в консоль, после выводе произведет перенос строки
     *
     * @param   string   $string   Строка   для вывода
     *
     * @return  void
     * @throws  \RuntimeException   Если не удается вызвать функцию вывода
     */
    public function printNl(string $string): void
    {
        self::print($string . PHP_EOL);
    }

    /**
     * Выводит нумерованный список
     *
     * @param   iterable   $list   Элементы для вывода
     *
     * @return void
     */
    public static function printNumericList(iterable $list): void
    {
        $maxElement = $list instanceof \Countable ? $list->count() : 0;

        $position = 0;
        foreach ($list as $item) echo "";
    }

    /**
     * Вывеет строку в консоль и ожидает ввода ответа от пользователя, вернет введенный ответ
     *
     * ```
     * ConsoleLightTools::printWithAnswer('Введите ваш возраст:');
     * ```
     * Выведет (на месте "_" будет курсор для ввода ответа)
     * ```
     * > Введите ваш возраст:_
     * ```
     *
     * @param   string        $quest          Строка для вывода в консоль (если вам нужен перевод строки [т.е. \n], добавьте его сами)
     * @param   int<0, max>   $answerLength   Максимальная длина ответа (все что больше, будет откинуто)
     *
     * @return  string
     * @throws  \RuntimeException   Если не удалось прочитать ввод из STDIN или вывести "строку-вопрос" в консоль
     */
    public static function printWithTextAnswer(string $quest, int $answerLength = self::ANSWER_LENGTH_DEFAULT): string
    {
        if ($quest !== '') echo $quest;

        // * * * Отслеживаем ответ

        // Читаем строку ввода
        $input = fgets(STDIN, $answerLength);
        if ($input === false)
        {
            throw new \RuntimeException('Error reading from STDIN');
        }

        // Из строки ввода нужно убрать все пробельные символы в начале и конце,
        // так как строка будет оканчиваться символом перевода строки, пробелы спереди отбрасываем, так как скорее всего они вводятся случайно
        return trim($input);
    }

    /**
     * Выведет вопрос с вариантами ответа "Да/Нет"
     *
     * Выведет вопрос, требующий ответа "Да" или "Нет", если введен варианты отличающийся от возможных вариантов "Да/Нет",
     * то будет снова попытка задать вопрос. Если все попытки исчерпаны - функция вернет NULL
     *
     * @param   string        $quest               Вопрос
     * @param   int<0, max>   $attempts            Кол-во попыток
     * @param   int<0, max>   $addVariantInQuest   Нужно ли к вопросу добавить варианты ответа (число, добавляемых вариантов)
     * @param   string[]      $variantYes          Варианты "Да"
     * @param   string[]      $variantNo           Варианты "Нет"
     *
     * @return  null|bool           Вернет TRUE в случае ввода "Да", FALSE в случае "Нет" или NULL - если не удалось получить ответ (исчерпаны попытки ввода)
     */
    public static function printWithYesOrNo(string $quest, int $attempts = self::YES_OR_NO_ATTEMPTS_DEFAULT, int $addVariantInQuest = 1, array $variantYes = self::YES_OR_NO_VARIANT_YES_DEFAULT, array $variantNo = self::YES_OR_NO_VARIANT_NO_DEFAULT): null|bool
    {
        // если нужно добавить варианты ответов
        if ($addVariantInQuest > 0)
        {
            $variantYes = implode(', ', array_slice($variantYes, 0, $addVariantInQuest));
            $variantNo = implode(', ', array_slice($variantNo, 0, $addVariantInQuest));
            $quest .= " ({$variantYes} | {$variantNo}): ";
        }
        // если в конце вопроса нет пробельного символа
        elseif ($quest !== '' && trim(substr($quest, -1, 0)) !== '')
        {
            $quest .= " ";
        }

        // выводим, пока не введен верный ответ
        for ($i = 0; $i < $attempts; $i++)
        {
            $answer = self::printWithTextAnswer($quest);

            if (in_array($answer, $variantYes)) return true;
            elseif (in_array($answer, $variantNo)) return false;
        }

        return null;
    }
}

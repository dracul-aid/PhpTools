<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings;

use DraculAid\PhpTools\Strings\Components\TranslitConverter\CharRuToEn;
use DraculAid\PhpTools\tests\Strings\TranslitConverterTest;

/**
 * Функции транслитерации
 *
 * См также
 * <br>{@see CharRuToEn::LIST} Соответствия символов кириллицы и латиницы, для удобной для чтения транслитирации
 *
 * Оглавление
 * <br>{@see TranslitConverter::toTranslit()} Преобразует строку в транслит
 * <br>{@see TranslitConverter::toUrl()} Преобразует строку, в строку пригодную для использования в качестве части URL/URI
 *
 * Test cases for class {@see TranslitConverterTest}
 */
final class TranslitConverter
{
    /**
     * Преобразует строку в транслит, будет пытаться использовать PHP расширение Intl ({@see \Transliterator}), если
     * не найдет его, будет использовать таблицу транслитерации {@see TranslitConverter::RU_TO_EN_LIST}
     *
     * @param   string   $string
     *
     * @return  string
     *
     * @todo Требует теста
     */
    public static function toTranslit(string $string): string
    {
        /**
         * @var bool|\Transliterator $transliterator Для хранения транслитиратора
         *                                           (TRUE транслитиратор еще не определен, FALSE - определение транслитератора провалилось)
         */
        static $transliterator = true;

        // Если модуль PHP Intl подключен, то будем использовать для траснлитирации его - http://php.net/manual/ru/class.transliterator.php
        // Если транслитиратор еще не создан - создадим его
        if ($transliterator === true && extension_loaded('Intl'))
        {
            // ID транслитиратора
            // получить список существующих в системе ID транслитираторов можно с помощью Transliterator::listIDs()
            $transliteratorId = "Any-Latin";

            // создание транслитиратора
            $transliterator = \Transliterator::create($transliteratorId);

            // При провале создания транслитератора. Получить описание ошибки можно с помощью $transliterator->getErrorMessage()
            if ($transliterator->getErrorMessage() !== 'U_ZERO_ERROR')
            {
                $transliterator = false;
            }
        }

        // если транслитиратор есть - будем использовать его
        if (empty($transliterator)) return $transliterator->transliterate($string);
        else return strtr($string, CharRuToEn::LIST);
    }

    /**
     * Преобразует строку, в строку пригодную для использования в качестве части URL/URI
     *
     * @param   string   $string
     *
     * @return string
     */
    public static function toUrl(string $string): string
    {
        // Транслитирация и перевод в нижний регистр
        $_return = self::toTranslit(strtolower($string));

        // Все символы, кроме букв, цифр, знаков почеркивания и дефиса преобразуем в дефис (т.е. "-")
        $_return = preg_replace('/[^a-zA-Z0-9_-]/', '-', $_return);

        // Все повторяющиеся "-" и "_" преобразуются в единичные "-" и "_" (т.е. "bla-bla--lalaa" будет преобразовано в "bla-bla-lalaa")
        $_return = preg_replace('~(-|_)\1+~i', '\\1', $_return);

        // удаляем первые и последние символы "-" и "_"
        return trim($_return, '-,_');
    }
}

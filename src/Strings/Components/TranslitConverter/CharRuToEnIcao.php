<?php declare(strict_types=1);

/*
 * This file is part of PhpTools - https://github.com/dracul-aid/PhpTools
 *
 * (c) Konstantin Marataev <dracul.aid@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DraculAid\PhpTools\Strings\Components\TranslitConverter;

use DraculAid\PhpTools\Strings\TranslitConverter;

/**
 * Таблица транслитерации. Соответствия символов кириллицы и латиницы по формату принятому в ИКАО (Международная организация
 * гражданской авиации)
 *
 * (!) Этот формат используется в России для транслитерации для в загран паспортах и банковских картах, см https://www.gosuslugi.ru/help/faq/foreign_passport/100359
 *
 * См также
 * <br>{@see CharRuToEn} Таблица транслитерации кириллицы для наиболее удобного чтения на латинице
 * <br>{@see TranslitConverter::CyrillicToIcao()} Функция транслитерации в формат загран паспорта России (и банковских карт России)
 * <br>{@see TranslitConverter} Класс, для работы с транслитом
 *
 * Оглавление
 * <br>{@see CharRuToEn::LIST} Соответствия символов кириллицы и латиницы
 */
final class CharRuToEnIcao
{
    /**
     * Соответствия символов кириллицы и латиницы
     * (ключ - символ кириллицы или иной символ, значение символ в латинице)
     */
    public const LIST = [
        'А' => 'A', 'а' => 'a',
        'Б' => 'B', 'б' => 'b',
        'В' => 'V', 'в' => 'v',
        'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd',
        'Е' => 'E', 'е' => 'e',
        'Ё' => 'E', 'ё' => 'e',
        'Ж' => 'ZH', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z',
        'И' => 'I', 'и' => 'i',
        'Й' => 'I', 'й' => 'i',
        'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l',
        'М' => 'M', 'м' => 'm',
        'Н' => 'N', 'н' => 'n',
        'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p',
        'Р' => 'R', 'р' => 'r',
        'С' => 'S', 'с' => 's',
        'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u',
        'Ф' => 'F', 'ф' => 'f',
        'Х' => 'KH', 'х' => 'kh',
        'Ц' => 'TS', 'ц' => 'ts',
        'Ч' => 'CH', 'ч' => 'ch',
        'Ш' => 'SH', 'ш' => 'sh',
        'Щ' => 'SHCH', 'щ' => 'shch',
        'Ы' => 'Y', 'ы' => 'y',
        'Ъ' => 'IE', 'ъ' => 'ie',
        'Э' => 'E', 'э' => 'e',
        'Ю' => 'IU', 'ю' => 'iu',
        'Я' => 'IA', 'я' => 'ia',
    ];
}

# StaticDi - Имитация Di для статических методов

Стандартная боль легаси кода, огромные массивы классов со статическими методами, как правило, любая попытка
покрытия такого кода юнит-тестами и определения разного поведения для разных площадок - это боль, превращающая код
во все более ужасного монстра

`StaticDi` призвана хоть как-то решить эту проблему, позволяя вызывать статические методы не из конкретного класса,
а через функцию, которая уже определит (например, через конфиги), метод какого класса будет вызван

Ниже, в этой документации, будут представлены варианты использования `StaticDi`

## Базовый пример использования

Этот пример поможет понять, как устроен и как работает `StaticDi`

```php
<?php declare(strict_types=1);

use DraculAid\PhpTools\Classes\StaticDi\StaticDi;

// Версия класса для "прода"
class A {
    public static function isLive(): bool {
        return getenv('HOST') === 'LIVE_HOST';
    }
}
 // Версия класса для "тестов"
class B extends A {
    public static function isLive(): bool {
        return true;
    }
}

// в обычных условиях получаем класс A
StaticDi::get(A::class)::isLive();

// Для тестов поменяем возвращаемый класс
StaticDi::getDefaultInstance()->rules[A::class] = B::class;

// И теперь будем получать класс B
StaticDi::get(A::class)::isLive();
```

## Самый "простой" способ внедрения в проект

Самый простой способ, использование трейта [StaticDiMagicTrait](../src/Classes/StaticDi/StaticDiTrait.php), позволяет
вообще практически не менять код вашего проекта, все что вам нужно:
1. Удалить весь код из класса, который хотите вызывать через `StaticDi` (добавив в него трейт `StaticDiMagicTrait`)
2. Реализовать базовый класс (в него будет перенесена изначальная логика)
3. Реализовать классы с измененным поведением

**Оригинальный код**
```php
<?php declare(strict_types=1);

class StaticComponent {
    public static function isLive(): bool {
        return getenv('HOST') === 'LIVE_HOST';
    }
}

class OtherComponent {
    public function run(): void {
        if (!StaticComponent::isLive()) return;
        
        echo "On Live";
    }
}
```

**Переделка**
```php
<?php declare(strict_types=1);

class StaticComponentBase {
    public static function isLive(): bool {
        return getenv('HOST') === 'LIVE_HOST';
    }
}

class StaticComponentForUnitTest {
    public static function isLive(): bool {
        return true;
    }
}

class StaticComponent {
    use DraculAid\PhpTools\Classes\StaticDi\StaticDiMagicTrait;
}

// устанавливаем правила в конфигах
DraculAid\PhpTools\Classes\StaticDi\StaticDi::getDefaultInstance()->rules[StaticComponent::class] = StaticComponentBase::class;
// или
DraculAid\PhpTools\Classes\StaticDi\StaticDi::getDefaultInstance()->rules[StaticComponent::class] = StaticComponentForUnitTest::class;

// используем, вообще не меняя ничего
class OtherComponent {
    public function run(): void {
        if (!StaticComponent::isLive()) return;
        
        echo "On Live";
    }
}
```
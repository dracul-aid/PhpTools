# FunctionRunList - Отложенный список функций

`\DraculAid\PhpTools\Code\FunctionRunList\AbstractFunctionRunList` Предоставляет простой интерфейс для реализации "_отложенных
списков функций_". Пример использования — у вас есть код, обрабатывающий оплаты, этот код должен занести данные в БД, а также
отправить письма вашим клиентам и данные в аналитический сервис. `AbstractFunctionRunList` позволит создать вам список функций,
который вы сможете выполнить по завершению всех операций с Базами данных

`\DraculAid\PhpTools\Code\FunctionRunList\FunctionRunList` Самый простой пример реализации `AbstractFunctionRunList`, он может
подойти для большинства случаев, для которых нужно создать списки функций.

## Примеры использования `FunctionRunList`

Основные функции:
* `addFunction()` Добавит в список функций новую функцию
* `run()` Запустит выполнение установленных функций

### Список функций для выполнения

```php
use DraculAid\PhpTools\Code\FunctionRunList\FunctionRunList;

...

// Создаем список функций
$functionList = new FunctionRunList();

// Открываете транзакцию с БД
$sqlTransaction = SqlDriver::startTransaction();

try {

    // Какие-то операции с БД 
    $sqlTransaction->execute('INSERT ...');
    $sqlTransaction->execute('INSERT ...');
    
    // Добавляем отправку писем клиентам
    $functionList->addFunction($emalOrderComponent->sendEmail());
    
    // Какие-то операции с БД 
    $sqlTransaction->execute('INSERT ...');
    
    // Добавляем отправку данных в аналитический сервис
    $functionList->addFunction($analyticsComponent->sendData());

    // Применяем транзакцию (сохраняем данные в БД)
    $sqlTransaction->commit();
    
    // Применяем накопленные функции
    $functionList->run();
} catch (\Throwable) {
    // Откатываете изменения в БД
    $sqlTransaction->rollback();
}
```

### Обработка падения некоторых функций из списка функций

`AbstractFunctionRunList` устойчив к тому, что добавленные в "список функций" функции могут упасть во время своего выполнения.
Если вам необходимо как-то обработать эти падения, то, добавляя функцию, вы можете также добавить и обработчик ее падения.

```php
use DraculAid\PhpTools\Code\FunctionRunList\FunctionRunList;

...

// Создаем список функций
$functionList = new FunctionRunList();

$functionList->addFunction(
    funcion() {}, // ваша функция
    fail_function() {} // будет вызвана, если упала ваша функция
);
```
Вы также можете создать вашу функцию для обработки ошибки, с аргументом `\Throwable`, тогда в нее будет передан объект ошибки
```php
use DraculAid\PhpTools\Code\FunctionRunList\FunctionRunList;

...

// Создаем список функций
$functionList = new FunctionRunList();

$functionList->addFunction(
    funcion() {},
    fail_function(\Throwable $err) {
        echo "Функция упала, см ошибку {$err}";    
    }
);
```
Если устанавливаемая вами функция не должна выполняться, в случае, "если ранее упала любая другая функция", то вы можете установить
для вашей функции `bool` аргумент, в который будет передан `TRUE` (если ранее установленные функции отработали успешно) или
`FALSE` (если хотя бы одна ранее вызванная функция упала)
```php
use DraculAid\PhpTools\Code\FunctionRunList\FunctionRunList;

...

// Создаем список функций
$functionList = new FunctionRunList();

$functionList->addFunction(
    funcion(bool $notError) {
        // если хотя бы одна функция, вызванная до этой функции, упала
        if ($notError === false) return;
        
        // полезный код функции
        ...
    },
    fail_function(\Throwable $err) {
        // эту ошибку также можно найти в getFailList()
        // если эта функция также упадет, то ее ошибку можно найти в getFailRollbackList()
        echo "Функция упала, см ошибку {$err}";    
    }
);
```

## Дополнительные возможности `AbstractFunctionRunList`

`AbstractFunctionRunList` предоставляет список ошибок, пойманных во время выполнения списка функций
* `getFailList()` - Ошибки, выброшенные функциями из списка функций
* `getFailRollbackList()` - Ошибки, выброшенные функциями, обрабатывающими ошибки

Для расширения возможностей `FunctionRunList` следует создать свой класс-список на основе `AbstractFunctionRunList` переопределив
некоторые его методы:
* `beforeRun()` - Вызывается перед обработкой "списка функций", если вернет `FALSE`, то список функций не будет выполнен. 
* `afterRun()` - Вызывается по окончанию выполнения "списка функций"
* `getFunctionElementClass()` - Вернет имя класса-структуры, хранящего установленную функцию (а также выполняющую ее)
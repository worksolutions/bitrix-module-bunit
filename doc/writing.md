##### [Главная страница](../readme.md)

# Создание тестов

Именно умение грамотно писать тесты определяет успешность внедрения модульного тестирования в проект, инструмент тут может только помочь, но он не сделает всю работу самостоятельно.

## Определение тестового набора (класса)

Тестовый набор определяется созданием класса в тестовом каталоге. Как уже упомяналось в описании настройки модуля, в каталоге тестов можно и нужно создавать подкаталоги хранения тестовых классов. Так хранилище тестов будет выглядеть более элегантным и структурированным. Каждый тестовый класс должен содержать в конце названия и имени файла постфикс ```TestCase```. Все тестовые классы являются наследниками от базового класса ```\WS\BUnit\Cases\BaseCase```. Специальными признаками при определении тестов являются аннотации в комментариях к классу.

*Пример определения тестового набора:*

```php
<?php
/**
 * @label component
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */
class TestingTestCase extends \WS\BUnit\Cases\BaseCase {
    public function setUp() {
        // Создание фикстуры для прохода тестовых методов...
    }

    /**
     * @test
     */
    public function isTrue() {
        $this->getAssert()->asEmpty(false);
    }

    /**
     * @skip
     * @test
     */
    public function useResultModifier() {
        $rm = new \WS\BUnit\Invokers\ResultModifierInvoker("project:test.with.class");
        $rm->setArResult(array('id' => 10));
        $rm->execute();
        $this->getAssert()->equal($rm->getArResultValue("id"), 10, "Params are not equal");
    }

    /**
     * @throws Exception
     * @label main
     * @test
     */
    public function throwsException() {
        throw new LogicException();
    }

    public function paramsForTest() {
        return array(
            array(false, 10),
            array(false, 20),
            array(true, 30),
        );
    }

    /**
     * Tests whether data more than 25
     * @test
     * @dataProvider paramsForTest
     */
    public function testDataProvider($expected, $number) {
        $actual = $number > 25;
        $this->getAssert()->equal($actual, $expected);
    }

    /**
     * @test
     */
    public function useDB() {
        CModule::IncludeModule("iblock");
        $dbResult = CIBlock::getList(array(), array());
        $this->getAssert()->asTrue($dbResult->AffectedRowsCount() > 0, "Count of iblocks should be more than 0");
    }

    public function tearDown() {
        // Удаление созданных фикстур...
    }
}
```

При запуске каждого теста используются два специальных метода: ```setUp``` и ```tearDown```. Первый вызывается перед началом запуска теста, второй - после окончания работы теста, даже в случае если тест не прошел проверку. Тестовые методы определяются аннотациями ```@test```.

## Определение тестов

Единицей модульного теста является алгоритм проверки определенного функционала проверки. Важно чтобы этот функционал был как можно меньше, а каждый тест выполнял только одну основную проверку или один аспект проверки корректной работы функционала. Полная проверка корректности работы функционала определяется тестовым набором. Тестовый метод в наборе должен содержать аннотацию ```@test```, именно по этому признаку этот метод будет запускаться при общем проходе тестов, остальные методы являются вспомогательными или служебными для автора тестового набора.

#### Проверка
Итогом теста должна являться проверка всех тестов и от каждого из них зависит успешность результата. Тест считается не пройденным, если хотя бы одна проверка оказалась ложной.

*Пример проверки*

```php
// ...

$this->getAssert()->asTrue($a == 10, "Сообщение появится в случае, если проверка не будет пройдена");
```

Существует несколько методов проверки:

- ```asTrue($actual, $message = "")``` - проверка актуального значения на истинность;
- ```asFalse($actual, $message = "")``` - проверка актуального значения на отрицание;
- ```same($actual, $expected, $message = "")``` - строгое сравнение двух объектов, для объектов ссылки должны указывать на один и тот же объект, для простых типов данных - сравнение происходит по типу и значению;
- ```equal($actual, $expected, $message = "")``` - нестрогое сравнение значений;
- ```notEqual($actual, $expected, $message = "")``` - проверка значений на неравенство без приведения типов;
- ```asEmpty($actual, $message = "")``` - проверка значения на пустоту;
- ```fail($message = "")``` - явное указание наличия ошибки, в основном используется вкупе с условным оператором или выбросом исключения:
```php
// ...

} catch (Exception $e) {
    $this->fail("Отлов исключения означает неверное поведение функционала");
}
```

Также в тестах можно провоцировать выбросы исключений и впоследствии делать проверки на принадлежность объектов выброшенных исключений к конкретным классам. Причем, если сработала исключительная ситуация и объект исключения соответствует ожидаемому - тест считается пройденным. Ожидание исключения указывается аннотацией ```@throws``` в комментарии к тестовому методу, например:
```php
/**
 * @throws Exception
 * @test
 */
public function throwsException() {
    // Тест будет пройден, так как ожидается выброс исключения
    throw new LogicException();
}

/**
 * @throws InvalidArgumentException
 * @test
 */
public function exceptionInDepth() {
    $object = new SomeObject();
    $object->setArray(10);
}
```

## Выделение тестов в группы
Для обеспечения группирования списков тестов могут использоваться метки, определенные при создании тестов и тестовых наборов. Метки задаются аннотацией ```@label``` в комментарии к тестовому методу или тестовому классу (набору) проекта. Причем, в первом случае метка теста рассматривается индивидуально для каждого теста, а во втором - влияет одновременно на все тесты. Можно определять несколько меток для тестов и наборов. Пример:

```php

/**
 * @test
 *
 * @throws InvalidArgumentException
 *
 * @label nagative
 * @label core
 */
public function excptionInDepth() {
    $object = new SomeObject();
    $object->setArray(10);
}
```

## Пропуск выполнения тестов
Иногда может потребоваться пропуск нескольких тестов при выполнении. Это бывает необходимо, когда требуется выяснить как один тест влияет на другой или отключить несколько тестов для отладки функционала. Аннотация ```@skip``` указывает на то, что тест при запуске нужно всегда пропускать. Также можно пропускать и тестовый набор, например:

```php
/**
 * @test
 * @skip пока не будет проверен остальной функционал
 *
 * @throws InvalidArgumentException
 *
 * @label nagative
 * @label core
 */
public function excptionInDepth() {
    $object = new SomeObject();
    $object->setArray(10);
}
```

## Выполнение тестирования с множественным набором (поставщик данных)
Для выполнения одного и того же алгоритма тестирования с разными данными существует абстракция ```поставщик данных``` (data provider) при применении которого тест запускается несколько раз с разными данными:

```php
/**
 * Data provider for testMoreThan25
 */
public function listOfData() {
    return array(
        // первый элемент - результат, остальные аргументы
        array(false, 10),
        array(false, 20),
        array(true, 30),
    );
}

/**
 * Tests whether data more than 25
 * @test
 * @dataProvider listOfData
 */
public function testMoreThan25($expected, $number) {
    $actual = $number > 25;
    $this->getAssert()->equal($actual, $expected);
}
```

Таким образом тест ```testMoreThan25``` будет запущен 3 раза (по числу элементов результата метода ```listOfData```). Каждый из элементов результата поставщика данных должен быть массивом. Элементы массива будут переданы в качестве аргументов в тестовый метод. Принято первым аргументом объявлять ожидаемый результат, затем тестовые данные. Это делается для того, чтобы при изменении набора тестовых данных параметр результата остался неизменным.

## Для проектов на Битриксе

CMS 1C-Битрикс имеет свои абстракции реализации проектов. Основные из них которые требуются разработчику наиболее часто - это компоненты страниц, вызов и реакция на события системы.

Модуль позволяет искусственно запускать события и код компонентов для проверки тестовых случаев.

#### Тестирование работы компонента

Для помощи в тестировании кода компонента используется класс ```\WS\BUnit\Invokers\ComponentInvoker```

Методы класса:

- ```__constructor($componentName)``` - инициализация объекта запуска компонента, имя компонента такое же как в методе ```CMain::IncludeComponent()``` ядра;
- ```setParams($params)``` - устанавливает параметры для запуска тестироуемого компонента;
- ```execute()``` - запускает компонент на выполнение (шаблон при этом не используется);
- ```getResultValue($name)``` - возращает параметр $arResult по ключу $name;
- ```getArResult()``` - возращает полный $arResult работы компонента;
- ```getExecuteResult()``` - возращает результат работы компонента, когда в коде компонента используется оператор возрата ```return```.

Пример:

```php
// ...

/**
 * @label component
 * @test
 */
public function useComponentInvoker() {
    $component = new \WS\BUnit\Invokers\ComponentInvoker("project:test.component");
    $component->setParams(array("id" => 10));
    $component->execute();
    $this->getAssert()->equal($component->getResultValue("id"), 10, "Результат не верен");
}
```

#### Тестирование работы адаптера шабалона (result_modifier)

Тестировать result_modifier шаблона компонента можно объектом класса ```\WS\BUnit\Invokers\ResultModifierInvoker```.

Методы:

- ```__construct($componentName, $template)``` - инициализация объекта, параметры совпадают с параметрами метода ```CMain::IncludeComponent()```;
- ```setArResult($arResult)``` - искусственная установка результата для передачи адаптеру;
- ```execute()``` - запуск адаптера на выполнение;
- ```getArResult()``` - возращает полный ```$arResult``` работы адаптера;
- ```getArResultValue($name)``` - значение результата работы адаптера по ключу ```$name```;

Пример:

```php
/**
 * @label component
 * @test
 */
public function modifierForSomeTemplate() {
    $rm = new \WS\BUnit\Invokers\ResultModifierInvoker("project:test.with.class", "list");
    $rm->setArResult(array("id" => 10));
    $rm->execute();
    $this->getAssert()->equal($rm->getArResultValue("id"), 10, "Параметры не равны");
}
```

#### Тестирование обработки события

Класс ```WS\BUnit\Invokers\EventInvoker``` облегчает тестирование обработки событий.

Методы:

- ```__construct($module, $eventName)``` - инициализация объекта запуска события, $module - имя модуля выброса события, $eventName - название события;
- ```setExecuteParams($params)``` - установка параметров события в виде массива, будут переданы в параметры события;
- ```execute()``` - выброс события;
- ```countOfHandlers()``` - получение количества обработчиков события;
- ```getEvent()``` - получение объекта события;

Пример:

```php
// ...

/**
 * @test
 */
public function handlersOfEventExist() {
    $eventInvoker = new \WS\BUnit\Invokers\EventInvoker("main", "OnPageStart");
    $eventInvoker->setExecuteParams(array(
        "IBLOCK_ID" => 12
    ));
    $eventInvoker->execute();

    $this->getAssert()->asTrue($eventInvoker->countOfHandlers() > 1);
}
```
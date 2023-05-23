<?php

namespace Habr\Renat\UnitTests\Blog\Commands;

use PHPUnit\Framework\TestCase;
use Habr\Renat\Blog\Commands\Arguments;
use Habr\Renat\Blog\Exceptions\ArgumentsException;

class ArgumentsTest extends TestCase {

    // Провайдер данных
    public function argumentsProvider(): iterable {
        return [
            ['some_string', 'some_string'], // Тестовый набор
// Первое значение будет передано
// в тест первым аргументом,
// второе значение будет передано
// в тест вторым аргументом
            [' some_string', 'some_string'], // Тестовый набор №2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

// Связываем тест с провайдером данных с помощью аннотации @dataProvider
// У теста два агрумента
// В одном тестовом наборе из провайдера данных два значения
    /**
     * @dataProvider argumentsProvider
     */
    public function testItReturnsArgumentsValueByName(
            $inputValue,
            $expectedValue
    ): void {
        $arguments = new Arguments(['some_key' => $inputValue]);

        $value = $arguments->get('some_key');

        $this->assertEquals($expectedValue, $value);
    }

    public function testCreateClassAndReturnsArgumentsValueByName(): void {
        $arguments = Arguments::fromArgv(['some_key' => 'some_key=some_value']);

        $value = $arguments->get('some_key');

        $this->assertEquals('some_value', $value);
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void {
        $arguments = new Arguments([]);

        $this->expectException(ArgumentsException::class);

        $this->expectExceptionMessage('No such argument: some_key');

        $arguments->get('some_key');
    }

}

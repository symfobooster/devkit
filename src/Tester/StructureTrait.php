<?php

namespace Symfobooster\Devkit\Tester;

trait StructureTrait
{
    protected function checkStructure(array $fields, array $array): void
    {
        foreach ($fields as $field => $type) {
            switch ($type) {
                case 'integer':
                case 'int':
                    $this->assertIsNumeric($array[$field]);
                    break;
                case 'string':
                    $this->assertIsString($array[$field]);
                    break;
                case 'array':
                    $this->assertIsArray($array[$field]);
                    break;
                case 'bool':
                    $this->assertIsBool($array[$field]);
                    break;
                case 'uuid':
                    $this->assertIsString($array[$field]);
                    $this->assertTrue(
                        (bool)preg_match(
                            '|^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$|',
                            $array[$field]
                        )
                    );
                    break;
                case 'null':
                    $this->assertNull($array[$field]);
                    break;
                case 'date':
                    $this->assertIsString($array[$field]);
                    $pattern = '|(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\+(\d{2}):(\d{2})|';
                    $this->assertMatchesRegularExpression($pattern, $array[$field]);
                    break;
            }
        }
        $this->assertCount(count($fields), $array);
    }

    protected function checkStructureInList(array $fields, array $array): void
    {
        foreach ($array as $item) {
            $this->checkStructure($fields, $item);
        }
    }

    protected function checkValues(array $values, array $model): void
    {
        $this->assertEquals(count($values), count($model));
        foreach ($values as $key => $value) {
            $this->assertEquals($value, $model[$key]);
        }
    }
}

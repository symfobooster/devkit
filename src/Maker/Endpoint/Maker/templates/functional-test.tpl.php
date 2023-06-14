<?php
/** @var FunctionalTestMaker $maker */
/** @var Field[] $fields */
/** @var Input $input */
/** @var Manifest $manifest */

use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTestMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Field;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Input;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

echo "<?php\n";
?>

namespace <?= $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfobooster\Devkit\Tester\ClientTrait;

class <?= $class_name ?> extends WebTestCase
{
use ClientTrait;

public function testSuccess(): void
{
$response = $this->send<?= ucfirst($manifest->method) ?>('/<?= $manifest->domain ?>/<?= $manifest->endpoint ?>', $this->getData());
$this->checkSuccess();
}

/**
* @dataProvider getNotValidData
*/
public function testNotValid(string $field, $value): void
{
$data = $this->getData();
$data[$field] = $value;

$response = $this->send<?= ucfirst($manifest->method) ?>('/<?= $manifest->domain ?>/<?= $manifest->endpoint ?>', $data);
$this->checkNotValid([$field]);
}


/**
* @dataProvider getNotValidData
*/
public function testRequired(string $field, $value): void
{
$data = $this->getData();
unset($data[$field]);

$response = $this->send<?= ucfirst($manifest->method) ?>('/<?= $manifest->domain ?>/<?= $manifest->endpoint ?>', $data);
$this->checkNotValid([$field]);
}

public function getData(): array
{
return [
<?php foreach ($fields as $field): ?>
    '<?= $field->name ?>' => <?= $maker->getDataExample($field) ?>,
<?php endforeach; ?>
];
}

public function getNotValidData(): array
{
return [
<?php foreach ($fields as $field): ?>
    ['<?= $field->name ?>', 'InvalidValue'],
<?php endforeach; ?>
];
}
}

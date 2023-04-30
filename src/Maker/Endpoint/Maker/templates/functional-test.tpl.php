<?php
/** @var \Zabachok\SymfoboosterDevkit\Maker\Endpoint\Maker\FunctionalTestMaker $maker */
/** @var \Zabachok\SymfoboosterDevkit\Maker\Endpoint\Manifest\Field[] $fields */
/** @var \Zabachok\SymfoboosterDevkit\Maker\Endpoint\Manifest\Input $input */
/** @var \Zabachok\SymfoboosterDevkit\Maker\Endpoint\Manifest\Manifest $manifest */
echo "<?php\n";
?>

namespace <?= $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zabachok\SymfoboosterDevkit\Tester\ClientTrait;

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

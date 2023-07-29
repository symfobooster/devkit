<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\PhpNamespace;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\DataExampleTrait;
use Symfobooster\Devkit\Maker\Endpoint\Maker\FunctionalTest\FunctionMakerInterface;
use Symfobooster\Devkit\Maker\Endpoint\ManifestLoader;
use Symfobooster\Devkit\Maker\FileStorage;
use Symfobooster\Devkit\Maker\Storage;
use Symfobooster\Devkit\Tester\ClientTrait;
use Symfobooster\Devkit\Tester\DataBaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTestMaker extends AbstractMaker
{
    use DataExampleTrait;

    /** @var FunctionMakerInterface[] */
    private array $functionMakers;

    public function __construct(
        ManifestLoader $manifestLoader,
        Storage $storage,
        FileStorage $fileStorage,
        array $functionMakers
    ) {
        parent::__construct($manifestLoader, $storage, $fileStorage);
        $this->functionMakers = $functionMakers;
    }

    private Dumper $dumper;
    private string $url;

    public function make(): void
    {
        $this->dumper = new Dumper();
        $this->url = '/' . strtolower($this->manifest->controller) . '/' . strtolower($this->manifest->action);
        $generator = new ClassMaker(
            'Tests\\Functional\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst(
                $this->manifest->action
            ) . 'Test',
            WebTestCase::class
        );
        $class = $generator->getClass();
        $namespace = $generator->getNamespace();
        $namespace->addUse(WebTestCase::class);

        $namespace->addUse(ClientTrait::class);
        $class->addTrait(ClientTrait::class);
        $namespace->addUse(DataBaseTrait::class);
        $class->addTrait(DataBaseTrait::class);

        foreach ($this->functionMakers as $maker) {
            $maker = new $maker();
            if ($maker->isNeedToRun($this->manifest)) {
                $maker->run($this->manifest, $namespace, $class);
            }
        }
        $this->addGetRequestMethod($namespace, $class);
        $this->addSetUp($class);
        $this->addTearDown($class);

        $this->fileStorage->addFile('/' . lcfirst($generator->getPath()), $generator->getContent());
    }

    private function addGetRequestMethod(PhpNamespace $namespace, ClassType $class): void
    {
        $method = $class->addMethod('getRequest')
            ->setPrivate()
            ->setReturnType('array');

        $result = [];
        if (!empty($this->manifest->input) || !empty($this->manifest->input->fields)) {
            foreach ($this->manifest->input->fields as $field) {
                $result[$field->name] = $this->getDataExample($field);
            }
        }

        $method->setBody('return ' . $this->dumper->dump($result) . ';');
    }

    private function addSetUp(ClassType $class): void
    {
        $class->addProperty('repository')->setType('ChangeRepository');

        $method = $class->addMethod('setUp')
            ->setReturnType('void');
        $method->addBody('$this->createBrowser();');
        $method->addBody('$this->loadFixtures([]);');
        $method->addBody('$this->repository = $this->getEntityManager()->getRepository(ChangeEntity::class);');
    }

    private function addTearDown(ClassType $class): void
    {
        $method = $class->addMethod('tearDown')
            ->setReturnType('void');
        $method->addBody('$this->purgeDatabase();');
        $method->addBody('parent::tearDown();');
    }
}

<?php

namespace Symfobooster\Devkit\Maker\Endpoint\Maker;

use Nette\PhpGenerator\Method;
use Symfobooster\Base\Input\InputInterface;
use Symfobooster\Base\Output\Success;
use Symfobooster\Base\Service\ServiceInterface;
use Symfobooster\Devkit\Maker\AbstractMaker;
use Symfobooster\Devkit\Maker\Endpoint\ClassMaker;
use Symfobooster\Devkit\Maker\Endpoint\Manifest\Manifest;

class ServiceMaker extends AbstractMaker
{
    public function make(): void
    {
        $generator = new ClassMaker(
            'App\\' . ucfirst($this->manifest->controller) . '\\' . ucfirst(
                $this->manifest->action
            ) . '\\Service'
        );
        $namespace = $generator->getNamespace();
        $namespace->addUse(ServiceInterface::class);
        $namespace->addUse(InputInterface::class);
        $namespace->addUse($this->storage->get('outputClass'));

        $generator->getClass()->addImplement(ServiceInterface::class);

        switch ($this->manifest->type) {
            case Manifest::TYPE_LIST:
                $this->makeListService($generator);
                break;
            case Manifest::TYPE_VIEW:
                $this->makeViewService($generator);
                break;
            case Manifest::TYPE_UPDATE:
                $this->makeUpdateService($generator);
                break;
            case Manifest::TYPE_DELETE:
                $this->makeDeleteService($generator);
                break;
            default:
                $this->makeDefaultService($generator);
                break;
        }

        $this->storage->set('serviceClass', $namespace->getName() . '\\' . $generator->getClass()->getName());
        $this->fileStorage->addFile('/src' . $generator->getPath(), $generator->getContent());
    }

    private function makeListService(ClassMaker $generator): void
    {
        $this->addBehave($generator);
    }

    private function makeViewService(ClassMaker $generator): void
    {
        $behave = $this->addFindById($generator);
        $entityVar = $this->getVariableByClass($this->manifest->service->entity);
        $repositoryVar = $this->getVariableByClass($this->manifest->service->repository);
    }

    private function addFindById(ClassMaker $generator): Method
    {
        $this->addRepository($generator);
        $repositoryVar = $this->getVariableByClass($this->manifest->service->repository);

        $behave = $this->addBehave($generator);
        $generator->getNamespace()->addUse(NotFound::class);
        $generator->getNamespace()->addUse($this->manifest->service->entity);

        $entityVar = $this->getVariableByClass($this->manifest->service->entity);
        $entityClass = $this->getNameByClass($this->manifest->service->entity);

        $body = <<<EOT
/** @var $entityClass \$$entityVar */
\$$entityVar = \$this->{$repositoryVar}->findById(\$input->id);
if (is_null(\$$entityVar)) {
    return new NotFound();
}
EOT;
        $behave->addBody($body);

        return $behave;
    }

    private function addRepository(ClassMaker $generator): void
    {
        if (empty($this->manifest->service->repository)) {
            return;
        }
        $generator->getNamespace()->addUse($this->manifest->service->repository);

        $method = $generator->getClass()->addMethod('__construct');
        $method->addPromotedParameter($this->getVariableByClass($this->manifest->service->repository))
            ->setType($this->manifest->service->repository)
            ->setPrivate();
    }

    private function addBehave(ClassMaker $generator): Method
    {
        $method = $generator->getClass()->addMethod('behave');
        $method->setReturnType('mixed');
        $method->addParameter('input')->setType(InputInterface::class);

        return $method;
    }

    private function makeUpdateService(ClassMaker $generator)
    {
        $behave = $this->addFindById($generator);
        $entityVar = $this->getVariableByClass($this->manifest->service->entity);
        $repositoryVar = $this->getVariableByClass($this->manifest->service->repository);
        $generator->getNamespace()->addUse(Success::class);

        $behave->addBody("\n\n");
        $behave->addBody('$this->' . $repositoryVar . '->persist($' . $entityVar . ');');
        $behave->addBody('');
        $behave->addBody('return new Success();');
    }

    private function makeDeleteService(ClassMaker $generator): void
    {
        $behave = $this->addFindById($generator);
        $entityVar = $this->getVariableByClass($this->manifest->service->entity);
        $repositoryVar = $this->getVariableByClass($this->manifest->service->repository);
        $generator->getNamespace()->addUse(Success::class);

        $behave->addBody('$this->' . $repositoryVar . '->delete($' . $entityVar . ');');
        $behave->addBody('');
        $behave->addBody('return new Success();');
    }

    private function makeDefaultService(ClassMaker $generator): void
    {
        $this->addBehave($generator);
    }
}

<?php


namespace yii\di;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use yii\di\exceptions\NotFoundException;

/**
 * This class implements a composite container for use with containers that support the delegate lookup feature.
 * The goal of the implementation is simplicity.
 * Class CompositeContainer
 * @package yii\di
 */
class CompositeContainer implements ContainerInterface
{
    /**
     * We use a simple array since order matters
     * @var ContainerInterface[] The list of containers
     */
    private $containers = [];

    /** @inheritdoc */
    public function get($id)
    {
        foreach ($this->containers as $container) {
            try {
                return $container->get($id);
            } catch (NotFoundExceptionInterface $e) {
                continue;
            }
        }
        throw new NotFoundException();
    }

    /** @inheritdoc */
    public function has($id)
    {
        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Attaches a container to the composite container.
     * @param ContainerInterface $container
     */
    public function attach(ContainerInterface $container)
    {
        array_unshift($this->containers, $container);
    }

    /**
     * Removes a container from the list of containers.
     * @param ContainerInterface $container
     */
    public function detach(ContainerInterface $container)
    {
        foreach ($this->containers as $i => $c) {
            if ($container === $c) {
                unset($this->containers[$i]);
            }
        }
        $this->containers = array_values($this->containers);
    }
}
<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\ConfigBundle\Infrastructure\Doctrine;

use Black\Bundle\ConfigBundle\Domain\Model\ConfigManagerInterface;
use Black\Bundle\CommonBundle\Doctrine\ManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ConfigManager
 *
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ConfigManager implements ConfigManagerInterface, ManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param ObjectManager $dm
     * @param string        $class
     */
    public function __construct(ObjectManager $dm, $class)
    {
        $this->manager    = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata    = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * @return ObjectManager|mixed
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return ObjectRepository|\Doctrine\Common\Persistence\ObjectRepository|mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $model
     *
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    public function persist($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $this->manager->persist($model);
    }

    /**
     * @return mixed|void
     */
    public function flush()
    {
        $this->manager->flush();
    }

    /**
     * @param $model
     *
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    public function remove($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $this->manager->remove($model);
    }

    /**
     * @return mixed
     */
    public function createInstance()
    {
        $class  = $this->getClass();
        $model = new $class;

        return $model;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function find($value)
    {
        return $this->repository->findOneBy(['id' => $value]);
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function findByName($name)
    {
        return $this->repository->findOneBy(['name' => $name]);
    }
}

<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\ConfigBundle\Doctrine;

use Black\Bundle\ConfigBundle\Model\ConfigInterface;
use Black\Bundle\ConfigBundle\Model\ConfigManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ConfigManager
 *
 * @package Black\Bundle\ConfigBundle\Doctrine
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ConfigManager implements ConfigManagerInterface
{
    /**
     * @var array
     */
    protected $properties = array();

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
        $this->manager     = $dm;
        $this->repository  = $dm->getRepository($class);

        $metadata          = $dm->getClassMetadata($class);
        $this->class       = $metadata->name;
        $this->loadProperties();
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
     * @param object $model
     *
     * @throws \InvalidArgumentException
     */
    public function persist($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $this->getManager()->persist($model);
    }

    /**
     *
     */
    public function flush()
    {
        $this->getManager()->flush();
    }

    /**
     * @param object $model
     *
     * @throws \InvalidArgumentException
     */
    public function remove($model)
    {
        if (!$model instanceof $this->class) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->getManager()->remove($model);
    }

    /**
     * @param mixed $model
     */
    public function persistAndFlush($model)
    {
        $this->persist($model);
        $this->flush();
    }

    /**
     * @param mixed $model
     */
    public function removeAndFlush($model)
    {
        $this->getManager()->remove($model);
        $this->getManager()->flush();
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
     * @param ConfigInterface $property
     */
    public function updateProperty(ConfigInterface $property)
    {
        $this->getManager()->merge($property);
        $this->getManager()->flush();
    }

    /**
     * @param array $criteria
     *
     * @return array
     */
    public function findPropertiesBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * @param integer $id
     * 
     * @return \Black\Bundle\ConfigBundle\Model\ConfigInterface|object
     */
    public function findPropertyById($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param string  $name
     * 
     * @return \Black\Bundle\ConfigBundle\Model\ConfigInterface|object
     */
    public function findPropertyByName($name)
    {
        return $this->getRepository()->findOneBy(array('name' => $name));
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Load properties
     */
    public function loadProperties()
    {
        if (array() !== $this->properties) {
            return;
        }

        $this->properties = $this->getRepository()->findAll();
    }

    /**
     *
     */
    public function reloadProperties()
    {
        $this->unload();
        $this->loadProperties();
    }

    /**
     *
     */
    public function unload()
    {
        $this->properties = array();
    }
}

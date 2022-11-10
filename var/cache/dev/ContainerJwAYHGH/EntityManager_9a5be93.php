<?php

namespace ContainerJwAYHGH;
include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectManager.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/lib/Doctrine/ORM/EntityManagerInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/lib/Doctrine/ORM/EntityManager.php';

class EntityManager_9a5be93 extends \Doctrine\ORM\EntityManager implements \ProxyManager\Proxy\VirtualProxyInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager|null wrapped object, if the proxy is initialized
     */
    private $valueHolderf298d = null;

    /**
     * @var \Closure|null initializer responsible for generating the wrapped object
     */
    private $initializer0e2cc = null;

    /**
     * @var bool[] map of public properties of the parent class
     */
    private static $publicPropertiesbeb08 = [
        
    ];

    public function getConnection()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getConnection', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getConnection();
    }

    public function getMetadataFactory()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getMetadataFactory', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getMetadataFactory();
    }

    public function getExpressionBuilder()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getExpressionBuilder', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getExpressionBuilder();
    }

    public function beginTransaction()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'beginTransaction', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->beginTransaction();
    }

    public function getCache()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getCache', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getCache();
    }

    public function transactional($func)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'transactional', array('func' => $func), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->transactional($func);
    }

    public function wrapInTransaction(callable $func)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'wrapInTransaction', array('func' => $func), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->wrapInTransaction($func);
    }

    public function commit()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'commit', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->commit();
    }

    public function rollback()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'rollback', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->rollback();
    }

    public function getClassMetadata($className)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getClassMetadata', array('className' => $className), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getClassMetadata($className);
    }

    public function createQuery($dql = '')
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'createQuery', array('dql' => $dql), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'createNamedQuery', array('name' => $name), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->createNamedQuery($name);
    }

    public function createNativeQuery($sql, \Doctrine\ORM\Query\ResultSetMapping $rsm)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'createNativeQuery', array('sql' => $sql, 'rsm' => $rsm), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'createNamedNativeQuery', array('name' => $name), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->createNamedNativeQuery($name);
    }

    public function createQueryBuilder()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'createQueryBuilder', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->createQueryBuilder();
    }

    public function flush($entity = null)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'flush', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->flush($entity);
    }

    public function find($className, $id, $lockMode = null, $lockVersion = null)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'find', array('className' => $className, 'id' => $id, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->find($className, $id, $lockMode, $lockVersion);
    }

    public function getReference($entityName, $id)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getReference', array('entityName' => $entityName, 'id' => $id), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getPartialReference', array('entityName' => $entityName, 'identifier' => $identifier), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getPartialReference($entityName, $identifier);
    }

    public function clear($entityName = null)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'clear', array('entityName' => $entityName), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->clear($entityName);
    }

    public function close()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'close', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->close();
    }

    public function persist($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'persist', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->persist($entity);
    }

    public function remove($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'remove', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->remove($entity);
    }

    public function refresh($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'refresh', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->refresh($entity);
    }

    public function detach($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'detach', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->detach($entity);
    }

    public function merge($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'merge', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->merge($entity);
    }

    public function copy($entity, $deep = false)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'copy', array('entity' => $entity, 'deep' => $deep), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'lock', array('entity' => $entity, 'lockMode' => $lockMode, 'lockVersion' => $lockVersion), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->lock($entity, $lockMode, $lockVersion);
    }

    public function getRepository($entityName)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getRepository', array('entityName' => $entityName), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getRepository($entityName);
    }

    public function contains($entity)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'contains', array('entity' => $entity), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->contains($entity);
    }

    public function getEventManager()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getEventManager', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getEventManager();
    }

    public function getConfiguration()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getConfiguration', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getConfiguration();
    }

    public function isOpen()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'isOpen', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->isOpen();
    }

    public function getUnitOfWork()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getUnitOfWork', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getHydrator', array('hydrationMode' => $hydrationMode), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'newHydrator', array('hydrationMode' => $hydrationMode), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getProxyFactory', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getProxyFactory();
    }

    public function initializeObject($obj)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'initializeObject', array('obj' => $obj), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->initializeObject($obj);
    }

    public function getFilters()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'getFilters', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->getFilters();
    }

    public function isFiltersStateClean()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'isFiltersStateClean', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->isFiltersStateClean();
    }

    public function hasFilters()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'hasFilters', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return $this->valueHolderf298d->hasFilters();
    }

    /**
     * Constructor for lazy initialization
     *
     * @param \Closure|null $initializer
     */
    public static function staticProxyConstructor($initializer)
    {
        static $reflection;

        $reflection = $reflection ?? new \ReflectionClass(__CLASS__);
        $instance   = $reflection->newInstanceWithoutConstructor();

        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $instance, 'Doctrine\\ORM\\EntityManager')->__invoke($instance);

        $instance->initializer0e2cc = $initializer;

        return $instance;
    }

    public function __construct(\Doctrine\DBAL\Connection $conn, \Doctrine\ORM\Configuration $config)
    {
        static $reflection;

        if (! $this->valueHolderf298d) {
            $reflection = $reflection ?? new \ReflectionClass('Doctrine\\ORM\\EntityManager');
            $this->valueHolderf298d = $reflection->newInstanceWithoutConstructor();
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);

        }

        $this->valueHolderf298d->__construct($conn, $config);
    }

    public function & __get($name)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__get', ['name' => $name], $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        if (isset(self::$publicPropertiesbeb08[$name])) {
            return $this->valueHolderf298d->$name;
        }

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf298d;

            $backtrace = debug_backtrace(false, 1);
            trigger_error(
                sprintf(
                    'Undefined property: %s::$%s in %s on line %s',
                    $realInstanceReflection->getName(),
                    $name,
                    $backtrace[0]['file'],
                    $backtrace[0]['line']
                ),
                \E_USER_NOTICE
            );
            return $targetObject->$name;
        }

        $targetObject = $this->valueHolderf298d;
        $accessor = function & () use ($targetObject, $name) {
            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __set($name, $value)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__set', array('name' => $name, 'value' => $value), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf298d;

            $targetObject->$name = $value;

            return $targetObject->$name;
        }

        $targetObject = $this->valueHolderf298d;
        $accessor = function & () use ($targetObject, $name, $value) {
            $targetObject->$name = $value;

            return $targetObject->$name;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = & $accessor();

        return $returnValue;
    }

    public function __isset($name)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__isset', array('name' => $name), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf298d;

            return isset($targetObject->$name);
        }

        $targetObject = $this->valueHolderf298d;
        $accessor = function () use ($targetObject, $name) {
            return isset($targetObject->$name);
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $returnValue = $accessor();

        return $returnValue;
    }

    public function __unset($name)
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__unset', array('name' => $name), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        $realInstanceReflection = new \ReflectionClass('Doctrine\\ORM\\EntityManager');

        if (! $realInstanceReflection->hasProperty($name)) {
            $targetObject = $this->valueHolderf298d;

            unset($targetObject->$name);

            return;
        }

        $targetObject = $this->valueHolderf298d;
        $accessor = function () use ($targetObject, $name) {
            unset($targetObject->$name);

            return;
        };
        $backtrace = debug_backtrace(true, 2);
        $scopeObject = isset($backtrace[1]['object']) ? $backtrace[1]['object'] : new \ProxyManager\Stub\EmptyClassStub();
        $accessor = $accessor->bindTo($scopeObject, get_class($scopeObject));
        $accessor();
    }

    public function __clone()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__clone', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        $this->valueHolderf298d = clone $this->valueHolderf298d;
    }

    public function __sleep()
    {
        $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, '__sleep', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;

        return array('valueHolderf298d');
    }

    public function __wakeup()
    {
        \Closure::bind(function (\Doctrine\ORM\EntityManager $instance) {
            unset($instance->config, $instance->conn, $instance->metadataFactory, $instance->unitOfWork, $instance->eventManager, $instance->proxyFactory, $instance->repositoryFactory, $instance->expressionBuilder, $instance->closed, $instance->filterCollection, $instance->cache);
        }, $this, 'Doctrine\\ORM\\EntityManager')->__invoke($this);
    }

    public function setProxyInitializer(\Closure $initializer = null) : void
    {
        $this->initializer0e2cc = $initializer;
    }

    public function getProxyInitializer() : ?\Closure
    {
        return $this->initializer0e2cc;
    }

    public function initializeProxy() : bool
    {
        return $this->initializer0e2cc && ($this->initializer0e2cc->__invoke($valueHolderf298d, $this, 'initializeProxy', array(), $this->initializer0e2cc) || 1) && $this->valueHolderf298d = $valueHolderf298d;
    }

    public function isProxyInitialized() : bool
    {
        return null !== $this->valueHolderf298d;
    }

    public function getWrappedValueHolderValue()
    {
        return $this->valueHolderf298d;
    }
}

if (!\class_exists('EntityManager_9a5be93', false)) {
    \class_alias(__NAMESPACE__.'\\EntityManager_9a5be93', 'EntityManager_9a5be93', false);
}

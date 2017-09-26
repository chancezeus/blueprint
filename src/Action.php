<?php

namespace Dingo\Blueprint;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionMethod;
use RuntimeException;

class Action extends Section
{
    /**
     * Action reflector instance.
     *
     * @var \ReflectionMethod
     */
    protected $reflector;

    /**
     * Annotations belonging to the action.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $annotations;

    /**
     * Parent resource of the action.
     *
     * @var \Dingo\Blueprint\RestResource
     */
    protected $resource;

    /**
     * Create a new action instance.
     *
     * @param \ReflectionMethod $reflector
     * @param \Illuminate\Support\Collection $annotations
     */
    public function __construct(ReflectionMethod $reflector, Collection $annotations)
    {
        $this->reflector = $reflector;
        $this->annotations = $annotations;
    }

    /**
     * Get the actions definition.
     *
     * @return string
     */
    public function getDefinition()
    {
        $definition = $this->getMethod();

        if ($identifier = $this->getIdentifier()) {
            if ($uri = $this->getUri()) {
                $definition = $definition . ' ' . $uri;
            }

            $definition = $identifier . ' [' . $definition . ']';
        }

        $level = $this->resource->getGroupIdentifier() ? '### ' : '## ';

        return $level . $definition;
    }

    /**
     * Get the actions version annotation.
     *
     * @return \Dingo\Blueprint\Annotation\Versions|null
     */
    public function getVersions()
    {
        if ($annotation = $this->getAnnotationByType('Versions')) {
            return $annotation;
        }

        return null;
    }

    /**
     * Get the actions response annotation.
     *
     * @return \Dingo\Blueprint\Annotation\Response|null
     */
    public function getResponse()
    {
        if ($annotation = $this->getAnnotationByType('Response')) {
            return $annotation;
        }

        return null;
    }

    /**
     * Get the actions request annotation.
     *
     * @return \Dingo\Blueprint\Annotation\Request|null
     */
    public function getRequest()
    {
        if ($annotation = $this->getAnnotationByType('Request')) {
            return $annotation;
        }

        return null;
    }

    /**
     * Get the actions transaction annotation.
     *
     * @return \Dingo\Blueprint\Annotation\Transaction|null
     */
    public function getTransaction()
    {
        if ($annotation = $this->getAnnotationByType('Transaction')) {
            return $annotation;
        }

        return null;
    }

    /**
     * Get the actions identifier.
     *
     * @return string|null
     */
    public function getIdentifier()
    {
        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($this->reflector);

        return $docBlock->getSummary();
    }

    /**
     * Get the actions description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($this->reflector);

        return $docBlock->getDescription();
    }

    /**
     * Get the actions URI.
     *
     * @return string
     */
    public function getUri()
    {
        if (($annotation = $this->getAnnotationByType('Method\Method')) && isset($annotation->uri)) {
            $uri = trim($annotation->uri, '/');
        } else {
            return null;
        }

        if (!starts_with($uri, '{?')) {
            $uri = '/' . $uri;
        }

        return '/' . trim(trim($this->resource->getUri(), '/') . rtrim($uri, '/'), '/');
    }

    /**
     * Get the actions method.
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getMethod()
    {
        if ($annotation = $this->getAnnotationByType('Method\Method')) {
            return strtoupper(substr(get_class($annotation), strrpos(get_class($annotation), '\\') + 1));
        }

        throw new RuntimeException('No HTTP method given, invalid API blueprint.');
    }

    /**
     * Set the parent resource on the action.
     *
     * @param \Dingo\Blueprint\RestResource $resource
     *
     * @return void
     */
    public function setResource(RestResource $resource)
    {
        $this->resource = $resource;
    }
}

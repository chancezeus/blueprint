<?php

namespace Dingo\Blueprint;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;

class RestResource extends Section
{
    /**
     * Resource identifier.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Resource reflection instance.
     *
     * @var \ReflectionClass
     */
    protected $reflector;

    /**
     * Collection of annotations belonging to a resource.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $annotations;

    /**
     * Collection of actions belonging to a resource.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $actions;

    /**
     * Collection of default request headers belonging to a resource.
     *
     * @var array
     */
    protected $requestHeaders = [];

    /**
     * Collection of default response headers belonging to a resource.
     *
     * @var array
     */
    protected $responseHeaders = [];

    /**
     * Create a new resource instance.
     *
     * @param string $identifier
     * @param \ReflectionClass $reflector
     * @param \Illuminate\Support\Collection $annotations
     * @param \Illuminate\Support\Collection $actions
     */
    public function __construct($identifier, ReflectionClass $reflector, Collection $annotations, Collection $actions)
    {
        $this->identifier = $identifier;
        $this->reflector = $reflector;
        $this->annotations = $annotations;
        $this->actions = $actions;

        $this->setResourceOnActions();
    }

    /**
     * Set the resource on each of the actions.
     *
     * @return void
     */
    protected function setResourceOnActions()
    {
        $this->actions->each(function (Action $action) {
            $action->setResource($this);
        });
    }

    /**
     * Get the resource definition.
     *
     * @return string
     */
    public function getDefinition()
    {
        $definition = $this->getUri();

        if ($method = $this->getMethod()) {
            $definition = $method . ' ' . $definition;
        }

        $level = $this->getGroupIdentifier() ? '## ' : '# ';

        return $level . $this->getIdentifier() . ($definition == '/' ? '' : ' [' . $definition . ']');
    }

    /**
     * Get the resource group annotation.
     *
     * @return \Dingo\Blueprint\Annotation\Group
     */
    public function getGroupAnnotation()
    {
        return $this->getAnnotationByType('Group');
    }

    /**
     * Get the resource group.
     *
     * @return string
     */
    public function getGroupIdentifier()
    {
        if (($annotation = $this->getGroupAnnotation()) && isset($annotation->identifier)) {
            return $annotation->identifier;
        }

        return null;
    }

    /**
     * Get the actions belonging to the resource.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * get the resource URI.
     *
     * @return string
     */
    public function getUri()
    {
        if (($annotation = $this->getAnnotationByType('Resource')) && isset($annotation->uri)) {
            return '/' . trim($annotation->uri, '/');
        }

        return '/';
    }

    /**
     * Get the resource method.
     *
     * @return string|null
     */
    public function getMethod()
    {
        if (($annotation = $this->getAnnotationByType('Resource')) && isset($annotation->method)) {
            return $annotation->method;
        }

        return null;
    }

    /**
     * Get the resource description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        $factory = DocBlockFactory::createInstance();
        $docBlock = $factory->create($this->reflector);
        $text = $docBlock->getSummary() . $docBlock->getDescription();
        return $text;
    }

    /**
     * Get the resource identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        if (($annotation = $this->getAnnotationByType('Resource')) && isset($annotation->identifier)) {
            return $annotation->identifier;
        }

        return $this->identifier;
    }

    /**
     * Check if resource has default request headers set.
     *
     * @return bool
     */
    public function hasRequestHeaders()
    {
        return count($this->getRequestHeaders()) > 0;
    }

    /**
     * Get the resource default request headers.
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        if (($annotation = $this->getAnnotationByType('Resource')) && isset($annotation->requestHeaders)) {
            return $annotation->requestHeaders;
        }

        return $this->requestHeaders;
    }

    /**
     * Check if resource has default response headers set.
     *
     * @return bool
     */
    public function hasResponseHeaders()
    {
        return count($this->getResponseHeaders()) > 0;
    }

    /**
     * Get the resource default response headers.
     *
     * @return array
     */
    public function getResponseHeaders()
    {
        if (($annotation = $this->getAnnotationByType('Resource')) && isset($annotation->responseHeaders)) {
            return $annotation->responseHeaders;
        }

        return $this->responseHeaders;
    }
}

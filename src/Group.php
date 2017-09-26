<?php

namespace Dingo\Blueprint;

use Illuminate\Support\Collection;

class Group extends Section
{
    /**
     * Group identifier.
     *
     * @var string
     */
    public $identifier = null;

    /**
     * Collection of resources belonging to group.
     *
     * @var array
     */
    protected $resources;

    /**
     * Create a new group instance.
     *
     * @param \Dingo\Blueprint\RestResource $resource
     */
    public function __construct(RestResource $resource)
    {
        $this->resources = new Collection([$resource]);
        $this->identifier = $resource->getGroupIdentifier();
    }

    /**
     * Get the group identifier.
     *
     * @return string|null
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Add resource to the group.
     *
     * @param \Dingo\Blueprint\RestResource $resource
     */
    public function addResource(RestResource $resource)
    {
        $this->resources->contains($resource) ?: $this->resources->push($resource);
    }

    /**
     * Get the resources belonging to the group.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Get the group definition.
     *
     * @return string
     */
    public function getDefinition()
    {
        if ($this->identifier) {
            return '# Group ' . $this->identifier;
        }

        return null;
    }
}

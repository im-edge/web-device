<?php

namespace IMEdge\Web\Device\Widget\Entity;

use IMEdge\Web\Data\Model\Entity;
use IMEdge\Web\Data\Model\EntitySensor;
use IMEdge\Web\Data\Model\NetworkInterfaceConfig;
use IMEdge\Web\Data\Model\NetworkInterfaceStatus;
use RuntimeException;

class EntityTree
{
    /** @var array<int, Entity> */
    protected array $entities = [];
    /** @var array<string, Entity> */
    protected array $byClass = [];
    /** @var array<int, array<int, Entity>> */
    protected array $children = [];
    /** @var array<int, Entity> */
    protected array $roots = [];
    /** @var EntitySensor[] */
    protected array $sensors = [];
    /** @var NetworkInterfaceConfig[] */
    protected array $interfaceConfigs = [];
    /** @var NetworkInterfaceStatus[] */
    protected array $interfaceStatuses = [];

    /**
     * @param Entity[] $entities
     */
    public static function create(array $entities): EntityTree
    {
        $self = new static();
        foreach ($entities as $entity) {
            $self->addEntity($entity);
        }
        $self->buildTree();

        return $self;
    }

    /**
     * @return array<int, Entity>
     */
    public function getRootEntities(): array
    {
        return $this->roots;
    }

    /**
     * @return array<int, Entity>
     */
    public function getChildrenFor(int $entityId): array
    {
        return $this->children[$entityId] ?? [];
    }

    public function getSensorFor(int $entityId): ?EntitySensor
    {
        return $this->sensors[$entityId] ?? null;
    }

    /**
     * @param int $entityId
     * @return array{0: NetworkInterfaceConfig, 1: NetworkInterfaceStatus}|null
     */
    public function getInterfacesFor(int $entityId): ?array
    {
        if (isset($this->interfaceConfigs[$entityId]) && isset($this->interfaceStatuses[$entityId])) {
            return [
                $this->interfaceConfigs[$entityId],
                $this->interfaceStatuses[$entityId]
            ];
        }

        return null;
    }

    protected function addEntity(Entity $entity)
    {
        $id = (int) $entity->get('entity_index');
        if (isset($this->entities[$id])) {
            throw new RuntimeException('Cannot assign the same entityId twice');
        }
        $this->byClass[$entity->get('class')][$id] = &$entity;
        $this->entities[$id] = $entity;
    }

    protected function buildTree(): void
    {
        foreach ($this->entities as $idx => $entity) {
            $parentIdx = (int) $entity->get('parent_index');
            if (isset($this->entities[$parentIdx])) {
                $this->children[$parentIdx][$idx] = $entity;
            } else {
                $this->roots[$idx] = $entity;
            }
        }
    }

    /**
     * @param array<int, EntitySensor> $entitySensors
     */
    public function setSensors(array $entitySensors): void
    {
        $this->sensors = $entitySensors;
    }

    /**
     * @param array<int, NetworkInterfaceConfig> $interfaceConfigs
     * @param array<int, NetworkInterfaceStatus> $interfaceStatuses
     */
    public function setInterfaces(array $interfaceConfigs, array $interfaceStatuses): void
    {
        $this->interfaceConfigs = $interfaceConfigs;
        $this->interfaceStatuses = $interfaceStatuses;
    }
}

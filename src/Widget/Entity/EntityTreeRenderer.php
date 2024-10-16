<?php

namespace IMEdge\Web\Device\Widget\Entity;

use gipfl\Translation\TranslationHelper;
use IMEdge\Web\Data\Model\Entity;
use IMEdge\Web\Data\Model\EntitySensor;
use IMEdge\Web\Grapher\GraphRendering\ImedgeGraphPreview;
use Icinga\Module\Imedge\Graphing\RrdImageLoader; // TODO: move!
use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;
use ipl\Html\HtmlDocument;
use ipl\Html\HtmlElement;
use Ramsey\Uuid\Uuid;

class EntityTreeRenderer extends BaseHtmlElement
{
    use TranslationHelper;

    protected $tag = 'div';
    protected $defaultAttributes = [
        'class' => 'entity-tree'
    ];
    protected EntityTree $tree;
    protected RrdImageLoader $imgLoader;
    protected string $graphStart = 'end-8hours';

    public function __construct(EntityTree $tree, RrdImageLoader $imgLoader)
    {
        $this->tree = $tree;
        $this->imgLoader = $imgLoader;
    }

    protected function assemble()
    {
        // [entity_index] => 1
        // [name] => Chassis
        // [alias] =>
        // [description] => Cisco ASR1001-HX Chassis
        // [model_name] => ASR1001-HX
        // [asset_id] =>
        // [parent_index] => 0
        // [class] => chassis
        // [relative_position] => -1
        // [revision_hardware] => V03
        // [revision_firmware] =>
        // [revision_software] =>
        // [manufacturer_name] => Cisco Systems Inc
        // [serial_number] => TTM241200W9
        // [field_replaceable_unit] => y
        $tree = $this->tree;
        $this->addEntities($tree->getRootEntities(), $this);
    }

    /**
     * @param array<int, Entity> $entities
     */
    protected function addEntities(array $entities, HtmlDocument $parent): void
    {
        foreach ($entities as $id => $entity) {
            $parent->add($this->entityElement($entity));
        }
    }

    protected function entityElement(Entity $entity): HtmlDocument
    {
        $tag = $this->createEntityElement($entity);
        $entityIdx = $entity->get('entity_index');

        if ($interfaces = $this->tree->getInterfacesFor($entityIdx)) {
            [$ifConfigs, $ifStatuses] = $interfaces;
            foreach ($ifConfigs as $ifIndex => $ifConfig) {
                $img = $this->imgLoader->getDeviceImg(
                    Uuid::fromBytes($entity->get('device_uuid')),
                    'if_traffic',
                    (string)$ifIndex,
                    'if_traffic_simple'
                );
                if ($ifConfig->get('status_admin') === 'down') {
                    $tag->addAttributes(['class' => 'adminDown']);
                } elseif ($ifStatuses[$ifIndex]->get('status_operational') === 'down') {
                    $tag->addAttributes(['class' => 'down']);
                }
                if ($img) {
                    $imgContainer = new ImedgeGraphPreview($img);
                    $imgContainer->addAttributes(['style' => 'width: 100%; height: 10em;']);
                    $img->graph->timeRange->setStart($this->graphStart);
                    //$img->graph->layout->setOnlyGraph();
                    $img->graph->layout->disableXAxis();
                    $imgContainer->add(Html::tag('strong', [
                        'style' => 'position: absolute; display: inline-block; z-index: 10; top: 10%; left: 30%; margin: auto'
                    ], $ifConfig->get('if_name')));

                    $tag->add($imgContainer);
                } else {
                    $tag->add(Html::tag('strong', $ifConfig->get('if_name')));
                }
            }
        }
        $children = $this->tree->getChildrenFor($entityIdx);
        if (! empty($children)) {
            $subElement = Html::tag('div', ['class' => 'sub-entities']);
            $sensors = [];
            foreach ($children as $idx => $child) {
                if ($child->get('class') === 'sensor') {
                    $sensors[$idx] = $child;
                }
            }
            foreach (array_keys($sensors) as $idx) {
                unset($children[$idx]);
            }
            $sensorsContainer = Html::tag('div', ['class' => 'entity-sensors']);
            $tag->add($sensorsContainer);
            $sensorEntityNames = [];
            foreach ($sensors as $idx => $sensorEntity) {
                $sensorEntityNames[$idx] = $sensorEntity->get('name');
            }
            if ($commonPrefix = self::getCommonPrefix($sensorEntityNames)) {
                foreach ($sensorEntityNames as &$name) {
                    if (strlen($name) > strlen($commonPrefix)) {
                        $name = substr($name, strlen($commonPrefix));
                    }
                }
            }

            foreach ($sensors as $idx => $sensorEntity) {
                if ($sensor = $this->tree->getSensorFor($idx)) {
                    $sensorContent = [
                        Html::tag('strong', $this->getSensorValue($sensor)),
                        ' (',
                        Html::tag(
                            'span',
                            // Strip a common prefix across all sensors, if any
                            self::stripOptionalPrefix(
                            // Sensor names are often prefixed with their outer container name.
                                self::stripOptionalPrefix(
                                    self::getNameAndOrDescription($sensorEntity),
                                    $entity->get('name')
                                ),
                                $commonPrefix
                            )
                        ),
                        ')',
                        Html::tag('br'),
                    ];
                    $img = $this->imgLoader->getDeviceImg(
                        Uuid::fromBytes($entity->get('device_uuid')),
                        'entity_sensor',
                        (string)$idx,
                        'entitySensor'
                    );
                    if ($img) {
                        $imgContainer = new ImedgeGraphPreview($img);
                        $imgContainer->addAttributes(['style' => 'width: 100%; height: 8em;']);
                        $imgContainer->add(
                            Html::tag('div', [
                                'style' => 'position: absolute; top: 1px; left: 1px; z-index: 10;'
                            ], $sensorContent)
                        );
                        $img->graph->timeRange->setStart($this->graphStart);
                        // $img->graph->layout->setOnlyGraph();
                        $img->graph->layout->disableXAxis();
                        $sensorsContainer->add($imgContainer);
                    } else {
                        $sensorsContainer->add($sensorContent);
                    }
                }
            }
            $this->addEntities($children, $subElement);
            $tag->add($subElement);
        }

        return $tag;
    }

    /**
     * @param string[] $strings
     * @return string|null
     */
    protected static function getCommonPrefix(array $strings): ?string
    {
        if (empty($strings)) {
            return null;
        }
        $strings = array_values($strings); // -> keys
        if (count($strings) === 2) {
            return $strings[0];
        }

        sort($strings);
        $first = $strings[0];
        $last = $strings[count($strings) - 1];
        $maxLength = min(strlen($first), strlen($last));
        for ($i = 0; $i < $maxLength && $first[$i] === $last[$i]; $i++) {
            // nothing, just increment $i
        }

        return substr($first, 0, $i);
    }

    protected static function stripOptionalPrefix(string $string, string $prefix): string
    {
        if (str_starts_with($string, $prefix) && $string !== $prefix) {
            return ltrim(substr($string, strlen($prefix)));
        }

        return $string;
    }

    protected function getSensorValue(EntitySensor $sensor): string
    {
        $unit = $sensor->get('sensor_units_display');
        if ($unit === null) { // These are ugly workarounds, display hint should be there
            if ($sensor->get('sensor_scale') === 'units') {
                $unit = $sensor->get('sensor_type');
            } else {
                $unit = $sensor->get('sensor_scale') . ' ' . $sensor->get('sensor_type');
            }
        }

        $adjustedValue = $sensor->get('sensor_value') / pow(10, $sensor->get('sensor_precision')) . ' ' . $unit;
        if ($sensor->get('sensor_status') !== 'ok') {
            $adjustedValue = sprintf($this->translate('Sensor is %s'), $sensor->get('sensor_status'));
        } elseif ($sensor->get('sensor_type') === 'truthvalue') {
            switch ((int) $sensor->get('sensor_value')) {
                case 1:
                    $adjustedValue = 'true';
                    break;
                case 2:
                    $adjustedValue = 'false';
                    break;
                default:
                    $adjustedValue = 'Invalid: ' . $sensor->get('sensor_value');
            }
        }

        return $adjustedValue;
    }

    protected function createEntityElement(Entity $entity): HtmlElement
    {
        $tag = Html::tag('div', ['class' => ['entity', $entity->get('class') . '-entity']]);
        if ($entity->get('class') === 'fan') {
            $tag->add(Html::tag('img', [
                'src' => 'img/inventory/components/fan.svg',
                'height' => 32,
                'width' => 32,
                'style' => 'float: left',
                'alt' => '',
            ]));
        }
        if ($entity->get('field_replaceable_unit') === 'y') {
            $tag->addAttributes(['class' => 'fru-entity']);
        }
        $tag->add(self::getNameAndOrDescription($entity));
        if ($modelInfo = self::getModelInfo($entity)) {
            $tag->add([Html::tag('br'), $this->translate('Model') . ': ' . $modelInfo]);
        }
        if ($serialInfo = self::getSerialInfo($entity)) {
            $tag->add([Html::tag('br'), [$this->translate('s/n'), ': '], $serialInfo]);
        }

        return $tag;
    }

    protected static function getNameAndOrDescription(Entity $entity): ?string
    {
        $name = $entity->get('name');
        $description = $entity->get('description');
        if (! self::isValidString($description)) {
            return $name;
        }

        if (! self::isValidString($name)) {
            return $description;
        }

        if (str_contains($description, $name)) {
            return $description;
        }

        if (str_contains($name, $description)) {
            return $name;
        }

        return sprintf('%s: %s', $name, $description);
    }

    protected static function isValidString(?string $string): bool
    {
        return $string !== null && $string !== '' && $string !== 'N/A';
    }

    protected static function getModelInfo(Entity $entity): ?string
    {
        $model = $entity->get('model_name');
        $manufacturer = $entity->get('manufacturer_name');
        if (self::isValidString($model)) {
            if (self::isValidString($manufacturer)) {
                return trim($model) . ', ' . trim($manufacturer);
            }
            return trim($model);
        }
        if (self::isValidString($manufacturer)) {
            return trim($manufacturer);
        }

        return null;
    }

    protected static function getSerialInfo(Entity $entity): ?array
    {
        $serial = $entity->get('serial_number');
        if ($serial === null || $serial === '') {
            return null;
        }

        return [Html::tag('span', ['class' => 'entity-serial-number'], $serial)];
    }
}

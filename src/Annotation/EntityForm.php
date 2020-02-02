<?php

namespace Drupal\wmcustom_entity_form\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class EntityForm extends Plugin
{
    /** @var string */
    public $entity_type;
    /** @var string */
    public $bundle;
    /** @var string */
    public $operation = 'default';

    public function getId()
    {
        if (isset($this->definition['entity_type'], $this->definition['bundle'])) {
            return implode('.', [
                $this->definition['entity_type'],
                $this->definition['bundle'],
                $this->definition['operation']
            ]);
        }

        return parent::getId();
    }
}

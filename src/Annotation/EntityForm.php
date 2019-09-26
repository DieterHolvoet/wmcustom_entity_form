<?php

namespace Drupal\wmcustom_entity_form\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class EntityForm extends Plugin
{
    /**
     * For example: node.article
     * Or more granular: node.article.delete
     */
    public $id;
}

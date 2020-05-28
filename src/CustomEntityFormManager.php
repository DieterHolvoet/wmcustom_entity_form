<?php

namespace Drupal\wmcustom_entity_form;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmcustom_entity_form\Annotation\EntityForm;

class CustomEntityFormManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'Form/Entity',
            $namespaces,
            $moduleHandler,
            EntityFormInterface::class,
            EntityForm::class
        );
        $this->alterInfo('custom_entity_form');
        $this->setCacheBackend($cacheBackend, 'custom_entity_form_plugins');
    }

    public function getFormClass(string $entityType, string $bundle, string $operation = 'default')
    {
        $keys[] = implode('.', [$entityType, $bundle, $operation]);

        // Delete forms has to be explicitly <bundle>.delete
        // So don't fallback to just <bundle> when delete operation.
        if (!in_array($operation, ['delete', 'cancel'])) {
            $keys[] = implode('.', [$entityType, $bundle, 'default']);
        }

        foreach ($keys as $key) {
            if ($this->hasDefinition($key)) {
                return $this->getDefinition($key)['class'];
            }
        }

        return null;
    }
}

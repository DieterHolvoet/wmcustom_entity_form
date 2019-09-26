<?php

namespace Drupal\wmcustom_entity_form;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmcustom_entity_form\Annotation\EntityForm;

class CustomEntityFormManager extends DefaultPluginManager
{
    protected $entityForms;

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

    public function clearCachedDefinitions()
    {
        parent::clearCachedDefinitions();
        $this->entityForms = null;
    }

    public function getFormClass($entityType, $bundle, $operation = 'default')
    {
        if (!isset($this->entityForms)) {
            $this->processDefinitions();
        }

        if (empty($this->entityForms[$entityType])) {
            return null;
        }

        $keys[] = sprintf(
            '%s.%s',
            $bundle,
            $operation
        );

        // Delete forms has to be explicitly <bundle>.delete
        // So don't fallback to just <bundle> when delete operation.
        if ($operation !== 'delete') {
            $keys[] = $bundle;
        }

        foreach ($keys as $key) {
            if (isset($this->entityForms[$entityType][$key])) {
                return $this->entityForms[$entityType][$key]['class'];
            }
        }

        return null;
    }

    protected function processDefinitions()
    {
        $forms = [];

        foreach ($this->getDefinitions() as $definition) {
            $info = explode('.', $definition['id']);
            $entityType = $info[0] ?? null;
            $bundle = $info[1] ?? null;
            $operation = $info[2] ?? null;

            if (!$entityType || !$bundle) {
                continue;
            }

            $key = $bundle; // ['article']
            if ($operation) {
                $key = sprintf('%s.%s', $key, $operation); // ['article.delete']
            }

            $forms[$entityType][$key] = $definition;
        }

        return $this->entityForms = $forms;
    }
}

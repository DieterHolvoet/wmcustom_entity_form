<?php

namespace Drupal\wmcustom_entity_form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

class WmcustomEntityFormServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        $container->setDefinition(
            'controller.entity_form',
            $container->getDefinition('custom_entity_form.controller.entityform')
        );

        $container->setDefinition(
            'entity.form_builder',
            $container->getDefinition('custom_entity_form.formbuilder.entity')
        );
    }
}

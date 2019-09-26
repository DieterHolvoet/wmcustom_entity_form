<?php

namespace Drupal\wmcustom_entity_form\Controller;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\wmcustom_entity_form\Service\EntityFormBuilder;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class HtmlEntityFormController extends \Drupal\Core\Entity\HtmlEntityFormController
{
    /** @var \Drupal\wmcustom_entity_form\Service\EntityFormBuilder */
    protected $entityFormBuilder;

    public function __construct(
        ArgumentResolverInterface $resolver,
        FormBuilderInterface $form_builder,
        EntityManagerInterface $manager,
        EntityFormBuilder $entityFormBuilder
    ) {
        parent::__construct($resolver, $form_builder, $manager);
        $this->entityFormBuilder = $entityFormBuilder;
    }

    protected function getFormObject(RouteMatchInterface $routeMatch, $formArg)
    {
        // Let parent do the heavy lifting ( getting entity and operation )
        $formObject = parent::getFormObject($routeMatch, $formArg);

        // Pass it on to the entityFormBuilder
        return $this->entityFormBuilder->getFormObject(
            $formObject->getEntity(),
            $formObject->getOperation()
        );
    }
}

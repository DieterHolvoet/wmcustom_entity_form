<?php

namespace Drupal\wmcustom_entity_form\Service;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\wmcustom_entity_form\CustomEntityFormManager;

class EntityFormBuilder extends \Drupal\Core\Entity\EntityFormBuilder
{
    /** @var \Drupal\Core\Entity\EntityManagerInterface */
    protected $entityManager;
    /** @var \Drupal\Core\StringTranslation\TranslationInterface */
    protected $stringTranslation;
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface */
    protected $moduleHandler;
    /** @var \Drupal\Core\DependencyInjection\ClassResolverInterface */
    protected $classResolver;
    /** @var \Drupal\wmcustom_entity_form\CustomEntityFormManager */
    protected $customEntityFormManager;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        FormBuilderInterface $formBuilder,
        EntityManagerInterface $entityManager,
        TranslationInterface $stringTranslation,
        ModuleHandlerInterface $moduleHandler,
        ClassResolverInterface $classResolver,
        CustomEntityFormManager $customEntityFormManager
    ) {
        parent::__construct($entityTypeManager, $formBuilder);
        $this->stringTranslation = $stringTranslation;
        $this->moduleHandler = $moduleHandler;
        $this->classResolver = $classResolver;
        $this->customEntityFormManager = $customEntityFormManager;
        $this->entityManager = $entityManager;
    }

    public function getForm(EntityInterface $entity, $operation = 'default', array $formStateAdditions = [])
    {
        $formObject = $this->getFormObject($entity, $operation);
        $formState = (new FormState())->setFormState($formStateAdditions);
        return $this->formBuilder->buildForm($formObject, $formState);
    }

    public function getFormObject(EntityInterface $entity, $operation = 'default')
    {
        $formClass = $this->customEntityFormManager->getFormClass(
            $entity->getEntityTypeId(),
            $entity->bundle(),
            $operation
        );

        $formObject = null;
        if ($formClass) {
            $formObject = $this->createFormObject($formClass, $operation);
        }

        if (!$formObject) {
            $formObject = $this->entityTypeManager->getFormObject(
                $entity->getEntityTypeId(),
                $operation
            );
        }

        $formObject->setEntity($entity);

        return $formObject;
    }

    protected function createFormObject($class, $operation)
    {
        $formObject = $this->classResolver->getInstanceFromDefinition($class);

        /** @see \Drupal\Core\Entity\EntityTypeManager::getFormObject */
        return $formObject
            ->setStringTranslation($this->stringTranslation)
            ->setModuleHandler($this->moduleHandler)
            ->setEntityTypeManager($this->entityTypeManager)
            ->setOperation($operation)
            ->setEntityManager($this->entityManager);
    }
}

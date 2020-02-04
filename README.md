<a href="https://www.wieni.be">
    <img src="https://www.wieni.be/themes/custom/drupack/logo.svg" alt="Wieni logo" title="Wieni" align="right" height="60" />
</a>

Drupal Custom Entity Form
======================

> Use custom entity forms per bundle.

## Installation

```
composer require wieni/wmcustom_entity_form
drush en wmcustom_entity_form
```

## Why

Because you like to build forms tailored to your client's needs. But you also like to keep codebase clean. 

## Use
This plugin-based module will look for entity forms in the `Form/Entity` directory of your modules.

The classes have to implement the `@EntityForm` annotation. The `id` you give that annotation will be
used to match the entity type and bundle.

## Caveats

This only works on forms created by the EntityFormBuilder. It won't kick in if your form is shown using
inline entity form. In that case you'll still need to use `HOOK_inline_entity_form_entity_form_alter` 

## Example

```php
<?php

namespace Drupal\mymodule\Form\Entity;

use Drupal\Core\Form\FormStateInterface;
use Drupal\wmcustom_entity_form\Annotation\EntityForm;
use Drupal\node\NodeForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @EntityForm(
 *     id="node.article"
 * )
 */
class ArticleForm extends NodeForm
{
    public static function create(ContainerInterface $container)
    {
        // use dependency injection
        return new static(...);
    }

    public function form(array $form, FormStateInterface $formState)
    {
        $form = parent::form($form, $formState);
        // alter form without using any hooks

        // use afterbuilds with the :: notation for non-static afterbuilds
        $form['#after_build'][] = '::myAfterBuild';

        // access $this->entity; ( powerful combined with wieni/wmmodel )
        if ($this->entity->getWhatever()) {
            $form['field_foo_bar']['#access'] = false;
        }

        return $form;
    }

    public function myAfterBuild(array $form, FormStateInterface $formState)
    {
        // access to $this scope and all your injected dependencies
    }

    // override protected methods of the original entity form
    protected function actions(array $form, FormStateInterface $formState)
    {
        return parent::actions($form, $formState);
    }
}
```

# Also see

- [wieni/wmmodel](https://github.com/wieni/wmmodel) to have a model class per bundle.
- [wieni/wmcontroller](https://github.com/wieni/wmcontroller) to have a controller per bundle.

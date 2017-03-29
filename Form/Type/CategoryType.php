<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Form\Type;


use Symfony\Component\Form\FormBuilderInterface;

use Pim\Bundle\EnrichBundle\Form\Type\CategoryType as BaseCategoryType;

/**
 * Type for category properties
 */
class CategoryType extends BaseCategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('googleCategoryId', 'text',
            [
                'required' => true
            ]
        );
    }
}

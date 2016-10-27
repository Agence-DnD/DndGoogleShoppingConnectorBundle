<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type to create/edit google categories
 *
 * @author Florian Fauvel <florian.fauvel@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GoogleCategoryType extends AbstractType
{

    /** @var string Entity FQCN */
    protected $dataClass;

    /** @var string Translation entity FQCN */
    protected $translationDataClass;

    /**
     * Constructor
     *
     * @param string $dataClass
     * @param string $translationDataClass
     */
    public function __construct($dataClass, $translationDataClass)
    {
        $this->dataClass            = $dataClass;
        $this->translationDataClass = $translationDataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addNameField($builder);
    }

    /**
     * Add name field
     *
     * @param FormBuilderInterface $builder
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(
            'name',
            'pim_translatable_field',
            [
                'field'             => 'name',
                'translation_class' => $this->translationDataClass,
                'entity_class'      => $this->dataClass,
                'property_path'     => 'translations'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'google_shopping_connector_enrich_google_category';
    }

    /**
     * Gets the parent of the form: adds fields form buildForm method to those of the parent
     *
     * @return string
     */
    public function getParent()
    {
        return 'pim_custom_entity';
    }
}

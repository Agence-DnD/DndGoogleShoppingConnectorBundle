<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\FormConfigurationProvider;


use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProvider\ProductCsvExport;
use Pim\Component\Catalog\Repository\CurrencyRepositoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;



/**
 * Class XmlProductExport
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class XmlProductExport extends ProductCsvExport
{

    /** @var array $decimalSeparators */
    protected $decimalSeparators;

    /** @var array $dateFormats */
    protected $dateFormats;

    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;

    /** @var CurrencyRepositoryInterface $currencyRepository */
    protected $currencyRepository;

    /**
     * @var array $supportedJobNames
     */
    protected $supportedJobNames;

    /**
     * @var FormConfigurationProviderInterface
     */
    protected $simpleCsvExport;


    /**
     * XmlProductExport constructor.
     *
     * @param FormConfigurationProviderInterface $simpleCsvExport
     * @param array                              $supportedJobNames
     * @param array                              $decimalSeparators
     * @param array                              $dateFormats
     * @param AttributeRepositoryInterface       $attributeRepositoryInterface
     * @param CurrencyRepositoryInterface        $currencyRepositoryInterface
     */
    public function __construct(
        FormConfigurationProviderInterface $simpleCsvExport,
        array $supportedJobNames,
        array $decimalSeparators,
        array $dateFormats,
        AttributeRepositoryInterface $attributeRepositoryInterface,
        CurrencyRepositoryInterface $currencyRepositoryInterface
    ) {
        parent::__construct($simpleCsvExport, $supportedJobNames, $decimalSeparators, $dateFormats);
        $this->attributeRepository = $attributeRepositoryInterface;
        $this->currencyRepository = $currencyRepositoryInterface;
    }


    /**
     * @return mixed
     */
    public function getFormConfiguration()
    {
        $parentConfiguration = parent::getFormConfiguration();

        $customOptions = [
            'currency'               => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->getCurrencyCodes(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'dnd_google_shopping_connector.export.currency.label',
                    'help'     => 'dnd_google_shopping_connector.export.currency.help',
                ],
            ],
            'channelTitle'                  => [
                'options' => [
                    'label' => 'dnd_google_shopping_connector.export.title.label',
                    'help'  => 'dnd_google_shopping_connector.export.title.help',
                ],
            ],
            'description'            => [
                'options' => [
                    'label' => 'dnd_google_shopping_connector.export.description.label',
                    'help'  => 'dnd_google_shopping_connector.export.description.help',
                ],
            ],
            'websiteLink'            => [
                'options' => [
                    'label' => 'dnd_google_shopping_connector.export.websiteLink.label',
                    'help'  => 'dnd_google_shopping_connector.export.websiteLink.help',
                ],
            ],
            'googleId'               => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleId.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleId.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleTitle'            => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleTitle.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleTitle.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleDescription'      => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleDescription.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleDescription.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleLink'             => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleLink.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleLink.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleImagesLink'       => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleImagesLink.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleImagesLink.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleCondition'        => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => [
                        'new'         => 'dnd_google_shopping_connector.export.googleCondition.choice.new',
                        'refurbished' => 'dnd_google_shopping_connector.export.googleCondition.choice.refurbished',
                        'used'        => 'dnd_google_shopping_connector.export.googleCondition.choice.used',
                    ],
                    'required' => true,
                    'label'    => 'dnd_google_shopping_connector.export.googleCondition.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleCondition.help',
                    'select2'  => true,
                ],
            ],
            'googleAvailability'     => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => [
                        'preorder'     => 'dnd_google_shopping_connector.export.googleAvailability.choice.preorder',
                        'in stock'     => 'dnd_google_shopping_connector.export.googleAvailability.choice.in_stock',
                        'out of stock' => 'dnd_google_shopping_connector.export.googleAvailability.choice.out_of_stock',
                    ],
                    'required' => true,
                    'label'    => 'dnd_google_shopping_connector.export.googleAvailability.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleAvailability.help',
                    'select2'  => true,
                ],
            ],
            'googlePrice'            => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googlePrice.label',
                    'help'     => 'dnd_google_shopping_connector.export.googlePrice.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleGtin'             => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleGtin.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleGtin.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleBrand'            => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleBrand.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleBrand.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleColor'            => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleColor.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleColor.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleGender'           => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleGender.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleGender.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleAgeGroup'         => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleAgeGroup.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleAgeGroup.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleMaterial'         => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleMaterial.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleMaterial.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googleSize'             => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googleSize.label',
                    'help'     => 'dnd_google_shopping_connector.export.googleSize.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googlePattern'          => [
                'type'    => 'choice',
                'options' => [
                    'label'    => 'dnd_google_shopping_connector.export.googlePattern.label',
                    'help'     => 'dnd_google_shopping_connector.export.googlePattern.help',
                    'required' => true,
                    'choices'  => $this->getAttributesChoices(),
                    'select2'  => true,
                ],
            ],
            'googlePersonalizeOne'   => [
                'type'    => 'choice',
                'options' => [
                    'label'   => 'dnd_google_shopping_connector.export.googlePersonalizeOne.label',
                    'help'    => 'dnd_google_shopping_connector.export.googlePersonalizeOne.help',
                    'choices' => $this->getAttributesChoices(),
                    'select2' => true,
                ],
            ],
            'googlePersonalizeTwo'   => [
                'type'    => 'choice',
                'options' => [
                    'label'   => 'dnd_google_shopping_connector.export.googlePersonalizeTwo.label',
                    'help'    => 'dnd_google_shopping_connector.export.googlePersonalizeTwo.help',
                    'choices' => $this->getAttributesChoices(),
                    'select2' => true,
                ],
            ],
            'googlePersonalizeThree' => [
                'type'    => 'choice',
                'options' => [
                    'label'   => 'dnd_google_shopping_connector.export.googlePersonalizeThree.label',
                    'help'    => 'dnd_google_shopping_connector.export.googlePersonalizeThree.help',
                    'choices' => $this->getAttributesChoices(),
                    'select2' => true,
                ],
            ],
            'googlePersonalizeFour'  => [
                'type'    => 'choice',
                'options' => [
                    'label'   => 'dnd_google_shopping_connector.export.googlePersonalizeFour.label',
                    'help'    => 'dnd_google_shopping_connector.export.googlePersonalizeFour.help',
                    'choices' => $this->getAttributesChoices(),
                    'select2' => true,
                ],
            ],
            'googlePersonalizeFive'  => [
                'type'    => 'choice',
                'options' => [
                    'label'   => 'dnd_google_shopping_connector.export.googlePersonalizeFive.label',
                    'help'    => 'dnd_google_shopping_connector.export.googlePersonalizeFive.help',
                    'choices' => $this->getAttributesChoices(),
                    'select2' => true,
                ],
            ],
            'pimMediaUrl'            => [
                'options' => [
                    'label' => 'dnd_google_shopping_connector.export.pimMediaUrl.label',
                    'help'  => 'dnd_google_shopping_connector.export.pimMediaUrl.help',
                ],
            ],
        ];

        return array_merge($parentConfiguration, $customOptions);

    }

    /**
     * Retrieve attributes code for select option
     *
     * @return array[] $choices
     */
    protected function getAttributesChoices()
    {
        $choices = [];
        $choices[''] = '';
        foreach ($this->attributeRepository->getAttributesAsArray() as $attribute) {
            $choices[$attribute['code']] = $attribute['code'];
        }

        return $choices;
    }

    /**
     * Get currency codes for select option
     *
     * @return array
     */
    protected function getCurrencyCodes()
    {
        $choices = [];
        foreach ($this->currencyRepository->getActivatedCurrencies() as $currency) {
            $choices[$currency->getCode()] = $currency->getCode();
        }

        return $choices;
    }

}

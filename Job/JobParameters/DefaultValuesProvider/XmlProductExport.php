<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Akeneo\Component\Localization\Localizer\LocalizerInterface;
use Pim\Component\Catalog\Query\Filter\Operators;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;

/**
 * Class XmlProductExport
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class XmlProductExport implements DefaultValuesProviderInterface
{

    /** @var array $supportedJobNames */
    protected $supportedJobNames;

    /** @var ChannelRepositoryInterface $channelRepository */
    protected $channelRepository;

    /** @var LocaleRepositoryInterface $localeRepository */
    protected $localeRepository;


    /**
     * XmlProductExport constructor.
     *
     * @param ChannelRepositoryInterface $channelRepository
     * @param LocaleRepositoryInterface  $localeRepository
     * @param array                      $supportedJobNames
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository,
        array $supportedJobNames
    ) {
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * Get the default values for the export
     * @return array
     */
    public function getDefaultValues()
    {
        $parameters['filePath'] = '/tmp/export_google_shopping.xml';
        $parameters['decimalSeparator'] = LocalizerInterface::DEFAULT_DECIMAL_SEPARATOR;
        $parameters['dateFormat'] = LocalizerInterface::DEFAULT_DATE_FORMAT;
        $parameters['currency'] = null;
        $parameters['channelTitle'] = null;
        $parameters['description'] = null;
        $parameters['websiteLink'] = null;
        $parameters['googleId'] = null;
        $parameters['googleTitle'] = null;
        $parameters['googleDescription'] = null;
        $parameters['googleLink'] = null;
        $parameters['googleImagesLink'] = null;
        $parameters['googleCondition'] = null;
        $parameters['googleAvailability'] = null;
        $parameters['googlePrice'] = null;
        $parameters['googleGtin'] = null;
        $parameters['googleBrand'] = null;
        $parameters['googleColor'] = null;
        $parameters['googleGender'] = null;
        $parameters['googleAgeGroup'] = null;
        $parameters['googleMaterial'] = null;
        $parameters['googleSize'] = null;
        $parameters['googlePattern'] = null;
        $parameters['googlePersonalizeOne'] = null;
        $parameters['googlePersonalizeTwo'] = null;
        $parameters['googlePersonalizeThree'] = null;
        $parameters['googlePersonalizeFour'] = null;
        $parameters['googlePersonalizeFive'] = null;
        $parameters['pimMediaUrl'] = null;
        $parameters['with_media'] = true;

        $defaultChannel = $this->channelRepository->getFullChannels()[0];
        $defaultLocaleCode = $this->localeRepository->getActivatedLocaleCodes()[0];
        $parameters['filters'] = [
            'data'      => [
                [
                    'field'    => 'enabled',
                    'operator' => Operators::EQUALS,
                    'value'    => true,
                ],
                [
                    'field'    => 'completeness',
                    'operator' => Operators::GREATER_OR_EQUAL_THAN,
                    'value'    => 100,
                ],
                [
                    'field'    => 'categories.code',
                    'operator' => Operators::IN_CHILDREN_LIST,
                    'value'    => [],
                ],
            ],
            'structure' => [
                'scope'   => $defaultChannel->getCode(),
                'locales' => $defaultLocaleCode,
            ],
        ];

        return $parameters;

    }

    /**
     * @param JobInterface $job
     *
     * @return bool
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}

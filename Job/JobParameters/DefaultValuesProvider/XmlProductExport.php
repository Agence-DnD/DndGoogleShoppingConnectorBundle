<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Connector\Job\JobParameters\DefaultValuesProvider\ProductCsvExport;

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

    /** @var array $supportedJobNames */
    protected $supportedJobNames;

    /** @var ChannelRepositoryInterface $channelRepository */
    protected $channelRepository;

    /** @var LocaleRepositoryInterface $localeRepository */
    protected $localeRepository;


    /**
     * XmlProductExport constructor.
     *
     * @param DefaultValuesProviderInterface $simpleProvider
     * @param ChannelRepositoryInterface     $channelRepository
     * @param LocaleRepositoryInterface      $localeRepository
     * @param array                          $supportedJobNames
     */
    public function __construct(
        DefaultValuesProviderInterface $simpleProvider,
        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository,
        array $supportedJobNames
    ) {
        parent::__construct($simpleProvider, $channelRepository, $localeRepository, $supportedJobNames);
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $parameters = parent::getDefaultValues();

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

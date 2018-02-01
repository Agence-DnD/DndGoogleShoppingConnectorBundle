<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Pim\Component\Connector\Job\JobParameters\DefaultValuesProvider\ProductCsvExport;

/**
 * Class ProductXmlExport
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class ProductXmlExport extends ProductCsvExport
{

    /**
     * @return array $parameters
     */
    public function getDefaultValues(): array
    {
        $parameters                           = parent::getDefaultValues();
        $parameters['currency']               = null;
        $parameters['channelTitle']           = null;
        $parameters['description']            = null;
        $parameters['websiteLink']            = null;
        $parameters['googleId']               = null;
        $parameters['googleTitle']            = null;
        $parameters['googleDescription']      = null;
        $parameters['googleLink']             = null;
        $parameters['googleImagesLink']       = null;
        $parameters['googleCondition']        = null;
        $parameters['googleAvailability']     = null;
        $parameters['googlePrice']            = null;
        $parameters['googleGtin']             = null;
        $parameters['googleBrand']            = null;
        $parameters['googleColor']            = null;
        $parameters['googleGender']           = null;
        $parameters['googleAgeGroup']         = null;
        $parameters['googleMaterial']         = null;
        $parameters['googleSize']             = null;
        $parameters['googlePattern']          = null;
        $parameters['googlePersonalizeOne']   = null;
        $parameters['googlePersonalizeTwo']   = null;
        $parameters['suffix_category']        = null;
        $parameters['googlePersonalizeThree'] = null;
        $parameters['googlePersonalizeFour']  = null;
        $parameters['googlePersonalizeFive']  = null;

        return $parameters;
    }
}

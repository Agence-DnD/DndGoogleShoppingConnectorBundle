<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\DefaultValuesProvider;

use Pim\Component\Connector\Job\JobParameters\DefaultValuesProvider\SimpleXlsxImport;

/**
 * Class GoogleCategoryXlsxImport
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class GoogleCategoryXlsxImport extends SimpleXlsxImport
{

    /**
     * @return array $parameters
     */
    public function getDefaultValues(): array
    {
        $parameters                    = parent::getDefaultValues();
        $parameters['master_category'] = null;
        $parameters['suffix_category'] = null;

        return $parameters;
    }
}

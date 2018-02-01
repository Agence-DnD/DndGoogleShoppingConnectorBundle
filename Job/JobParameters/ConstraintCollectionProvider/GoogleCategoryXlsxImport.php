<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use Pim\Component\Connector\Job\JobParameters\ConstraintCollectionProvider\SimpleXlsxImport;

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
     * @return array
     */
    public function getConstraintCollection(): array
    {
        return [];
    }
}

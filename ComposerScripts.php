<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle;

use Composer\Script\Event;

/**
 * Google Shopping connector scripts for composer
 *
 * @author    Nicolas Souffleur <nicolas.souffleur@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ComposerScripts
{

    /** @var string PRODUCT_VALUE_FILENAME ProductValue override file name */
    const PRODUCT_VALUE_FILENAME = 'ProductValue.php';

    /** @var string CE_FILENAME Community Edition ProductValue File */
    const CE_FILENAME = 'ProductValue.php.ce';

    /** @var string EE_FILENAME Enterprise Edition ProductValue File */
    const EE_FILENAME = 'ProductValue.php.ee';

    /**
     *
     * Script to generate the ProductValue override depending the Akeneo edition
     *
     * @param Event $event
     */
    public static function getProductValueByPimEdition(Event $event)
    {
        $directory = __DIR__.'/Model/';

        if (file_exists($directory.self::PRODUCT_VALUE_FILENAME)) {
            return;
        }

        $akeneoEdition = self::CE_FILENAME;

        $enterprisePackage = false;

        if (file_exists(__DIR__.'/../../akeneo/pim-enterprise-standard') || file_exists(__DIR__.'/../../akeneo/pim-enterprise-dev')) {
            $enterprisePackage = true;
        }

        if (true === $enterprisePackage) {
            $akeneoEdition = self::EE_FILENAME;
        }

        copy($directory.$akeneoEdition, $directory.self::PRODUCT_VALUE_FILENAME);
    }
}

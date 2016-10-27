<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Model;

use Akeneo\Component\Localization\Model\TranslationInterface;

/**
 * Google category translation interface
 *
 * @author    Florian Fauvel <florian.fauvel@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface GoogleCategoryTranslationInterface extends TranslationInterface
{
    /**
     * Set name
     *
     * @param string $name
     *
     * @return GoogleCategoryTranslationInterface
     */
    public function setName($name);

    /**
     * Get the name
     *
     * @return string
     */
    public function getName();
}

<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Entity;


use Pim\Bundle\CatalogBundle\Entity\Category as BaseCategory;

/**
 * Class Category
 *
 * @package   Dnd\Bundle\GoogleShoppingConnectorBundle\Entity
 * @author    Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright Agence Dn'D <http://www.dnd.fr>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Category extends BaseCategory
{

    /** @var  $googleCategoryId */
    protected $googleCategoryId;

    /**
     * @return mixed
     */
    public function getGoogleCategoryId()
    {
        return $this->googleCategoryId;
    }

    /**
     * @param $googleCategoryId
     *
     * @return $this
     */
    public function setGoogleCategoryId($googleCategoryId)
    {
        $this->googleCategoryId = $googleCategoryId;

        return $this;
    }
}

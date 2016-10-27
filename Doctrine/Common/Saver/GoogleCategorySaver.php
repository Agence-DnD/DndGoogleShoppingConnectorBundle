<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Doctrine\Common\Saver;

use Akeneo\Component\StorageUtils\Saver\BulkSaverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Component\StorageUtils\Saver\SavingOptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Dnd\Bundle\GoogleShoppingConnectorBundle\Entity\GoogleCategory;

/**
 * Saves google category unitary or by bulk operation.
 *
 * @author Florian Fauvel <florian.fauvel@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GoogleCategorySaver implements SaverInterface, BulkSaverInterface
{
    /** @var ObjectManager */
    protected $objectManager;

    /** @var SavingOptionsResolverInterface */
    protected $optionsResolver;

    /**
     * @param ObjectManager                  $objectManager
     * @param SavingOptionsResolverInterface $optionsResolver
     */
    public function __construct(
        ObjectManager $objectManager,
        SavingOptionsResolverInterface $optionsResolver
    ) {
        $this->objectManager   = $objectManager;
        $this->optionsResolver = $optionsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function save($googleCategory, array $options = [])
    {
        if (!$googleCategory instanceof GoogleCategory) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expects a "Dnd\Bundle\GoogleShoppingConnectorBundle\Entity\GoogleCategory", "%s" provided.',
                    ClassUtils::getClass($googleCategory)
                )
            );
        }

        $options = $this->optionsResolver->resolveSaveOptions($options);
        $this->objectManager->persist($googleCategory);

        if (true === $options['flush']) {
            $this->objectManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(array $googleCategories, array $options = [])
    {
        if (empty($googleCategories)) {
            return;
        }

        $allOptions  = $this->optionsResolver->resolveSaveAllOptions($options);
        $itemOptions = $allOptions;
        $itemOptions['flush'] = false;

        foreach ($googleCategories as $googleCategory) {
            $this->save($googleCategory, $itemOptions);
        }

        if (true === $allOptions['flush']) {
            $this->objectManager->flush();
        }
    }
}

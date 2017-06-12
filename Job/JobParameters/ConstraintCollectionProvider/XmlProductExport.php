<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class XmlProductExport
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class XmlProductExport implements ConstraintCollectionProviderInterface
{

    /** @var array $supportedJobNames */
    protected $supportedJobNames;

    /**
     * XmlProductExport constructor.
     *
     * @param array $supportedJobNames
     */
    public function __construct(
        array $supportedJobNames
    ) {
        $this->supportedJobNames = $supportedJobNames;
    }


    /**
     * @return Collection
     */
    public function getConstraintCollection()
    {
        return new Collection(['fields' => []]);
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

<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\ConstraintCollectionProvider;
use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class GoogleCategoryImport
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class GoogleCategoryImport implements ConstraintCollectionProviderInterface
{

    /** @var ConstraintCollectionProviderInterface */
    protected $simpleProvider;
    /** @var array */
    protected $supportedJobNames;

    /**
     * GoogleCategoryImport constructor.
     *
     * @param ConstraintCollectionProviderInterface $simpleCsv
     * @param array                                 $supportedJobNames
     */
    public function __construct(ConstraintCollectionProviderInterface $simpleCsv, array $supportedJobNames)
    {
        $this->simpleProvider = $simpleCsv;
        $this->supportedJobNames = $supportedJobNames;
    }
    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        return new Collection(['fields' => []]);
    }
    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}

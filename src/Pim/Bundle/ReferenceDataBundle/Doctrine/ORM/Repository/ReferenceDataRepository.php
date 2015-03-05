<?php

namespace Pim\Bundle\ReferenceDataBundle\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Pim\Component\ReferenceData\Repository\ReferenceDataRepositoryInterface;
use Pim\Bundle\UIBundle\Entity\Repository\OptionRepositoryInterface;

/**
 * Repository for reference data entities
 *
 * TODO-CR: should not implement OptionRepositoryInterface that comes from the UI
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ReferenceDataRepository extends EntityRepository implements
 ReferenceDataRepositoryInterface, OptionRepositoryInterface
{
    /**
     * TODO-CR: should be renamed or dropped if unused
     *
     * {@inheritdoc}
     */
    public function getOption($id, $collectionId = null, array $options = array())
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * TODO-CR: should be renamed
     *
     * {@inheritdoc}
     */
    public function getOptions($dataLocale, $collectionId = null, $search = '', array $options = array())
    {
        $qb = $this->createQueryBuilder('cr');
        $qb->select(sprintf('%s.id as id, %s.code as text', $this->getAlias(), $this->getAlias()));

        return [
            'results' => $qb->getQuery()->getArrayResult(),
        ];
    }

    /**
     * TODO-CR: should be dropped
     *
     * {@inheritdoc}
     */
    public function getOptionLabel($referenceData, $dataLocale)
    {
        return '[' . $referenceData->getCode() . ']';
    }

    /**
     * TODO-CR: should be dropped
     *
     * {@inheritdoc}
     */
    public function getOptionId($referenceData)
    {
        return $referenceData->getId();
    }

    public function getAlias()
    {
        return 'cr';
    }
}

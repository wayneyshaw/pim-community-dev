<?php

namespace spec\Pim\Bundle\EnrichBundle\Connector\Reader\MassEdit;

use Akeneo\Component\Batch\Job\JobParameters;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Bundle\StorageUtilsBundle\Doctrine\ORM\Cursor\Cursor;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\EnrichBundle\Connector\Item\MassEdit\VariantGroupCleaner;
use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Query\ProductQueryBuilder;
use Pim\Component\Catalog\Query\ProductQueryBuilderFactoryInterface;

class FilteredVariantGroupProductReaderSpec extends ObjectBehavior
{
    function let(
        ProductQueryBuilderFactoryInterface $pqbFactory,
        VariantGroupCleaner $cleaner,
        StepExecution $stepExecution
    ) {
        $this->beConstructedWith($pqbFactory, $cleaner);
        $this->setStepExecution($stepExecution);
    }

    function it_reads_products(
        $cleaner,
        StepExecution $stepExecution,
        ProductInterface $product,
        $pqbFactory,
        ProductQueryBuilder $pqb,
        Cursor $cursor,
        JobParameters $jobParameters
    ) {
        $configuration = [
            'filters' => [
                [
                    'field'    => 'id',
                    'operator' => 'IN',
                    'value'    => [12, 13, 14]
                ],
            ],
            'actions' => []
        ];
        $stepExecution->getJobParameters()->willReturn($jobParameters);
        $jobParameters->get('filters')->willReturn($configuration['filters']);
        $jobParameters->get('actions')->willReturn($configuration['actions']);

        $cleaner->clean($stepExecution, $configuration['filters'], $configuration['actions'])
            ->willReturn(
                [
                    [
                        'field'    => 'id',
                        'operator' => 'IN',
                        'value'    => [12, 13]
                    ]
                ]
            );

        $pqbFactory->create()->willReturn($pqb);
        $pqb->addFilter('id', 'IN', [12, 13], ['locale' => null, 'scope' => null])->shouldBeCalled();
        $pqb->execute()->willReturn($cursor);
        $cursor->next()->shouldBeCalled();
        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(1);
        $cursor->current()->willReturn($product);

        $this->read()->shouldReturn($product);
    }
}

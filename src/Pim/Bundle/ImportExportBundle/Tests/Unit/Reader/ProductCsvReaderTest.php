<?php

namespace Pim\Bundle\ImportExportBundle\Tests\Unit\Reader;

use Pim\Bundle\ImportExportBundle\Reader\ProductCsvReader;

/**
 * Test related class
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductCsvReaderTest extends CsvReaderTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->reader = new ProductCsvReader(
            $this->getEntityManagerMock(
                array('sku', 'name'),
                array('view', 'manual')
            )
        );
        $this->stepExecution = $this->getStepExecutionMock();
        $this->reader->setStepExecution($this->stepExecution);
    }

    /**
     * Test related method
     */
    public function testExtendsCsvReader()
    {
        $this->assertInstanceOf('Pim\Bundle\ImportExportBundle\Reader\CsvReader', $this->reader);
    }

    /**
     * Test related method
     */
    public function testInitializeWithAttributeRepository()
    {
        $this->assertAttributeEquals(
            array('sku' => array(), 'name' => array()),
            'uniqueValues',
            $this->reader
        );

        $this->assertAttributeEquals(
            array('view', 'manual'),
            'mediaAttributes',
            $this->reader
        );
    }

    /**
     * Test related method
     */
    public function testReadDuplicateUniqueValue()
    {
        $this->reader->setFilePath(__DIR__ . '/../../fixtures/duplicate_values.csv');

        $this->stepExecution
            ->expects($this->once())
            ->method('addReaderWarning')
            ->with(
                get_class($this->reader),
                $this->stringStartsWith(
                    'The "sku" attribute is unique, the value "SKU-001" was already read in this file'
                ),
                array('sku' => 'SKU-001', 'name' => 'window')
            );

        $this->assertEquals(array('sku' => 'SKU-001', 'name' => 'door'), $this->reader->read());
        $this->assertEquals(array('sku' => 'SKU-002', 'name' => 'hatch'), $this->reader->read());
        $this->assertFalse($this->reader->read());
        $this->assertNull($this->reader->read());
    }

    public function testMediaPathAreAbsolute()
    {
        $this->reader->setFilePath(__DIR__ . '/../../fixtures/with_media.csv');

        $this->assertEquals(
            array(
                'sku'          => 'SKU-001',
                'name'         => 'door',
                'view'         => __DIR__ . '/../../fixtures/sku-001.jpg',
                'manual-fr_FR' => __DIR__ . '/../../fixtures/sku-001.txt',
            ),
            $this->reader->read()
        );
    }

    /**
     * @param array $uniqueAttributeCodes
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManagerMock(array $uniqueAttributeCodes, array $mediaAttributeCodes)
    {
        $em = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will(
                $this->returnValue(
                    $this->getEntityRepositoryMock(
                        $uniqueAttributeCodes,
                        $mediaAttributeCodes
                    )
                )
            );

        return $em;
    }

    /**
     * @param array $uniqueAttributeCodes
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getEntityRepositoryMock(array $uniqueAttributeCodes, array $mediaAttributeCodes)
    {
        $repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(array('findUniqueAttributeCodes', 'findMediaAttributeCodes'))
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->any())
            ->method('findUniqueAttributeCodes')
            ->will($this->returnValue($uniqueAttributeCodes));

        $repository->expects($this->any())
            ->method('findMediaAttributeCodes')
            ->will($this->returnValue($mediaAttributeCodes));

        return $repository;
    }
}

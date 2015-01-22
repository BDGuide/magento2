<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Plugin;

class TransactionWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Plugin\ProductRepository\TransactionWrapper
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Resource\Product
     */
    protected $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \Closure
     */
    protected $rollbackClosureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var bool
     */
    protected $saveOption = true;

    const ERROR_MSG = "error occurred";

    protected function setUp()
    {
        $this->resourceMock = $this->getMock('Magento\Catalog\Model\Resource\Product', [], [], '', false);
        $this->subjectMock = $this->getMock('Magento\Catalog\Api\ProductRepositoryInterface', [], [], '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Api\Data\ProductInterface', [], [], '', false);
        $productMock = $this->productMock;
        $saveOption = $this->saveOption;
        $this->closureMock = function () use ($productMock, $saveOption) {
            return $productMock;
        };
        $this->rollbackClosureMock = function () use ($productMock, $saveOption) {
            throw new \Exception(self::ERROR_MSG);
        };

        $this->model = new \Magento\Catalog\Model\Plugin\ProductRepository\TransactionWrapper($this->resourceMock);
    }

    public function testAroundSaveCommit()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('commit');

        $this->assertEquals(
            $this->productMock,
            $this->model->aroundSave($this->subjectMock, $this->closureMock, $this->productMock, $this->saveOption)
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage error occurred
     */
    public function testAroundSaveRollBack()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('rollBack');

        $this->model->aroundSave($this->subjectMock, $this->rollbackClosureMock, $this->productMock, $this->saveOption);
    }

    public function testAroundDeleteCommit()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('commit');

        $this->assertEquals(
            $this->productMock,
            $this->model->aroundDelete($this->subjectMock, $this->closureMock, $this->productMock, $this->saveOption)
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage error occurred
     */
    public function testAroundDeleteRollBack()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('rollBack');

        $this->model->aroundDelete($this->subjectMock, $this->rollbackClosureMock, $this->productMock, $this->saveOption);
    }

    public function testAroundDeleteByIdCommit()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('commit');

        $this->assertEquals(
            $this->productMock,
            $this->model->aroundDelete($this->subjectMock, $this->closureMock, $this->productMock, $this->saveOption)
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage error occurred
     */
    public function testAroundDeleteByIdRollBack()
    {
        $this->resourceMock->expects($this->once())->method('beginTransaction');
        $this->resourceMock->expects($this->once())->method('rollBack');

        $this->model->aroundDelete($this->subjectMock, $this->rollbackClosureMock, $this->productMock, $this->saveOption);
    }
}

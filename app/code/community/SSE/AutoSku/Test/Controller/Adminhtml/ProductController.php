<?php
/**
 * This file is part of SSE_AutoSku for Magento.
*
* @license osl-3.0
* @author Fabian Schmengler <fabian@schmengler-se.de> <@fschmengler>
* @category SSE
* @package SSE_AutoSku
* @copyright Copyright (c) 2015 Schmengler Software Engineering (http://www.schmengler-se.de/)
*/

/**
 * Test case for SKUs of associated products generated by "quick create" of configurable product
 *
 * @package SSE_AutoSku
 */
class SSE_AutoSku_Test_Controller_Adminhtml_ProductController extends EcomDev_PHPUnit_Test_Case_Controller
{
    const DEFAULT_ATTRIBUTE_SET = 4;
    const GENERAL_ATTRIBUTE_GROUP = 7;
    
    private $optionId;
    
    /**
     * Add color attribute to default attribute set and create option
     */
    protected function setUp()
    {
        parent::setUp();
        $setup = new Mage_Catalog_Model_Resource_Setup('catalog_setup');
        $colorAttributeId = $setup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'color');
        $setup->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY,
            self::DEFAULT_ATTRIBUTE_SET, self::GENERAL_ATTRIBUTE_GROUP, $colorAttributeId);
        $setup->addAttributeOption(array(
        	'attribute_id' => $colorAttributeId,
            'values'       => [ 'Octarin']
        ));
        $this->optionId = $setup->getConnection()->lastInsertId($setup->getTable('eav/attribute_option'));
    }
    /**
     * Delete created color option
     */
    protected function tearDown()
    {
        $setup = new Mage_Catalog_Model_Resource_Setup('catalog_setup');
        Mage::getModel('eav/entity_attribute_option')->setId($this->optionId)->delete();
        parent::tearDown();
    }
    /**
     * @test
     * @loadFixture configurableProduct
     * @singleton adminhtml/session
     * @singleton catalog/product_type_configurable
     */
    public function testQuickCreate()
    {
        $inputProductId = 1;
        $inputSimpleProduct = array(
        	'name_autogenerate' => '1',
            'color'             => $this->optionId,
            'status'            => 1,
            'weight'            => 1
        );
        $expectedSku = 'S100-Octarin';

        $this->adminSession();
        $this->getRequest()->setMethod('post');
        $this->getRequest()->setParam('product', $inputProductId);
        $this->getRequest()->setPost(array('simple_product' => $inputSimpleProduct));
        $this->dispatch('adminhtml/catalog_product/quickCreate');
        $this->assertRequestRoute('adminhtml/catalog_product/quickCreate');
        $this->assertResponseBodyJsonMatch(array_flip(array('attributes', 'product_id')), 'JSON response should contain product_id and attributes');
        $actualResponseJson = Zend_Json::decode($this->getResponse()->getOutputBody());
        $actualSimpleProduct = Mage::getModel('catalog/product')->load($actualResponseJson['product_id']);
        $this->assertEquals($expectedSku, $actualSimpleProduct->getSku(), 'SKU should be created from configurable SKU and attribute option');
    }
}
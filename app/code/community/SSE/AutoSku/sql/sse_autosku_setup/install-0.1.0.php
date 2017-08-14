<?php
/**
 * This file is part of SSE_AutoSku for Magento.
 *
 * @license osl-3.0
 * @author Fabian Schmengler <fabian@schmengler-se.de> <@fschmengler>
 * @category SSE
 * @package SSE_AutoSku
 * @copyright Copyright (c) 2014 Schmengler Software Engineering (http://www.schmengler-se.de/)
 */

/* @var $installer SSE_AutoSku_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/*
 * Set increment model for product entity
 */
$installer->updateEntityType(Mage_Catalog_Model_Product::ENTITY, [
    'increment_model'      => 'eav/entity_increment_numeric',
    'increment_pad_length' => 5,
    'increment_per_store'  => false]);
$installer->resetProductEntityStoreConfig();

/*
 * Set increment backend model for sku attribute
 */
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'sku', [
    'backend_model' => 'sse_autosku/entity_attribute_backend_increment',
    'is_required'   => '0',
    'is_unique'     => '1']);

$installer->endSetup();

<?php
class Cammino_Cachecleaner_Model_Observer
{
    public function clearCache()
    {
        Mage::app()->cleanCache();
        Mage::app()->getCacheInstance()->flush();
        Mage::getSingleton('fpc/fpc')->clean();
    }

    public function clearCacheAfterOrder(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $clearCacheActive = Mage::getStoreConfig('cachecleaner/cachecleaner_group/cachecleaner_active');
    
        if (!empty($clearCacheActive)) {
            Mage::log('Verificando o estoque dos itens do pedido', null, 'cache_cleaner.log');
    
            foreach ($order->getAllItems() as $item) {
                $productId = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
    
                // Skip product if is not a simple product
                if ($product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
                    continue;
                }
    
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                if ($stockItem->getQty() <= 0) {
                    Mage::log(
                        "O produto '{$product->getName()}' (SKU: {$product->getSku()}) ficou sem estoque após o pedido.",
                        null,
                        'cache_cleaner.log'
                    );
    
                    Mage::log('Limpando o cache FPC da loja', null, 'cache_cleaner.log');
                    Mage::getSingleton('fpc/fpc')->clean();
                    break;
                }
            }
        }
    }
}

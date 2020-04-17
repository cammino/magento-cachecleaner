<?php
class Cammino_Cachecleaner_Model_Observer
{
    public function clearCache()
    {
        Mage::app()->cleanCache();
        Mage::app()->getCacheInstance()->flush();
        rmdir('../../../../../var/cache');
    }
}

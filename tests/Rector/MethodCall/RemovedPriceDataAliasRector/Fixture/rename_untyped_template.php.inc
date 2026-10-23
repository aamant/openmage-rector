<?php $_productCollection = $this->getLoadedProductCollection()->addMinimalPrice() ?>
<?php foreach ($block->getCollection()->addMinimalPrice()->addFinalPrice() as $_product): ?>
    <?php echo $_product->getFinalPrice() ?>
<?php endforeach ?>
-----
<?php $_productCollection = $this->getLoadedProductCollection()->addPriceData() ?>
<?php foreach ($block->getCollection()->addPriceData() as $_product): ?>
    <?php echo $_product->getFinalPrice() ?>
<?php endforeach ?>

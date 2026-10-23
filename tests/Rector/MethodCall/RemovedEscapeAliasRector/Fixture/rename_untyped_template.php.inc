<?php foreach ($this->getItems() as $_item): ?>
    <a href="<?php echo $this->urlEscape($_item->getUrl()) ?>" title="<?php echo $this->htmlEscape($_item->getName()) ?>">
        <?php echo $this->helper('core')->htmlEscape($_item->getName(), ['b']) ?>
    </a>
<?php endforeach ?>
-----
<?php foreach ($this->getItems() as $_item): ?>
    <a href="<?php echo $this->escapeUrl($_item->getUrl()) ?>" title="<?php echo $this->escapeHtml($_item->getName()) ?>">
        <?php echo $this->helper('core')->escapeHtml($_item->getName(), ['b']) ?>
    </a>
<?php endforeach ?>

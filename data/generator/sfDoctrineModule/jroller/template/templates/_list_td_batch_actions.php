<td style="width: <?php echo $this->configuration->isNestedSet() ? '40px': '20px' ?>">
  <input type="checkbox" name="ids[]" value="[?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]" class="sf_admin_batch_checkbox" />
<?php if ($this->configuration->isNestedSet()): ?>
  <input type="hidden" id="select_node-[?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]" name="newparent[[?php echo $<?php echo $this->getSingularName() ?>->getPrimaryKey() ?]]" />
  <div class="jroller-drag" style="display:inline-block; float:right;"><a class="ui-icon ui-icon-arrow-4-diag"></a></div>
<?php endif; ?>
</td>


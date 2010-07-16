  public function getPager($model)
  {
    $class = $this->getPagerClass();

    return new $class($model, $this->getPagerMaxPerPage());
  }

  public function getPagerClass()
  {
    return '<?php echo isset($this->config['list']['pager_class']) ? $this->config['list']['pager_class'] : 'sfDoctrinePager' ?>';
<?php unset($this->config['list']['pager_class']) ?>
  }

  public function getPagerMaxPerPage()
  {
<?php if( !isset($this->config['list']['max_per_page']) ):?>
     return <?php echo $this->isNestedSetVar ? '300' : '20'; ?>;
<?php else:  ?>
     return <?php echo (integer) $this->config['list']['max_per_page']; ?>;
<?php endif;  ?>
<?php unset($this->config['list']['max_per_page']) ?>
  }

  public function getShowActions()
  {
    return array(  '_list' => NULL,  '_edit' => NULL, '_delete' => NULL);
  }

  
  public function getShowTitle()
  {
    return '<?php echo isset($this->config['show']['title']) ? $this->config['show']['title'] : 'View '.sfInflector::humanize($this->getModuleName()) ?>';
    <?php unset($this->config['show']['title']) ?>
  }

  public function getShowTab()
  {
    return '<?php echo isset($this->config['show']['tab']) ? $this->config['show']['tab'] : 'vertical' ?>';
    <?php unset($this->config['show']['tab']) ?>
  }

  public function getShowDisplay()
  {
    <?php if (isset($this->config['show']['display'])): ?>
    return <?php echo $this->asPhp($this->config['show']['display']) ?>;
    <?php elseif (isset($this->config['show']['hide'])): ?>
    return <?php echo $this->asPhp(array_diff($this->getAllFieldNames(false), $this->config['show']['hide'])) ?>;
    <?php else: ?>
    return <?php echo $this->asPhp($this->getAllFieldNames(false)) ?>;
    <?php endif; ?>
    <?php unset($this->config['show']['display'], $this->config['show']['hide']) ?>
  }

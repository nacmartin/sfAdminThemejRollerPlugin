  protected function getConfig()
  {
    $configuration = parent::getConfig();
    $configuration['show'] = $this->getFieldsShow();
    return $configuration;
  }

  protected function compile()
  {
    parent::compile();
    
    $config = $this->getConfig();
    
    // add configuration for the show view 
    $this->configuration['show'] = array( 'fields'         => array(),
                                          'title'          => $this->getShowTitle(),
                                          'actions'        => $this->getShowActions(),
                                          'display'        => $this->getShowDisplay(),
                                          'tab'            => $this->getShowTab(),
                                        ) ;

    $this->configuration['edit']['tab']  =  $this->getEditTab();


    foreach (array('show') as $context)
    {
      foreach ($this->configuration[$context]['actions'] as $action => $parameters)
      {
        $this->configuration[$context]['actions'][$action] = $this->fixActionParameters($action, $parameters);
      }
    }


  }

  public function getEditTab()
  {
    return '<?php echo isset($this->config['edit']['tab']) ? $this->config['edit']['tab'] : 'vertical' ?>';
    <?php unset($this->config['edit']['tab']) ?>
  }

 

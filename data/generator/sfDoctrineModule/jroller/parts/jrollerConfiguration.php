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

    foreach ($this->configuration['show']['actions'] as $action => $parameters)
    {
        $this->configuration['show']['actions'][$action] = $this->fixActionParameters($action, $parameters);
    }

    $this->configuration['edit']['tab']  =  $this->getEditTab();

    //Add batch actions for NestedSet Model
    if(!isset( $this->configuration['list']['batch_actions']['batchSavetreeorder']) && $this->isNestedSet() )
    {
        $parameters     = $this->fixActionParameters('order', array('label' => 'Save Tree Order'));
        $this->configuration['list']['batch_actions']['batchSavetreeorder'] = $parameters;
    }
  }

  public function getEditTab()
  {
    return '<?php echo isset($this->config['edit']['tab']) ? $this->config['edit']['tab'] : 'vertical' ?>';
<?php unset($this->config['edit']['tab']) ?>
  }

  public function isNestedSet()
  {
    return <?php if( isset($this->config['nestedset']) && $this->config['nestedset'] == true )
                {
                    echo 'true';
                    $this->isNestedSetVar = true;
                }
                else
                {
                    echo 'false';
                    $this->isNestedSetVar = false;
                }?>;
<?php unset($this->config['nestedset']) ?>
  }


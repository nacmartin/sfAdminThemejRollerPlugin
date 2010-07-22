<?php
        $form           = $this->getFormObject();
        $modelClass     = $this->getModelClass();
        $class          = New $modelClass;
?>

  public function editableValidRequest(sfWebRequest $request, $field_name = null)
  {
        $field_id       = $request->getParameter('id');
        if( $field_id == null ) return $this->forward404('Field Id is not define');

        $field          = Doctrine::getTable('<?php echo $modelClass ?>')->findOneById($field_id);
        if( $field == false ) return $this->forward404('Field Id is not correct');

        if( $field_name == null ) $field_name = $request->getParameter('name');
        if( isset($field->$field_name) == false )
        {
            $field_name = strtolower($field_name) . '_id';
            if( isset($field->$field_name) == false ) return $this->forward404('Field Name is not correct with field : '.$field_name);
        }

        return array($field, $field_name);
  }

  public function executeEditableSetBase(sfWebRequest $request, $field_name = null)
  {
        list($field, $field_name) = $this->editableValidRequest($request, $field_name);

        $new_value  = $request->getParameter('value');

        $field->$field_name = $new_value;
        $field->save();

        return sfView::NONE;
  }

  public function executeEditableGetBase(sfWebRequest $request, $field_name = null, $tmp = null)
  {
        list($field, $field_name)  = $this->editableValidRequest($request, $field_name);

        $this->getResponse()->setHttpHeader('Content-Type', 'application/json;');

        if(is_array($tmp) == false )
        {
            $tmp =  $field->$tmp();
        }

        return $this->renderText( json_encode($tmp) );
  }

  public function executeEditableNewGetBase(sfWebRequest $request, $field_name = null, $tmp = null)
  {
        list($field, $field_name)  = $this->editableValidRequest($request, $field_name);

        $this->getResponse()->setHttpHeader('Content-Type', 'application/json;');
        
        if( $tmp == null )  return $this->renderText( json_encode( array() ) );
        else                return $this->renderText( json_encode( array('label' => $tmp )) );
  }
  
  public function executeEditableNewSetBase(sfWebRequest $request, $field_name = null, $tmp = null)
  {
        list($field, $field_name)  = $this->editableValidRequest($request, $field_name);

        $tmpMethodName  = 'newEditableListFor'.ucwords($request->getParameter('name'));

        $retcode = $field->$tmpMethodName( $request->getParameter('value') );
        
        if($retcode != true) $this->getResponse()->setStatusCode(500, 'Error during the '.$tmpMethodName.' method');

        return sfView::NONE;
  }

<?php foreach($this->configuration->getValue('list.display') as $name => $field): ?>
<?php if( $field->isEditable() == true ):    ?>
  public function executeEditableSet<?php echo ucwords($name); ?>(sfWebRequest $request)
  {
        return $this->executeEditableSetBase($request, '<?php echo $name; ?>');
  }

  public function executeEditableNewGet<?php echo ucwords($name); ?>(sfWebRequest $request)
  {
        return $this->executeEditableNewGetBase($request, '<?php echo $name; ?>');
  }

  public function executeEditableNewSet<?php echo ucwords($name); ?>(sfWebRequest $request)
  {
<?php $methodName = 'newEditableListFor'. ucwords($field->getName());
      if( method_exists($class, $methodName) ): ?>
        return $this->executeEditableNewSetBase($request, '<?php echo $name; ?>');
<?php else: ?>
        return $this->forward404('Field don\'t support this method');
<?php endif;  ?>
  }

  public function executeEditableGet<?php echo ucwords($name); ?>(sfWebRequest $request)
  {
<?php   $fieldType  = null;
        $methodName = 'getEditableListFor'.  ucwords($field->getName()).'AsArray';

        if( method_exists($class, $methodName)) $fieldType  = 'list';
        else                                    $methodName = null;

        if( $methodName == null )
        {
            try {
                $fieldWidget        = $form->getWidget( $field->getName() );
            }
            catch(Exception $e) {
                $fieldWidget        = null;
            }
            
            $fieldWidgetClass   = get_class($fieldWidget);

            switch( $fieldWidgetClass )
            {
                //case 'sfWidgetFormDoctrineChoice':
                case 'sfWidgetFormChoice':
                    $fieldType = 'list';
                    break;

                default:
                    $fieldType = 'text';
                    break;
            }
        }
?>
<?php   if( $fieldType == 'text'): ?>
        return $this->forward404('Field don\'t support this method');
<?php   endif; ?>
<?php   if( $fieldType == 'list' && $methodName != null ): ?>
        return $this->executeEditableGetBase($request, '<?php echo $name; ?>', '<?php echo $methodName; ?>');
<?php   elseif ( $fieldType == 'list'):  ?>

        $tmp    = array(
<?php foreach( $fieldWidget->getChoices() as $id => $n ): ?>
                        array('id' => '<?php echo $id; ?>', 'name' => '<?php echo $n; ?>'),
<?php endforeach;?>
                  );

        return $this->executeEditableGetBase($request, '<?php echo $name; ?>', $tmp);
<?php   endif; ?>    
  }
<?php endif; ?>
<?php endforeach; ?>

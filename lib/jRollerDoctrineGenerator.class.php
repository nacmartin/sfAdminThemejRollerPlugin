<?php

class jRollerDoctrineGenerator extends sfDoctrineGenerator
{
  /**
   * Gets extra parameters for jRoller plugin.
   *
   * @return mixed
   */
  public function getExtra($value = false)
  {
    if (isset($this->params['extra']))
    {
      if ($value)
      {
        foreach ($this->params['extra'] as $val)
        {
          if ($val == $value) return true;
        }
        return false;
      }
      else
      {
        return $this->params['extra'];
      }
    }
    else
    {
      return array();
    }
  }

  /**
   * Returns HTML code for a field.
   *
   * @param jRollerGeneratorConfigurationField $field The field
   *
   * @return string HTML code
   */
  public function renderField($field)
  {
      
    $html = $this->getColumnGetter($field->getName(), true);
    $form = $this->getFormObject();

    if ($renderer = $field->getRenderer())
    {
      $html = sprintf("$html ? call_user_func_array(%s, array_merge(array(%s), %s)) : '&nbsp;'", $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
    }
    else if ($field->isComponent())
    {
      return sprintf("get_component('%s', '%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $field->getName(), $this->getSingularName(), $this->getSingularName());
    }
    else if ($field->isPartial())
    {
      return sprintf("get_partial('%s', array('type' => 'list', '%s' => \$%s))", $field->getName(), $this->getSingularName(), $this->getSingularName());
    }
    else if ('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
    }
    else if ('Boolean' == $field->getType())
    {
      $html = sprintf("get_partial('%s/list_field_boolean', array('value' => %s))", $this->getModuleName(), $html);
    }
    else if ($field->isEditable())
    {
        $tmpClass       = $this->getModelClass();
        $modelClass     = New $tmpClass;
        $fieldWidget    = null;
        $fieldType      = null;
        $fieldListNew   = 'false';
        $fieldName      = null;

        //Check if the field exist on the table; if the field do not exist it's probably a foreign key.
        try {
            $fieldWidget    = $form->getWidget( $field->getName() );
            $fieldName      = $field->getName();
        }
        catch(Exception $e) {
            $fieldWidget    = null;
            $fieldName      = ucwords( $field->getName() );
        }

        //Check if the method 'getEditableListForXXXXAsArray exist on the modelClass
        try {
            $methodName = 'getEditableListFor'. ucwords($field->getName()) .'AsArray';
            $modelClass->$methodName();
            $fieldType  = 'list';
        }
        catch (Exception $e) {
            $fieldType      = null;
        }

        if( $fieldType == null )
        {
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

        //If the field is a list, we check if the new method exist
        if( $fieldType == 'list' )
        {
            try {
                $methodName = 'newEditableListFor'. ucwords($field->getName());
                $modelClass->$methodName();
                $fieldListNew   = 'true';
            }
            catch (Exception $e)
            {
                $fieldListNew   = 'false';
            }
        }
        
        return sprintf("get_partial('list_field_editable', array('name' => '%s', 'module_name' => '%s', 'type' => '%s', 'new' => %s, 'field' => \$%s))",  $fieldName, $this->getModuleName(), $fieldType, $fieldListNew, $this->getSingularName());
       
    }
    
    if ($field->isLink())
    {
      $html = sprintf("link_to(%s, '%s', \$%s)", $html, $this->getUrlForAction(($this->getExtra('show') == true)?'show':'edit'), $this->getSingularName());
    }

    return $html;
  }
}

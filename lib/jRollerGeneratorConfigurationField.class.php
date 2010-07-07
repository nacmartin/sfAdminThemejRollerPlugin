<?php

/**
 * Model generator field.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: jRollerGeneratorConfigurationField.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class jRollerGeneratorConfigurationField extends sfModelGeneratorConfigurationField
{
 
  /**
   * Returns true if the column is editable.
   *
   * @return boolean true if the column is editable, false otherwise
   */
  public function isEditable()
  {
    return isset($this->config['is_editable']) ? $this->config['is_editable'] : false;
  }

  /**
   * Sets or unsets the editable flag.
   *
   * @param Boolean $boolean true if the field is editable, false otherwise
   */
  public function setEditable($boolean)
  {
    $this->config['is_editable'] = $boolean;
    $this->config['type']        = 'editable';
  }

  static public function splitFieldWithFlag($field)
  {
    if (in_array($flag = $field[0], array('=', '_', '~', '$')))
    {
      $field = substr($field, 1);
    }
    else
    {
      $flag = null;
    }

    return array($field, $flag);
  }

  /**
   * Sets a flag.
   *
   * The flag can be =, _, ~ or $
   *
   * @param string $flag The flag
   */
  public function setFlag($flag)
  {
    if (null === $flag)
    {
      return;
    }

    switch ($flag)
    {
      case '=':
        $this->setLink(true);
        break;
      case '_':
        $this->setPartial(true);
        break;
      case '~':
        $this->setComponent(true);
        break;
      case '$':
        $this->setEditable(true);
          break;
      default:
        throw new InvalidArgumentException(sprintf('Flag "%s" does not exist.', $flag));
    }
  }

  /**
   * Gets the flag associated with the field.
   *
   * The flag will be
   *
   *   * = for a link
   *   * _ for a partial
   *   * ~ for a component
   *   * # for e editable field
   *
   * @return string The flag
   */
  public function getFlag()
  {
    if ($this->isLink())
    {
      return '=';
    }
    else if ($this->isPartial())
    {
      return '_';
    }
    else if ($this->isComponent())
    {
      return '~';
    }
    else if ($this->isEditable())
    {
      return '$';
    }

    return '';
  }
}

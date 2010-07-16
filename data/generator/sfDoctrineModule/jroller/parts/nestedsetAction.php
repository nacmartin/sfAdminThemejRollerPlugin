  public function executeBatchSavetreeorder(sfWebRequest $request)
  {
        $newparent = $request->getParameter('newparent');

        //manually validate newparent parameter

        //make list of all ids
        $ids = array();
        foreach ($newparent as $key => $val)
        {
            $ids[$key] = true;
            if( !empty($val) )  $ids[$val] = true;
        }
        $ids = array_keys($ids);

        //validate if all id's exist
        $validator = new sfValidatorDoctrineChoice(array('model' => '<?php echo $this->getModelClass() ?>', 'multiple' => true));
        try
        {
          // validate ids
          $ids = $validator->clean($ids);

          // the id's validate, now update the tree
          $count = 0;
          $flash = "";

          foreach ($newparent as $id => $parentId)
          {
            if (!empty($parentId))
            {
              $node     = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find($id);
              $parent   = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find($parentId);

              if (!$parent->getNode()->isDescendantOfOrEqualTo($node))
              {
                $node->getNode()->moveAsFirstChildOf($parent);
                $node->save();

                $count++;

                $flash .= "<br/>Moved '".$node['name']."' under '".$parent['name']."'.";
              }
            }
          }

          if ($count > 0)
          {
            $this->getUser()->setFlash('notice', sprintf("Tree order updated, moved %s item%s:".$flash, $count, ($count > 1 ? 's' : '')));
          }
          else
          {
            $this->getUser()->setFlash('error', "You must at least move one item to update the tree order");
          }
        }
        catch (sfValidatorError $e)
        {
          $this->getUser()->setFlash('error', 'Cannot update the tree order, maybe some item are deleted, try again');
        }

        $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
  }
<script language="javascript">
<!--
   jQuery(function($){

         var   element = $().find('#editable-[?php echo $name; ?]-[?php echo $field->id; ?]');

         element.change( function() {
                        jrollerEditableSet( $(this))
         });

         element.data('id',     [?php echo $field->id; ?]);
         element.data('name',   '[?php echo $name; ?]');
         element.data('BaseUrl',   '[?php echo $module_name; ?]');
         element.data('SetUrl',    'editableSet[?php echo ucwords($name); ?]');
         element.data('GetUrl',    'editableGet[?php echo ucwords($name); ?]');
         element.data('NewSetUrl', 'editableNewSet[?php echo ucwords($name); ?]');
         element.data('NewGetUrl', 'editableNewGet[?php echo ucwords($name); ?]');
         element.data('hasNew',   [?php if($new == true) echo 'true'; else echo 'false'; ?]);
         element.data('editType',  '[?php echo $type; ?]');
   });
-->
</script>
<div class="editable" id="editable-[?php echo $name; ?]-[?php echo $field->id; ?]" contentEditable="false">[?php echo $field->$name; ?]</div>
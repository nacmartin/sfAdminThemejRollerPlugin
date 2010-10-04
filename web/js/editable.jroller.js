/**
 * editableText plugin that uses contentEditable property (FF2 is not supported)
 * Project page - http://github.com/valums/editableText
 * Copyright (c) 2009 Andris Valums, http://valums.com
 * Licensed under the MIT license (http://valums.com/mit-license/)
 */
(function(){

    $.fn.editable = function(opt){

         var defaults = {
		newlinesEnabled :      false,
		changeEvent :          'change'
	};

        var options = $.extend(defaults, opt);

        this.each(function() {

            // Add jQuery methods to the element
            var editable   = $(this);
            editable.data('value', editable.html());

            // Create edit/save buttons
            var buttons    = $( "<div class='sf-admin-editable-field-toolbar'>" +
                                    "<a href='#' class='edit ui-icon ui-icon-pencil'></a>" +
                                    "<a href='#' class='save ui-icon ui-icon-document'></a>" +
                                    "<a href='#' class='cancel ui-icon ui-icon-trash'></a>" +
                                 "</div>")
				.insertAfter(editable);

            editable.data('btnSave', buttons.find('.save'));
            editable.data('btnEdit', buttons.find('.edit'));
            editable.data('btnCancel', buttons.find('.cancel'));

            // Save references and attach events
            editable.data('btnEdit').click(function() {
                 editable.editableClickEdit();
                 return false;
            });

            editable.data('btnSave').click(function() {
                 editable.editableClickSave();
                 return false;
            });

            editable.data('btnCancel').click(function() {
                 editable.editableClickCancel();
                 return false;
            });

            editable.editableBtnDefault();

            if (!options.newlinesEnabled){
                 // Prevents user from adding newlines to headers, links, etc.
                 editable.keypress(function(event){
                     // event is cancelled if enter is pressed
                     return event.which != 13;
                 });
            }
         });

         return $(this);
    }

    $.fn.editableBtnDefault = function(){

         $(this).data('btnSave').hide();
         $(this).data('btnCancel').hide();
         $(this).data('btnEdit').show();
         $(this).attr('contentEditable', false);
         $(this).parent().attr('style', '#E6D6D6');

    }

    $.fn.editableClickEdit = function() {

         if( $(this).data('editType') == 'text')
         {
               $(this).data('btnSave').show();
               $(this).data('btnCancel').show();
               $(this).data('btnEdit').hide();
               $(this).attr('contentEditable', true);
         }

         if( $(this).data('editType') == 'list')
         {
               $(this).data('btnCancel').show();
               $(this).data('btnEdit').hide();
               jrollerEditableGetList($(this));
         }

         $(this).parent().attr('style', 'background-color: #E6D6D6');
    }

    $.fn.editableClickCancel = function() {

         $(this).html( $(this).data('value') );
         $(this).editableBtnDefault();
    }

    $.fn.editableClickSave = function() {

         $(this).editableBtnDefault();
         $(this).trigger('change');
    }

    $.fn.insertList = function(field, datas, options){

         // définition des paramètres par défaut
         var defaults = {
              title: "",
              callback: null
          };

         // mélange des paramètres fournis et par défaut
         var opts = $.extend(defaults, options);

         function createList(f){

             // créer la première zone, affichant l'option sélectionnée
             var cell = $("<div class='dropdownCell'>" + opts.title + "</div>");

             // créer la seconde zone, affichant toutes les options
             var dropdown = $("<div class='dropdownPanel'></div>");

             if(field.data('hasNew') == true) {

                 dropdown.append($("<div class='dropdownItem dropdownNew'></div")
                          .click(onNew)
                          .attr("value", '0')
                          .append('New ..'));
             }

             for(var i = 0; i < datas.length; i++ )
             {
                 var classLabel     = "";
                 var classOpt       = " dropdownOpt";

                 if(datas[i].label == 'yes')
                 {
                     classLabel    = " dropdownLabel";
                 }

                 if(datas[i].select == 'no' || datas[i].id == 0 )
                 {
                     classOpt       = "";
                 }

                 dropdown.append($('<div class="dropdownItem' + classLabel + classOpt +'" value="' + datas[i].id + '">' + datas[i].name + '</div>'));
            }

            // on masque la zone déroulante
            dropdown.hide();
            $.data(cell, "visible", false);

            // on positionne l'évènement de déroulage de la liste
            cell.click(function(){
                // si la liste est déroulée
                if ($.data(cell, "visible")){
                    dropdown.slideUp("fast");
                    $.data(cell, "visible", false);
                }else{
                    dropdown.slideDown("fast");
                    $.data(cell, "visible", true);
                }
            });
            //$(this).remove();


            // on remplace le contenu HTML par notre liste personnalisée
            $(this).html("");
            $(this).append(cell);
            $(this).append(dropdown);
            
            $(".dropdownOpt").each(function(){$(this).click(onSelect);});
            $(".dropdownNew").each(function(){$(this).click(onNew);});
            $(".dropdownItem").each(function(){$(this).hover(function(){$(this).addClass("dropdownItemSelected");},
                                                              function(){$(this).removeClass("dropdownItemSelected");})});
        

            // fonction appelée à chaque sélection d'un élément
            function onSelect(){
                cell.html($(this).html());
                cell.attr("value", $(this).attr("value"));
                dropdown.slideUp("fast");

                $.data(cell, "visible", false);

                // appel d'une fonction personnalisée
                if (opts.callback)
                    opts.callback(field, $(this));
            }

            function onNew(){
                $(this).removeClass("dropdownOpt");

                var popup      =  $('<div><div id="dgpopup" contentEditable="true"></div></div>');

                popup.dialog( { modal:      true,
                                draggable:  false,
                                resizable:  false,
                                title:      'My Title',
                                buttons:    {'Cancel':  function(){$(this).dialog("close");},
                                            'Save':    function(){jrollerEditableNewSet(field, $(this));}
                                }
                });

                //popup.find('#dgpopup').html($(this).html());

                var addr       = field.data('BaseUrl') + "/" + field.data('NewGetUrl');
                var params     = {}
                params.id      = field.data('id');
                params.name    = field.data('name');

                $.get( addr, params, function(data){ popup.find('#dgpopup').html( data.label ); }, 'json');

                if( popup.find('#dgpopup').html() == '')
                {
                    popup.find('#dgpopup').html($(this).html());
                }
            }
        }
        
        // création d'une liste déroulante personnalisée pour tous les éléments de l'objet jQuery
        $(this).each(createList);

        // interface fluide
        return $(this);
    }

    $(document).ready(function(){
       $('.editable').editable();
    });

})();

    function jrollerEditableSet(field, value) {

         var params        = {}
         params.id         = field.data('id');
         params.name       = field.data('name');

         if(field.data('editType') == 'text')
         { params.value   = field.html(); }
         else
         { params.value   = value; }
         
         var addr          = field.data('BaseUrl') + "/" + field.data('SetUrl')

         $.post( addr, params, function(){field.data( 'value', params.value )});
         //$.ajax()
    }

    function jrollerEditableGetList(field){

         var params     = {}
         params.id      = field.data('id');
         params.name    = field.data('name');

         var addr       = field.data('BaseUrl') + "/" + field.data('GetUrl')
         $.get( addr, params, function(data){jrollerEditableInsertListOnSuccess(field, data)}, 'json');
    }

    function jrollerEditableInsertListOnSuccess(field, data){

         field.insertList(field, data, {title: "select" , callback:jrollerEditableListCallBack});
         //field.find('ul').customDropDown(field, {title: "select" , callback:jrollerEditableListCallBack});
    }

    function jrollerEditableListCallBack(field, datas){

         jrollerEditableSet( field, datas.attr('value') );

         field.html(datas.html());
         field.editableBtnDefault();
         field.data('value', datas.html());
    }

    function jrollerEditableNewSet(field, popup) {

       var addr         = field.data('BaseUrl') + "/" + field.data('NewSetUrl');

       var params       = {};
       params.id        = field.data('id');
       params.name      = field.data('name');
       params.value     = popup.find('#dgpopup').html();

       $.post(addr, params);

       popup.dialog("close");

       field.data('value', params.value);
       field.editableClickCancel();
   }





    
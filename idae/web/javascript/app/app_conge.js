
    $('body').on("dragstart", '[data-dragconge]', function (event, node) {
        $(node).style.opacity = '0.4'
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData('dragid', $(node).identify());

    }.bind(this));

    $('body').on("dragend", '[data-dragconge]', function (event, node) {

        if (this.dnd_successful) {
            $(node).style.opacity = '1';
           // $(node).style.background = "";
            // event.target.parentNode.removeChild(event.target);
        } else {
            $(node).style.opacity = '1';
            //$(node).style.background = "";
            // $(this.element).select('[bugchk]').invoke('setOpacity', '1');
        }
    }.bind(this));

    $('body').on('dragover', '[data-dropzone=conge]', function (event, node) {
        event.dataTransfer.dropEffect = "move";
        event.preventDefault();
        node.style.background = "#FC3";
        return false;
    });

    $('body').on('dragleave', '[data-dropzone=conge]', function (event, node) {
        event.preventDefault();
        node.style.background = "";
        return false;
    });

   //

    // planning
    $('body').on('drop', '[data-dropzone=conge]', function (event, node) {
        //
        if (!event.dataTransfer.getData('dragid')) return;
        node.style.background = "";
        this.dnd_successful = true;
        var dragid = event.dataTransfer.getData('dragid');
        event.preventDefault();
        var node_parent = $($(node).parentNode);
        //
        var heureDebut  = $(node).readAttribute('heuredebut') || '',
        dateDebut       = $(node).readAttribute('datedebut'),
        idconge         = $(dragid).readAttribute('data-idconge');
        // MAJ
	   // ajaxValidation('app_update', 'mdl/app/', 'table=conge&table_value=' + idconge + '&vars[dateDebutConge]=' + dateDebut)
        //
        $(dragid).writeAttribute({datedebut: dateDebut});
        //
        console.log($(dragid))
        //
	    $(dragid).clonePosition(node,{setWidth:false,setHeight:false});


    }.bind(this));
	//
    $('body').on('dblclick', '[data-dropconge]', function (event,element) {
        var heure = $(element).readAttribute('heuredebut');
        var dateDebut = $(element).readAttribute('datedebut');
        var idagent = $(element).readAttribute('data-idagent');
        ajaxMdl('app/app/app_create', 'Nouveau congé', 'table=conge&vars[heureDebutConge]=' + heure + '&vars[dateDebutConge]=' + dateDebut + '&vars[idagent]=' + idagent ); 
    })
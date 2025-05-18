$('body').on("dragstart", '[data-dragtache]', function (event, node) {
	$(node).style.opacity = '0.4'
	event.dataTransfer.effectAllowed = "move";
	event.dataTransfer.setData('dragid', $(node).identify());

}.bind(this));

$('body').on("dragend", '[data-dragtache]', function (event, node) {

	if (this.dnd_successful) {
		$(node).style.opacity = '1';
		$(node).style.background = "";
		// event.target.parentNode.removeChild(event.target);
	} else {
		$(node).style.opacity = '1';
		$(node).style.background = "";
		// $(this.element).select('[bugchk]').invoke('setOpacity', '1');
	}
}.bind(this));

$('body').on('dragover', '[data-droptache]', function (event, node) {
	event.dataTransfer.dropEffect = "move";
	event.preventDefault();
	node.style.background = "#FC3";
	return false;
});

$('body').on('dragleave', '[data-droptache]', function (event, node) {
	event.preventDefault();
	node.style.background = "";
	return false;
});

//

// planning
$('body').on('drop', '[data-droptache]', function (event, node) {
	// console.log(event.dataTransfer)
	//
	if (!event.dataTransfer.getData('dragid')) return;
	node.style.background = "";
	this.dnd_successful = true;
	var dragid = event.dataTransfer.getData('dragid');
	event.preventDefault();
	var node_parent = $($(node).parentNode);
	//
	var heure = $(node).readAttribute('heuredebut') || '',
		dateDebut = $(node).readAttribute('dropvalue'),
		idtache = $(dragid).readAttribute('data-table_value');
	// MAJ
	if ($(node).hasAttribute('heuredebut')) {
		ajaxValidation('app_update', 'mdl/app/', 'table=tache&table_value=' + idtache + '&vars[dateDebutTache]=' + dateDebut + '&vars[heureDebutTache]=' + heure)
	} else {
		ajaxValidation('app_update', 'mdl/app/', 'table=tache&table_value=' + idtache + '&vars[dateDebutTache]=' + dateDebut)
	}
	//
	$(dragid).writeAttribute({heuredebut: heure, datedebut: dateDebut});
	//
	if ($(node).readAttribute('data-droptache') == 'rebound') {
		if ($$('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]').size() != 0) {
			// console.log($$('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]'));
			$$('[data-droptache="dropzone"][dropvalue="' + dateDebut + '"]').first().insert($(dragid));
		} else {
			$(dragid).remove();
		}
	} else {
		// insertion en place
		node.insert($(dragid));
		$(dragid).style.opacity = '1';
	}
	//
	$(node_parent).fire('dom:resizetache');
	$(node).fire('dom:resizetache');

}.bind(this));

$('body').on('dblclick', '[data-droptache]', function (event, element) {
	var heure = $(element).readAttribute('heuredebut');
	var dateDebut = $(element).readAttribute('dropvalue');
	ajaxMdl('app/app/app_create', 'Nouvelle tache', 'table=tache&vars[heureDebutTache]=' + heure + '&vars[dateDebutTache]=' + dateDebut);
})

$('body').on('dom:resizetache', '[data-droptache]', function (event, elemnt) {
	var element = elemnt;
	if (element.hasClassName('evenement') || element.hasClassName('caseMois')) return;
	$(element).select('.dyntache').each(function (node) {
		var att = $(node).readAttribute('data-heureDebut');
		var size = $(element).select('.dyntache[data-heureDebut="' + att + '"]').size()
		var aa = 0;

		$(element).select('.dyntache[data-heureDebut="' + att + '"]').each(function (renode) {
			width = $(renode).up().getWidth() / size
			if (size == 1) {
				margin = 0;
			} else {
				margin = (width * aa)
			}
			$(renode).setStyle({'width': width + 'px', 'marginLeft': (margin) + 'px'});
			$(renode).addClassName(att)
			aa++;
		})

	}.bind(this));
	var count_z = 0;
	$(element).select('.dyntache').sortBy(function (s) {
		return $(s).offsetTop;
	}).each(function (w) {
		count_z++;
		$(w).setStyle({zIndex: (count_z)});
	})
});
//


<?php
/**
 * Created by PhpStorm.
 * User: Mydde
 * Date: 17/12/14
 * Time: 12:28
 */
?>
<div class="blanc">
<div id="mytable1" style="position:relative; width: 100%; height: 300px"></div></div>

<script>



	var tableModel = {
		options : {
			width: '100%',
			title: 'JAW Motors Inventory',
			addSettingBehavior : false,
			pager: {
				pageParameter : 'page'
			},
			onCellBlur : function(element, value, x, y, id) {
				//alert(value);
			},
			toolbar : {
				elements: [MY.TableGrid.ADD_BTN, MY.TableGrid.DEL_BTN, MY.TableGrid.SAVE_BTN],
				onSave: function() {
					alert('on save handler');
				},
				onAdd: function() {
					alert('on add handler');
				},
				onDelete: function() {
					alert('on delete handler');
				}
			},
			rowClass : function(rowIdx) {
				var className = '';
				if (rowIdx % 2 == 0) {
					className = 'hightlight';
				}
				return className;
			}
		},
		columnModel : [
			{
				id : 'carId',
				title : 'Id',
				width : 30,
				sortable: false,
				editor: new MY.TableGrid.CellCheckbox({
					selectable : true
				})
			},
			{
				id: 'generalInfo',
				title: 'General Info'
			},
			{
				id : 'price',
				title : 'Price',
				width : 70,
				type: 'number'
			},
			{
				id : 'dateAcquired',
				title : 'Date acquired',
				width : 120
			},
			{
				id : 'origCountry',
				title : 'Origin Country',
				width : 100
			}
		],
		url: 'get_all_cars.php'
	};

	tableGrid1 = new MY.TableGrid(tableModel);
	tableGrid1.render($('mytable1'));


</script>
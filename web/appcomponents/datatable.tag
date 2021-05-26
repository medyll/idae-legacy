



<datatable>
	<div style="height:100%;overflow:auto;">
		<table class="blanc explorer" data-uniqid="{uniqid}">
			<thead>
				<tr>
					<td each={head in header }>
						<div style="position:sticky;top:0;width:100%;">
							<a>{head.title || head.field_name}</a>
						</div>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr each={ row in datas } data-table="{table}" data-table_value="{row['id'+table]}"
				    data-contextual="table={table}&table_value={row['id'+table]}">
					<td each={ head in header } data-field_name="{head.field_name}"
					    data-field_name_raw="{head.field_name_raw}">{row[head.field_name]} <raw content="{head.field_name}"/>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<script>


		this.table = opts.table || 'produit'
		this.uniqid = uniqid();
		this.options = opts || [];
		this.vars = opts.vars || [];

		this.header = window.APP.APPSCHEMES[this.table].columnModel

		objs = {
			stream_to: this.uniqid,
			table: this.options.table,
			groupBy: this.options.groupBy,
			piece: 'query', vars: this.vars
		};

		get_data('json_data_table', objs, function (err) {
			console.log('err', err);
		}).then(function (res) {
			console.log('res', res);
		}.bind(this));

		this.datas = []

		this.on('mount', function () {
			riot.observable();
			$(this.root.querySelector('table')).on('dom:stream_chunk', function (event) {
				var res_tmp = event.memo;
				var data = window.register_stream[res_tmp]['data'];
				var data_main = data['data_main'];
				data_main.each(function (data) {
					this.datas.push(data.html);
				}.bind(this));
				this.update();
			}.bind(this))
		})
	</script>
	<style scoped>
	</style>
</datatable>
<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 15/10/2016
	 * Time: 22:44
	 */

	namespace Idae;

	class AppStat extends \App {

		public $table;
		public $Table;
		public $name_id;
		public $chart_count_field;
		public $type_periodicite;
		public $type_stat;
		public $type_date;
		public $vars;
		public $arr_type = ['valeur', 'prix_precis', 'prix', 'pourcentage', 'heure'];


		public function red($table="") {

			$this->table             = $table;
			$this->APP_SCH           = $this->appscheme;
			$this->APP_SCH_TY        = $this->appscheme_field_type;
			$this->APP_SCH_FIELD     = $this->appscheme_field;
			$this->APP_SCH_HAS_FIELD = $this->appscheme_has_field;


		}

		private function doit() {
			$ARR_FIELD_DATE = $this->APP_SCH_FIELD->distinct('idappscheme_field', ['codeAppscheme_field_type' => 'date']);
			$ARR_HAS        = $this->APP_SCH_HAS_FIELD->distinct('idappscheme_field', ['codeAppscheme' => $this->table, 'idappscheme_field' => ['$in' => $ARR_FIELD_DATE]]);
			$RS_DATE_FIELD  = $this->APP_SCH_FIELD->find(['idappscheme_field' => ['$in' => $ARR_HAS]])->sort(['ordreAppscheme_field' => 1]);

			while ($ARR_FIELD = $RS_DATE_FIELD->getNext()) {
				$codeAppscheme_field = $ARR_FIELD['codeAppscheme_field'];
				if ($this->has_field($codeAppscheme_field)) {
					return $codeAppscheme_field;
				}
			}
		}

	}
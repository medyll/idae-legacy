<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 07/09/2015
	 * Time: 19:43
	 */

	/***************************************************************************
	 *  description: gestion des sessions par la bdd
	 *                            -------------------
	 *   copyright        : F_D_V copyright creative commmon cc by-no :
	 *                     pas d'utilisation commerciale autoris�e, droit de modification, l'auteur doit �tre cit�
	 *                     pour plus d'information http://creativecommons.org/licenses/by-nc/2.0/fr/
	 ****************************************************************************/
	class Session {
		public $session_time = 14400;// 4 heures
		public $session = array();
		private $conn;
		private $bdd;

		public function __construct () {
			//
			 
		}

		public function open ()//pour l'ouverture
		{
			$this->conn =  new MongoClient('mongodb://admin:gwetme2011@127.0.0.1');//on se connecte a la bdd
			$sitebase_app   = 'idaenext_sitebase_session';
			$this->bdd     = $this->conn->$sitebase_app->session; //on s�lectionne la base de donn�es
			// $this->bdd->insert(['red'=>'cool']);
			$this->gc();//on appelle la fonction gc
			return $this->bdd;
		}

		public function read ($sid)//lecture
		{

			$sid = stripslashes($sid);
			$arr = $this->bdd->findOne(['sess_id'=>$sid]);

			if ( empty($arr['sess_id']) ) {
				return FALSE;
			} else {
				return $arr['sess_datas'];
			}// on retourne la valeur de sess_datas
		}

		public function write ($sid , $data)//�criture
		{
			$expire = intval(time() + $this->session_time);//calcul de l'expiration de la session
			// $this->bdd->update(['sess_id'=>$sid],['$set'=>['sess_datas'=>$data],'sess_expire'=>$expire],['upsert'=>true]);

			$arr = $this->bdd->findOne(['sess_id'=>$sid]);

			if ( empty($arr['sess_id']) ) {
				$this->bdd->insert(['sess_id'=>$sid,'sess_datas'=>$data,'sess_expire'=>$expire]);
			} else {
				$this->bdd->update(['sess_id'=>$sid],['$set'=>['sess_datas'=>$data],'sess_expire'=>$expire],['upsert'=>true]);
			}
			return true;
		}

		public function close ()//fermeture
		{
			$this->conn->close();//on ferme la bdd
		}

		public function destroy ($sid)//destruction
		{
			$res = $this->bdd->remove(['sess_id'=>$sid]);

			return $res;
		}

		public function gc ()//nettoyage
		{
			if($this->bdd){
				$res = $this->bdd->remove(['sess_expire'=>['lt'=>time()- $this-> session_time]]);

				return true;
			}

		}

	}//fin de la classe

	ini_set('session.save_handler' , 'user');//on d�finit l'utilisation des sessions en personnel
	$session = new Session();//on d�clare la classe
	session_set_save_handler(array( $session , 'open' ) , array( $session , 'close' ) , array( $session , 'read' ) , array( $session , 'write' ) , array( $session , 'destroy' ) , array( $session ,'gc' ));//on pr�cise les m�thodes � employer pour les sessions
	// session_start();//on d�marre la session



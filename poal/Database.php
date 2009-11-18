<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

require_once("./Config.php");
require_once("./Acudit.php");
require_once("./Categoria.php");
class Database {
	public $m_db;
	function Connectar() {
		global $poalDbUser,$poalDbServer,$poalDbPassword;
		if($this->m_db) return $this->m_db;
		else {
			$this->m_db=mysql_connect($poalDbServer,$poalDbUser,$poalDbPassword);
			if(!$this->m_db) return false;
			$this->m_db=mysql_select_db("poal");
			if(!$this->m_db) return false;
			return $this->m_db;
		}
	}

	function SqlGetAcudits($categories,$cerca) {
		if($categories) {
			$sql .=" $u and (";
			$u = "";
			foreach($categories as $c) {
				$sql.=" $u categoria='$c' ";
				$u = "or";
			}
			$sql .= ")";
		}
		$sql .= " order by a.data desc";
		return $sql;
	}


	function GetAcudits($categories,$cerca,$pagina) {
		$usuari = $_SESSION["usuari"];
		$offset = $usuari->m_acudits_per_pagina * ($pagina-1);	
		$acudits = array();
		#$sql = "select distinct a.id,u.alias,a.text,a.data from acudits a, acudit_categoria ac, usuaris u where a.id=ac.id_acudit and a.autor=u.usuari ".
		$sql = "select distinct a.id,u.alias,a.text,a.data from acudits a left join acudit_categoria ac on ac.id_acudit=a.id, usuaris u where a.autor=u.usuari ".
			$this->SqlGetAcudits($categories,$cerca)." limit $offset, $usuari->m_acudits_per_pagina";
		$query = mysql_query($sql);
		while($q = mysql_fetch_array($query)) {
			$a = new Acudit($q["id"],$q["alias"],$q["text"],date("d-m-Y",$q["data"]));
			$a->m_categories = $this->GetCategoriesAcudit($q["id"]);
			array_push($acudits,$a);
		}
		return $acudits;
	}

	function ComptarAcudits($categories,$cerca) {
		$sql = "select count(*) from acudits a, acudit_categoria ac where a.id=ac.id_acudit".
			$this->SqlGetAcudits($categories,$cerca);
		$count =  mysql_result(mysql_query($sql),0);
		return $count;
	}


	function GetCategories() {
		$categories = array();
		$query = mysql_query("select ac.categoria,count(*) as num, c.escollible from acudit_categoria ac, categories c where ac.categoria=c.nom group by ac.categoria,c.escollible");
		while($q = mysql_fetch_array($query)) {
			$c = new Categoria($q["categoria"],$q["num"]);
			$c->m_es_escollible = $q["escollible"];
			array_push($categories,$c);
		}
		return $categories;
	}
	
	function GetCategoriesAcudit($id_acudit) {
		$categories = array();
		$query = mysql_query("select categoria from acudit_categoria where id_acudit=$id_acudit");
		while($q = mysql_fetch_array($query)) {
			$c = new Categoria($q["categoria"],0);
			array_push($categories,$c);
		}
		if (count($categories) == 0) {
			$categories = array(new Categoria("(Inclassificables)", 0));
		}
		return $categories;
	}

	function ValidarUsuari($usuari,$clau) {
		$this->Connectar();
		$query = mysql_query("select clau from usuaris where usuari='".mysql_escape_string($usuari)."'");
		if(!$query) return false;
		$clau_usuari = mysql_result($query,0);
		return (!strcmp($clau,$clau_usuari));
	}

	function GetUsuari($usuari) {
		$query = mysql_query("select es_admin,alias,email,acudits_per_pagina,pot_afegir from usuaris where usuari='$usuari'");
		$u = new Usuari();
		$r = mysql_fetch_array($query);
		if(!$r) return $u;
		$u->m_usuari = $usuari;
		$u->m_es_admin = $r["es_admin"];
		$u->m_alias = $r["alias"];
		$u->m_email = $r["email"];
		$u->m_acudits_per_pagina = $r["acudits_per_pagina"];
		$u->m_pot_afegir = $r["pot_afegir"];
		$query = mysql_query("select count(*) from acudits where autor='$u->m_usuari'");
		$u->m_nacudits = mysql_result($query,0);
		return $u;
	}

	function AfegirAcudit($text,$categories) {
		$u = $_SESSION["usuari"];
		$data = time();
		$text = $this->chr2html($text);
		$query = mysql_query("insert into acudits set usuari='$u->m_usuari',autor='$u->m_usuari',text='$text',data='$data'");
		$lastid=mysql_insert_id();
		if(!$categories) array_push($categories,"(Inclassificables)");
		foreach($categories as $c) {
			mysql_query("insert into acudit_categoria set id_acudit='$lastid',categoria='$c'");	
		}
	}

	function EsborrarAcudit($id){
		$query=mysql_query("delete from acudits where id='$id'");
		mysql_query($query);
	}
	
	function chr2html($text)
	{       
	        $text=str_replace("&","&amp;",$text);
		        $text=str_replace('"',"&quot;",$text);
			        $text=str_replace("'","&#039;",$text);
				        $text=str_replace("<","&lt;",$text);
					        $text=str_replace(">","&gt;",$text);
						        $text=str_replace(chr(13),"<br>",$text);
							        return $text;
								}
								
}


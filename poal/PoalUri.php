<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

class PoalUri {
	/*Tipus de pàgina:
		Acudits
		Cerca
	*/

	var $m_uri;
	var $m_pagina;
	var $m_tipus_pagina;
	var $m_categories;
	var $m_cerca;
	#var $m_tipus_possibles=array("Categories","Acudits","Cerca");
	function PoalUri($uri) {
		$this->m_uri = urldecode($uri);
		$this->Update();
	}

	//Actualitza les variables m_pagina, m_tipus_pagina, etc. segons $m_uri
	function Update() {
		$this->m_categories = array();
		$this->m_pagina = 1;
		$a = array();
		$tmp = explode("/",$this->m_uri);
		foreach($tmp as $tros) {
			if($tros!="") array_push($a,$tros);
		}
		$this->m_tipus_pagina = urldecode(array_shift($a));
		if($a[0]=="afegir") $this->m_tipus_pagina="acudits:afegir";
		if($a[0]=="esborrar") $this->m_tipus_pagina="acudits:esborrar";
		if(!strcmp($this->m_tìpus_pagina,"acudits") || 1) {
			if(preg_match("/\/pag([0-9]+)\//",$this->m_uri,$matches)) {
				$this->m_pagina=$matches[1];
			}
			while($tros = array_shift($a)) {
				if($tros=="categories" || $tros=="categoria") {
					array_push($this->m_categories, array_shift($a));
				}
				else if($tros=="buscar" || $tros=="cercar" || $tros == "busca" || $tros=="cerca" || $tros=="trobar" || $tros=="troba") {
					//$this->m_tipus_pagina="Cerca";
					preg_match("/ +/",array_shift($a),$this->m_cerca);
				}
			}
		}

	}
	
	//Obte la uri per a la pagina $p de la uri actual
	function GetPagina($p) {
		global $poalBase;
		$poalURL = '/';
		$url = $poalURL.$this->m_tipus_pagina."/pag$p/";
		if($this->m_categories) {
			$url.="categories/";
			$categories = "";
			foreach($this->m_categories as $c) {
				$categories.="$c ";
			}
			$url.=urlencode(trim($categories));
		}
		if($this->m_cerca) {
			$url.="cercar/";
			foreach($this->m_cercar as $c) {
				$url.=urlencode("$c ");
			}
		}
		return $url;
	}

	function GetPaginaArrel() {
		global $poalURL;
		$url = $poalURL.$this->m_tipus_pagina;
		return $url;
	}
	
	function GetPaginaActual() {
		return $this->GetPagina($this->m_pagina);
	}
}

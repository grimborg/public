<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/
   
class Acudit {
	var $m_id,$m_autor,$m_text,$m_data,$m_categories;
	function Acudit($id,$autor,$text,$data=null) {
		$this->m_id=$id;
		$this->m_autor=$autor;
		$this->m_data=$data;
		if(preg_match("/([^\[]+)(\[[^\]].*\])/",$text,$matches)) {
			$this->m_text=$matches[1];
			$this->m_gracia=$matches[2];
		}
		else {
			$this->m_text=$text;
			$this->m_gracia="";
		}
		$this->m_categories=array();
	}
	function AfegirCategoria($categoria) {
		array_push($this->m_categories,$categoria);
	}
	function GetText() {
		return $this->m_text;
	}
}

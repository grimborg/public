<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

require_once("./Config.php");
class Categoria {
	var $m_nom,$m_nacudits,$m_es_escollible;
	function Categoria($nom,$nacudits) {
		$this->m_nom = $nom;
		$this->m_nacudits = $nacudits;
		$this->m_es_escollible = true;
	}	
	function GetURL() {
		global $poalURL,$poalBase;
		return $poalURL.$poalBase."categories/".urlencode($this->m_nom);
	}
		
}

<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

require_once("./Acudit.php");
require_once("./Skin.php");
require_once("./Database.php");
class Pagina {
	var $m_titol,$m_acudits,$m_total_acudits,$m_text,$m_tipus,$m_uri;
	function Escriure() {
		switch($this->m_tipus) {
			case "acudits":
				$this->EscriureAcudits("");
				break;
			case "acudits:afegir":
				$this->EscriureAcudits();
			default:
				print "Unknown page type: $this->m_tipus";
		}
	}
	function EscriureAcudits() {
		$u = $_SESSION["usuari"];
		$pagina = 1;
		escriure_pagina_acudits($this->m_uri,$this->m_text,$this->m_acudits,$this->m_categories, $this->m_total_acudits,$u);
	}
}

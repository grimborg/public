<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

class Usuari {
	var $m_esta_identificat,$m_usuari,$m_es_admin,$m_acudits_per_pagina,$m_pot_afegir,$m_email,$m_alias,$m_nacudits;
	function Usuari() {
		global $poalUsuariAnonim,$poalAcuditsPerPagina,$poalAnonimPotAfegir;
		$this->m_esta_identificat=false;
		$this->m_usuari=$poalUsuariAnonim;
		$this->m_alias=$poalUsuariAnonim;
		$this->m_es_admin=false;
		$this->m_acudits_per_pagina=$poalAcuditsPerPagina;
		$this->m_pot_afegir=$poalAnonimPotAfegir;
		$this->m_nacudits = 0;
	}
}

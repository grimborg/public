<?
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

require_once("./PoalUri.php");
require_once("./Pagina.php");
require_once("./Usuari.php");
require_once("./Database.php");
session_start();

define("_BBCLONE_DIR","/usr/share/bbclone/");
define("COUNTER",_BBCLONE_DIR."mark_page.php");
define("_BBC_PAGE_NAME","Poal");
if(is_readable(COUNTER)) include_once(COUNTER);

$db = new Database();
registrar_usuari($db);
$uri = processar_uri();
$pagina = generar_pagina($uri);
$pagina->Escriure();


function registrar_usuari($db) {
	if(isset($_POST["tancar_sessio"])) $_SESSION["usuari"] = new Usuari();
	else {
		if(isset($_POST["usuari"]) && isset($_POST["clau"])) {
			if($db->ValidarUsuari($_POST["usuari"],$_POST["clau"])) {
				$u = $db->GetUsuari($_POST["usuari"]);
				$u->m_esta_identificat = true;
				$_SESSION["usuari"] = $u;
			}
		} else if(!isset($_SESSION["usuari"])) {
			$_SESSION["usuari"] = new Usuari();
		}
	}
}

function processar_uri() {
	$uri=urldecode($_SERVER["REQUEST_URI"]);
	if(!preg_match("/\/$/",$uri)) $uri.="/";
	if(!preg_match("/^\/acudits\//",$uri)) {
		header("Location: /acudits/");
		exit;
	}
	return new PoalUri($uri);
}

function generar_pagina($uri) {
	$db = new Database();
	$db->Connectar();
	$p = new Pagina();
	$p->m_uri = $uri;
	$p->m_tipus = $uri->m_tipus_pagina;
	$usuari = $_SESSION["usuari"];
	if(!strcmp($uri->m_tipus_pagina,"acudits")) {
		$p->m_acudits = $db->GetAcudits($uri->m_categories,$uri->m_cerca,$uri->m_pagina);
		$p->m_total_acudits = $db->ComptarAcudits($uri->m_categories,$uri->m_cerca);
		$p->m_categories = $db->GetCategories();
	}
	else if(!strcmp($uri->m_tipus_pagina,"acudits:afegir")) {
		$cat = $_POST["cat"];
		$text = $_POST["text"];
		if($usuari->m_pot_afegir) {
			$db->AfegirAcudit($text,$cat);
			$acudit = new Acudit(0,$usuari->m_alias,$text);
			$p->m_acudits = array();
			array_push($p->m_acudits,$acudit);
			$p->m_categories = $db->GetCategories();
			$p->m_text = "Acudit afegit!";
		}
		else {
			$p->m_text = "El teu usuari no te permis per afegir acudits.";
			$p->m_acudits = array();
			$p->m_categories = $db->GetCategories();
		}
	}
	else if(!strcmp($uri->m_tipus_pagina,"acudits:esborrar")) {
		print "esborrar acudits<br>";
		if($usuari->m_esta_identificat) {
			$esborrar = $_POST["esborrar"];
			foreach($esborrar as $id) {
				print "esborro l'acudit $id<br>";
				$db->EsborrarAcudit($id);
			}
		}
		else print "usuari no identificat";
	}
	return $p;
}

?>

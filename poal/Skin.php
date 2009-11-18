<?php
/* Aquest arxiu és part del Poal.
   Autor: Òscar Álvarez Vilaplana (oscar@poal.org)
   Llicència: GPL versió 2

   El codi del Poal (incloent-hi aquest arxiu) és programari lliure. Pots copiar-lo, modificar-lo, distribuir-lo i vendre'l d'acord amb els termes de la llicència GPL versió 2.

   La llicència és a http://www.gnu.org/licenses/gpl.txt
*/

function escriure_pagina_acudits($uri,$text,$acudits,$categories,$nacudits,$usuari) {
	global $poalURL,$poalLlicencia;
	$titol="Poal";
	$interval_pagines = 4;
	$num_pagines = round($nacudits / $usuari->m_acudits_per_pagina);
	//if($nacudits % $usuari->m_acudits_per_pagina) $num_pagines++;
	$pag_final = $uri->m_pagina + $interval_pagines;
	$pag_inicial = $uri->m_pagina - $interval_pagines;
	if ($pag_inicial < 1) {
		$pag_final += (1-$pag_inicial);
		$pag_inicial = 1;
	}
	if ($pag_final > $num_pagines) {
		$pag_inicial-=($pag_final-$num_pagines);
		if($pag_inicial<1) $pag_inicial=1;
		$pag_final = $num_pagines;
	}
	if ($pag_inicial==1) $nom_pag_inicial=$pag_inicial;
	else $nom_pag_inicial="...";
	if ($pag_final==$num_pagines) $nom_pag_final=$pag_final;
	else $nom_pag_final="...";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title><?=$titol?></title>
		<style type="text/css" media="screen">
			@import url( /style.css );
		</style>
                <link rel="icon" type="image/png" href="http://poal.org/favicon.ico" />
		<link rel="SHORTCUT ICON" href="http://poal.org/favicon.ico"/>
	</head>
	<body>
		<div id="page">
			<div id="header"><a href="/"><img src="/poal.gif"/></a></div>
			<div id="content" class="narrowcolumn"><?=$text?>
				<div class="pagines">
					<ul>
					<?for($i=$pag_inicial;$i<=$pag_final;$i++) {
						if($i == $pag_inicial) $nom = $nom_pag_inicial;
						else if($i == $pag_final) $nom = $nom_pag_final;
						else $nom = $i;
						if($i == $uri->m_pagina) {?>
							<li class="num_pagina"><?=$nom?></li>
						<?} elseif ($i == $uri->m_pagina - 1) {?>
							<li class="num_pagina"><a rel="previous" href="<?=$uri->GetPagina($i)?>"><?=$nom?></a></li>
						<?} elseif ($i == $uri->m_pagina + 1) {?>
							<li class="num_pagina"><a rel="next" href="<?=$uri->GetPagina($i)?>"><?=$nom?></a></li>
						<?} else {?>
							<li class="num_pagina"><a href="<?=$uri->GetPagina($i)?>"><?=$nom?></a></li>
						<?}?>
					<?}?>
					</ul>
				</div>
				<form action="http://www.poal.org/acudits/esborrar" method="post">
				<ul><?foreach($acudits as $acudit) {?>
					<li class="acudit">
						<div class="acudit">
							<div class="acudit_titol">
								<div class="acudit_autor"><?=$acudit->m_autor?></div>
								<div class="acudit_data"><?=$acudit->m_data?></div>
								<div class="acudit_categoria">
									<ul>
									<?foreach($acudit->m_categories as $categoria) {?>
										<li><a href="<?=$categoria->GetURL()?>"><?=$categoria->m_nom?></a></li><?}?>
									</ul>
								</div>
							</div>
							<p class="acudit_text"><?=$acudit->m_text?></p>
							<p class="acudit_gracia"><?=$acudit->m_gracia?></p>
							<?if($usuari->m_esta_identificat) {?>
								<p><input type="checkbox" name="esborrar[]" value="<?=$acudit->m_id?>">Esborrar</input></p>
							<?}?>
						</div>
					</li><?}?>
				</ul>
				<?if($usuari->m_esta_identificat) {?>
				<input type="submit" value="Esborrar"/>
				<?}?>
				</form>

				<div class="pagines">
					<ul>
					<?for($i=$pag_inicial;$i<=$pag_final;$i++) {
						if($i == $pag_inicial) $nom = $nom_pag_inicial;
						else if($i == $pag_final) $nom = $nom_pag_final;
						else $nom = $i;
						if($i == $uri->m_pagina) {?>
							<li class="num_pagina"><?=$nom?></li>
						<?} else {?>
							<li class="num_pagina"><a href="<?=$uri->GetPagina($i)?>"><?=$nom?></a></li>
						<?}?>
					<?}?>
					</ul>
				</div>

			<?if($usuari->m_pot_afegir) {?>
				<a name="afegir_acudits"></a>
				<div class="afegir_acudits">
					<h2>Afegir un acudit</h2>
					<form action="http://www.poal.org/acudits/afegir" method="post">
						<table>
							<tr>
								<td><textarea rows='11' cols='40' name='text'></textarea></td>
								<td>
									<select name="cat[]" multiple="multiple" size="11">
										<?foreach($categories as $c)
											if ($c->m_es_escollible) {?>
											<option name="<?urlencode($c->m_nom)?>"><?=$c->m_nom?></option>
										<?}?>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2"><input type="submit" value="Enviar!"></td>
							</tr>
						</table>
					</form>
				</div>
			<?}?>
			</div>
			<div id="sidebar">
				<ul>
					<li>
						<h2>Usuari</h2>
						<ul>
							<li class="detalls_usuari"><?=$usuari->m_alias?></li>
							<li class="detalls_usuari">Has creat <?=$usuari->m_nacudits?> acudits</li>
							<?if($usuari->m_pot_afegir) {?>
								<li class="detalls_usuari"><a href="#afegir_acudits">Afegir un acudit!</a></li>

							<?} else {?>
								<li class="detalls_usuari">No pots afegir acudits.</li>
							<?}?>
						</ul>
					
					<li>
						<h2>Cercador</h2>
						<form method="post" action="nova.php">
							<input type="text" size="17" name="s">
							<input type="submit" value="»">
						</form>
					</li>
					<li>
						<h2>Categories</h2>
						<ul><?foreach($categories as $categoria) {?>
							<li><a href="<?=$categoria->GetURL()?>"><?=$categoria->m_nom?></a> (<?=$categoria->m_nacudits?>)</li>
						<?}?>
							<li><a href="<?=$uri->GetPaginaArrel()?>">No tinc criteri!</a></li>
						</ul>
					</li>
					<li>
						<h2>Comentaris</h2>
						<ul>
							<li><a href="http://elpernil.info/wiki/Poal">Quant a el Poal</a></li>
							<li><a href="http://elpernil.info/wiki/Poal:Programari">El programari del Poal</a></li>
							<li><a href="http://elpernil.info/wiki/Poal:Comentaris">Deixa'ns el teu comentari!</a></li>
						</ul>
					</li>
					<?if ($usuari->m_esta_identificat) {?>
						<li>
						<form method="post" action="<?=$uri->GetPaginaActual()?>">
							<input type="submit" name="tancar_sessio" value="Tancar sessió"/>
						</form>
						</li>	
					<?}?>
					<?if (!$usuari->m_esta_identificat) {?>
					
						<li>
							<h2>Identificació</h2>
							<form method="post" action="<?=$uri->GetPaginaActual()?>">
								<table>
									<tr>
										<td>Usuari</td>
										<td><input type="text" name="usuari" size="10"></td>
									</tr>
									<tr>
										<td>Clau</td>
										<td><input type="password" name="clau" size="10"></td>
									</tr>
									<tr>
										<td colspan="2"><input type="submit" value="Identifica'm"></td>
									</tr>
								</table>
							</form>
						</li>
					<?}?>
					<li>
						<h2>Enllaços</h2>
						<ul>
							<li><a href="http://www.elpernil.info/">elpernil.info</a></li>
							<li><a href="http://ga.lifard.eu/">ga.lifard.eu</a></li>
						</ul>
					</li>
				</ul>
			</div>

			<div id="footer"><p><?=$poalLlicencia?></p></div>
		</div>
	</body>
</html>
<?}?>

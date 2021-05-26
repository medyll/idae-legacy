<?php
// source: /var/www/idaertys_preprod.mydde.fr/web/tpl/app//appsite/item.latte

use Latte\Runtime as LR;

class Templatefc028b0852 extends Latte\Runtime\Template
{
	public $blocks = [
		'entete_fiche' => 'blockEntete_fiche',
		'entete_fiche_detail' => 'blockEntete_fiche_detail',
		'fiche_mini' => 'blockFiche_mini',
		'fiche_micro' => 'blockFiche_micro',
		'fiche_image' => 'blockFiche_image',
		'fiche_detail_fk' => 'blockFiche_detail_fk',
		'fiche_produit_small' => 'blockFiche_produit_small',
		'fiche_rfk' => 'blockFiche_rfk',
		'liste' => 'blockListe',
		'liste_produit_small' => 'blockListe_produit_small',
		'liste_produit' => 'blockListe_produit',
		'liste_micro' => 'blockListe_micro',
		'liste_mini' => 'blockListe_mini',
		'liste_image' => 'blockListe_image',
		'cartouche_default' => 'blockCartouche_default',
		'cartouche_carrousel' => 'blockCartouche_carrousel',
		'calendar' => 'blockCalendar',
	];

	public $blockTypes = [
		'entete_fiche' => 'html',
		'entete_fiche_detail' => 'html',
		'fiche_mini' => 'html',
		'fiche_micro' => 'html',
		'fiche_image' => 'html',
		'fiche_detail_fk' => 'html',
		'fiche_produit_small' => 'html',
		'fiche_rfk' => 'html',
		'liste' => 'html',
		'liste_produit_small' => 'html',
		'liste_produit' => 'html',
		'liste_micro' => 'html',
		'liste_mini' => 'html',
		'liste_image' => 'html',
		'cartouche_default' => 'html',
		'cartouche_carrousel' => 'html',
		'calendar' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
?>



<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['item'])) trigger_error('Variable $item overwritten in foreach on line 23, 62');
		if (isset($this->params['row'])) trigger_error('Variable $row overwritten in foreach on line 187, 206, 211, 216, 221, 226, 296');
		if (isset($this->params['row2'])) trigger_error('Variable $row2 overwritten in foreach on line 299');
		
	}


	function blockEntete_fiche($_args)
	{
		extract($_args);
?>

	<div class="padding_more">entete_fiche
		<div class="flex_h flex_align_top">
			<div class="padding_more">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($img['img_small']['href'])) /* line 6 */ ?>">
			</div>
			<div class="padding_more flex_main">
				<h1><?php echo LR\Filters::escapeHtmlText($nom) /* line 9 */ ?></h1>

<?php
		if ($petitNom) {
			?>				<h2 ><?php echo LR\Filters::escapeHtmlText($petitNom) /* line 11 */ ?></h2>
<?php
		}
		if ($atout) {
			?>				<h5> Atout : <?php echo LR\Filters::escapeHtmlText($atout) /* line 12 */ ?></h5>
<?php
		}
?>

				<div class="margin_more padding_more bordert   flex_h flex_align_middle">
					<div class="  aligncenter borderr">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 16 */ ?>"><i class="fa fa-info-circle fa-2x"></i> fiche detail</a>
					</div>
					<div class="  aligncenter">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 19 */ ?>"><i class="fa fa-home fa-2x"></i> hub</a>
					</div>
				</div>
			</div>
			<div>
<?php
		$iterations = 0;
		foreach ($liste_fk as $item) {
?>				<div class="margin_more padding_more border4">
					<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($item['data_vars']['link']['hub'])) /* line 25 */ ?>"><?php
			echo $item['page_data_html']['estTop'] /* line 25 */ ?> <?php echo $item['page_data_html']['nom'] /* line 25 */ ?></a>
				</div>
<?php
			$iterations++;
		}
?>			</div>
		</div>
<?php
		if ($description) {
?>		<div class="padding_more justify">
			<?php echo call_user_func($this->filters->truncate, $description, 500) /* line 30 */ ?>

		</div>
<?php
		}
?>
	</div>
<?php
	}


	function blockEntete_fiche_detail($_args)
	{
		extract($_args);
?>

	<div class="padding_more">entete_fiche_detail
		<div class="flex_h flex_align_top">
			<div class="padding_more borderr">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($img['img_large']['href'])) /* line 39 */ ?>" width="<?php
		echo LR\Filters::escapeHtmlAttr($img['img_large']['size'][0]) /* line 39 */ ?>" height="<?php echo LR\Filters::escapeHtmlAttr($img['img_large']['size'][1]) /* line 39 */ ?>" style="max-width:100%">
			</div>
			<div class="padding_more flex_main">
				<h1 class="aligncenter"><?php echo LR\Filters::escapeHtmlText($nom) /* line 42 */ ?></h1>

				<div class="  aligncenter">
					<img class="boxshadow" src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($img['img_small']['href'])) /* line 45 */ ?>" width="<?php
		echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][0]) /* line 45 */ ?>" height="<?php echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][1]) /* line 45 */ ?>" style="max-width:100%">
				</div>
				<div class="margin_more padding_more bordert flex flex_h flex_align_middle">
					<div class="flex_main aligncenter borderr">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 49 */ ?>"><i class="fa fa-file-text fa-2x"></i> fiche</a>
					</div>
					<div class="flex_main aligncenter">
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 52 */ ?>"><i class="fa fa-home fa-2x"></i> hub</a>
					</div>
				</div>
<?php
		if ($petitNom) {
?>				<div class="aligncenter">
					<h4><?php echo LR\Filters::escapeHtmlText($petitNom) /* line 56 */ ?></h4>
				</div>
<?php
		}
		if ($atout) {
?>				<div class="aligncenter">
					<h5> Atout : <?php echo LR\Filters::escapeHtmlText($atout) /* line 59 */ ?></h5>
				</div>
<?php
		}
?>
				<div>
					<div>
<?php
		$iterations = 0;
		foreach ($liste_fk as $item) {
?>						<div class="padding_more bordert">
							<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($item['data_vars']['link']['hub'])) /* line 64 */ ?>"><?php
			echo $item['page_data_html']['estTop'] /* line 64 */ ?> <?php echo $item['page_data_html']['nom'] /* line 64 */ ?></a>
						</div>
<?php
			$iterations++;
		}
?>					</div>
				</div>
			</div>
		</div>
<?php
		if (isset($information)) {
?>		<div class="padding_more">
<?php
		}
		if (isset($petitNom)) {
			?>			<h4 ><?php
		}
		echo LR\Filters::escapeHtmlText($petitNom) /* line 71 */;
		if (isset($petitNom)) {
?></h4>
<?php
		}
		?>			<?php echo $information /* line 72 */ ?>

<?php
		if (isset($information)) {
?>		</div>
<?php
		}
?>
	</div>
<?php
	}


	function blockFiche_mini($_args)
	{
		extract($_args);
		?>	<?php
		if ($data) {
			?> <?php
			extract($data);
		}
?>

	<div data-tpl="fiche_mini" class="margin_more border4 flex_h flex_main">
		<div class="boxshadow aligncenter">
			<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 80 */ ?>" class="inline">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($img['img_small']['href'])) /* line 81 */ ?>" width="<?php
		echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][0]) /* line 81 */ ?>" height="<?php echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][1]) /* line 81 */ ?>">
			</a>
		</div>
		<div class="flex_v flex_align_stretch padding_more">
			<div class=""><h4><?php echo LR\Filters::escapeHtmlText($nom) /* line 85 */ ?></h4></div>
<?php
		if ($atout) {
			?>			<div class=""><h5><?php echo LR\Filters::escapeHtmlText($atout) /* line 86 */ ?></h5></div>
<?php
		}
		if ($description) {
			?>			<div ><h5><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->truncate, $description, 250)) /* line 87 */ ?></h5></div>
<?php
		}
?>
			<div class="padding_more">
<?php
		if (isset($link['fiche'])) {
			?>				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 89 */ ?>"><?php
		}
		?>Fiche <?php echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 89 */ ?> <?php
		echo LR\Filters::escapeHtmlText($petitNom) /* line 89 */;
		if (isset($link['fiche'])) {
?></a>
<?php
		}
		if (isset($link['fiche_detail'])) {
			?>				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 90 */ ?>"><?php
		}
		?>Fiche détail <?php
		echo LR\Filters::escapeHtmlText($petitNom) /* line 90 */;
		if (isset($link['fiche_detail'])) {
?></a>
<?php
		}
?>
			</div>
		</div>
	</div>
<?php
	}


	function blockFiche_micro($_args)
	{
		extract($_args);
?>
	<div data-tpl="fiche_micro" class="margin_more border4 flex_h flex_align_middle flex_main">
<?php
		if ($data['img']['img_small']) {
?>		<div style="width:70px;">
			<img width="<?php echo LR\Filters::escapeHtmlAttr($data['img']['img_small']['size'][0]) /* line 98 */ ?>" height="auto" class="boxshadow" src="<?php
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($data['img']['img_small']['href'])) /* line 98 */ ?>" style="max-width:100%;">
		</div>
<?php
		}
?>
		<div class="flex_main padding">
			<h4 style="width:150px;" class="ellipsis"><?php echo $data['nom'] /* line 101 */ ?></h4>

			<div class="">
				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($data['link']['liste'])) /* line 104 */ ?>">voir <?php
		echo call_user_func($this->filters->truncate, $data['nom'], 25) /* line 104 */ ?></a>
			</div>
		</div>
		<div class="ededed borderl padding flex_align_middle">
			<i class="fa fa-caret-right"></i>
		</div>
	</div>
<?php
	}


	function blockFiche_image($_args)
	{
		extract($_args);
		if ($data['img']['img_small']) {
?>	<div class="relative cool">
		<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($data['link']['hub'])) /* line 114 */ ?>" class="inline borderr">
			<img width="<?php echo LR\Filters::escapeHtmlAttr($data['img']['img_small']['size'][0]) /* line 115 */ ?>" height="<?php
			echo LR\Filters::escapeHtmlAttr($data['img']['img_small']['size'][1]) /* line 115 */ ?>" class="" src="<?php
			echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($data['img']['img_small']['href'])) /* line 115 */ ?>">
			<div class="ededed padding_more   aligncenter bordert">
				<?php echo $data['nom'] /* line 117 */ ?> <?php echo $data['estTop'] /* line 117 */ ?>

			</div>
		</a>
	</div>
<?php
		}
		
	}


	function blockFiche_detail_fk($_args)
	{
		extract($_args);
?>
	<div class="margin_more border4 flex_h flex_main">
		<div class="boxshadow aligncenter">
			<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['hub'])) /* line 125 */ ?>" class="inline" style="width:90px;">
				<img src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($img['img_small']['href'])) /* line 126 */ ?>" width="<?php
		echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][0]) /* line 126 */ ?>" height="<?php echo LR\Filters::escapeHtmlAttr($img['img_small']['size'][1]) /* line 126 */ ?>" style="max-width:100%">
			</a>
		</div>
		<div class="flex_v flex_align_stretch padding_more">
			<div class=""><h4><?php echo LR\Filters::escapeHtmlText($nom) /* line 130 */ ?></h4></div>
			<div class=""><h5><?php echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 131 */ ?></h5></div>
			<div class="padding_more">
<?php
		if (isset($link['fiche'])) {
			?>				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche'])) /* line 133 */ ?>"><?php
		}
		?>Fiche <?php echo LR\Filters::escapeHtmlText($nomAppscheme) /* line 133 */ ?> <?php
		echo LR\Filters::escapeHtmlText($petitNom) /* line 133 */;
		if (isset($link['fiche'])) {
?></a>
<?php
		}
		if (isset($link['fiche_detail'])) {
			?>				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($link['fiche_detail'])) /* line 134 */ ?>"><?php
		}
		?>Fiche détail <?php
		echo LR\Filters::escapeHtmlText($petitNom) /* line 134 */;
		if (isset($link['fiche_detail'])) {
?></a>
<?php
		}
?>
			</div>
		</div>
	</div>
<?php
	}


	function blockFiche_produit_small($_args)
	{
		extract($_args);
		?>	<?php
		if ($data) {
			?> <?php
			extract($data);
		}
?>

	<div data-tpl="fiche_produit_small" class="promo_zone_produit scheme blanc relative" >
		<div class="ellipsis bold padding  text-center">
			<span class="inline borderb"><?php echo LR\Filters::escapeHtmlText($nomDestination) /* line 143 */ ?> </span>
		</div>
		<div style="overflow:hidden;" class="margin ">
			<div class="border4" style="overflow:hidden;position:relative;" onclick="document.location.href='<?php
		echo LR\Filters::escapeHtmlAttr(LR\Filters::escapeJs($href)) /* line 146 */ ?>'">
				<div class="aligncenter" style="z-index:100;width:100%;position:absolute;left:-0px;top:30px;">
					<img style="box-shadow:0 0 3px #333;" width="50" src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($fournisseurSrc_small)) /* line 148 */ ?>">
				</div>
				<div class="shadow">
					<div class="col-xs-6 nopadding alignright" style="">
						<img class="img-responsive" src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($destinationSrc_square)) /* line 152 */ ?>">
					</div>
					<div class="col-xs-6 nopadding alignleft" style="">
						<img class="img-responsive" src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($transportSrc_square)) /* line 155 */ ?>">
					</div>
					<div class="spacer"></div>
				</div>
				<div class="relative">
					<div class="padding"
					     style="bottom:0;width:100%;color:#333;text-shadow:0 0 3px #fff; background-color:rgba(255,255,255,0.6);">
						<?php
		echo $estCoeur /* line 162 */;
		echo $estHome /* line 162 */;
		echo $estOutInclus /* line 162 */;
		echo $estVol /* line 162 */ ?>

					</div>
				</div>
				<div class="padding">
					<strong class="ellipsis"><?php echo LR\Filters::escapeHtmlText($nomProduit) /* line 166 */ ?></strong>

					<div class="ellipsis">Durée : <?php echo LR\Filters::escapeHtmlText($duree) /* line 168 */ ?> jours</div>
					<div class="ellipsis">Départ de : <strong><?php echo LR\Filters::escapeHtmlText($nomVille) /* line 169 */ ?></strong></div>
					<div class="ellipsis"> Date(s) : <?php echo LR\Filters::escapeHtmlText($dateDepart) /* line 170 */ ?></div>
					<div class="ellipsis fontnormal"><?php echo LR\Filters::escapeHtmlText($atout) /* line 171 */ ?></div>
					<div class="alignright ellipsis bordert"><span class="textegris">dés </span>
						<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($href)) /* line 173 */ ?>" style="font-size:17px;" class="texteorange bold"><?php
		echo $prix /* line 173 */ ?> €</a>
						<br>
						<?php echo LR\Filters::escapeHtmlText($promotion) /* line 175 */ ?><span class="textegris">*ttc/pers</span></div>
					<div class="alignright">
						<a class="small_next block alignright" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($href)) /* line 177 */ ?>">Plus d'infos <i class="fa fa-info-circle"></i></a>
					</div>
				</div>
			</div>
			<div class="miniliste_france"><?php echo LR\Filters::escapeHtmlText($dep_fr) /* line 181 */ ?></div>
		</div>
	</div>
<?php
	}


	function blockFiche_rfk($_args)
	{
		extract($_args);
		if ($page_rfk) {
			$iterations = 0;
			foreach ($page_rfk as $row) {
				if (!$cartouche) {
					$this->renderBlock('cartouche_default', ['row'=>$row] + $this->params, 'html');
				}
				else {
					$this->renderBlock($cartouche, ['row'=>$row] + $this->params, 'html');
				}
				$iterations++;
			}
		}
		
	}


	function blockListe($_args)
	{
		extract($_args);
		if ($data) {
			if (!$cartouche) {
				$this->renderBlock('cartouche_default', ['row'=>$data] + $this->params, 'html');
			}
			else {
				$this->renderBlock($cartouche, ['row'=>$data] + $this->params, 'html');
			}
		}
		
	}


	function blockListe_produit_small($_args)
	{
		extract($_args);
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($data) as $row) {
			$this->renderBlock('fiche_produit_small', ['data' => $row] + get_defined_vars(), 'html');
			$iterations++;
		}
		array_pop($this->global->its);
		$iterator = end($this->global->its);
		
	}


	function blockListe_produit($_args)
	{
		extract($_args);
		$iterations = 0;
		foreach ($data as $row) {
			$this->renderBlock('fiche_produit', ['data' => $row] + $this->params, 'html');
			$iterations++;
		}
		
	}


	function blockListe_micro($_args)
	{
		extract($_args);
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($data) as $row) {
			$this->renderBlock('fiche_micro', ['data' => $row] + get_defined_vars(), 'html');
			$iterations++;
		}
		array_pop($this->global->its);
		$iterator = end($this->global->its);
		
	}


	function blockListe_mini($_args)
	{
		extract($_args);
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($data) as $row) {
			$this->renderBlock('fiche_mini', ['data' => $row] + get_defined_vars(), 'html');
			$iterations++;
		}
		array_pop($this->global->its);
		$iterator = end($this->global->its);
		
	}


	function blockListe_image($_args)
	{
		extract($_args);
		$iterations = 0;
		foreach ($iterator = $this->global->its[] = new LR\CachingIterator($data) as $row) {
			$this->renderBlock('fiche_image', ['data' => $row] + get_defined_vars(), 'html');
			$iterations++;
		}
		array_pop($this->global->its);
		$iterator = end($this->global->its);
		
	}


	function blockCartouche_default($_args)
	{
		extract($_args);
?>	<div data-tpl="cartouche_default" class="flex_main" style="min-width:50%;">
		<div class="padding_more margin_more">
			<div>
				<div><h3><?php echo LR\Filters::escapeHtmlText($row['nomAppscheme']) /* line 234 */ ?> <?php echo LR\Filters::escapeHtmlText($lienType) /* line 234 */ ?></h3></div>
				<div>
					<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($row['link']['liste'])) /* line 236 */ ?>">(<?php
		echo LR\Filters::escapeHtmlText($row['count']) /* line 236 */ ?>) Liste <?php echo LR\Filters::escapeHtmlText($row['nomAppscheme']) /* line 236 */ ?></a>
				</div>
			</div>
<?php
		if ($row['liste_micro']) {
?>
				<div data-tpl="liste_micro" class="flex_h flex_wrap flex_align_stretch">
<?php
			$this->renderBlock('liste_micro', ['data' => $row['liste_micro']] + get_defined_vars(), 'html');
?>
				</div>
<?php
		}
		if ($row['liste_mini']) {
?>
				<div data-tpl="liste_mini">
<?php
			$this->renderBlock('liste_mini', ['data' => $row['liste_mini']] + get_defined_vars(), 'html');
?>
				</div>
<?php
		}
		if ($row['liste_produit']) {
?>
				<div data-tpl="liste_produit">
<?php
			$this->renderBlock('liste_produit', ['data' => $row['liste_produit']] + get_defined_vars(), 'html');
?>
				</div>
<?php
		}
		if ($row['liste_produit_small']) {
?>
				<div data-tpl="liste_produit_small">
<?php
			$this->renderBlock('liste_produit_small', ['data' => $row['liste_produit_small']] + get_defined_vars(), 'html');
?>
				</div>
<?php
		}
?>
		</div>
	</div>
<?php
	}


	function blockCartouche_carrousel($_args)
	{
		extract($_args);
?>	<div class="padding_more margin_more flex_main">
		<div>
			<div><h3><?php echo LR\Filters::escapeHtmlText($row['data']['nomAppscheme']) /* line 266 */ ?> cartouche_carrousel</h3></div>
			<div>
				<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($row['data']['link']['liste'])) /* line 268 */ ?>">(<?php
		echo LR\Filters::escapeHtmlText($row['data']['count']) /* line 268 */ ?>) Liste <?php echo LR\Filters::escapeHtmlText($row['data']['nomAppscheme']) /* line 268 */ ?></a>
			</div>
		</div>
		<div class="flex_h flex_align_middle border4">
			<div class="swiper-container  swiper-container-horizontal" swiper="swiper">
				<div class="swiper-wrapper">
<?php
		if ($row['liste']) {
			$this->renderBlock('liste_image', ['data' => $row['liste']] + get_defined_vars(), 'html');
		}
?>
				</div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
				<div class="swiper-scrollbar"></div>
			</div>
		</div>
	</div>
<?php
	}


	function blockCalendar($_args)
	{
		extract($_args);
?>
	<div class="margin  flex_h flex_align_middle">
		<div class="padding_more margin_more aligncenter borderr">
			<i class="fa fa-calendar fa-3x"></i>
		</div>
		<div class="swiper-container  swiper-container-horizontal" swiper="swiper">
			<div class="swiper-wrapper">
<?php
		$iterations = 0;
		foreach ($data as $row) {
?>
					<div class="flex_h  flex_wrap flex_align_top cool" style="min-width:100%;">
<?php
			if (is_array|($row)) {
				$iterations = 0;
				foreach ($row as $row2) {
?>
								<div class="aligncenter carre borderb" style="width:16%;">
									<div class="borderr">
										<h5 class="aligncenter"><?php echo LR\Filters::escapeHtmlText($row2['mois_fr']) /* line 302 */ ?></h5>
										<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($row2['link_produit'])) /* line 303 */ ?>">
											<?php echo LR\Filters::escapeHtmlText($row2['count']) /* line 304 */ ?> croisières
											<span class="textegris">dés</span> <?php echo LR\Filters::escapeHtmlText($row2['prix']) /* line 305 */ ?> €
										</a>
									</div>
								</div>
<?php
					$iterations++;
				}
			}
			else {
?>
							<div class="aligncenter border4" style="width:80px;">
								<h5 class="aligncenter"><?php echo LR\Filters::escapeHtmlText($row['mois_fr']) /* line 312 */ ?></h5>
								<a href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($row['link_produit'])) /* line 313 */ ?>">
									<?php echo LR\Filters::escapeHtmlText($row['count']) /* line 314 */ ?> croisières
									<span class="textegris">dés</span> <?php echo LR\Filters::escapeHtmlText($row['prix']) /* line 315 */ ?> €
								</a>
							</div>
<?php
			}
?>
					</div>
<?php
			$iterations++;
		}
?>
			</div>
			<div class="swiper-pagination"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
			<div class="swiper-scrollbar"></div>
		</div>
	</div>
<?php
	}

}

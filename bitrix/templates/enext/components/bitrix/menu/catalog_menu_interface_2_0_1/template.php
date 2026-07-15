<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

global $arSettings;

$isSiteClosed = false;
if(COption::GetOptionString("main", "site_stopped") == "Y" && !$USER->CanDoOperation("edit_other_settings"))
	$isSiteClosed = true;

$obName = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $this->GetEditAreaId($this->randString()));
$containerName = 'catalog-menu-'.$obName;

if(!$isSiteClosed && !empty($arResult)) {?>
	<div class="catalog-menu catalog-menu-<?=strtolower($arSettings['CATALOG_MENU_INTERFACE_2_0_SUBMENU']['VALUE'])?> catalog-menu-mobile-<?=strtolower($arSettings['CATALOG_MENU_INTERFACE_2_0_SUBMENU_MOBILE']['VALUE'])?>" id="<?=$containerName?>" data-entity="dropdown-menu">
		<div class="hidden-md hidden-lg catalog-menu-title"><div class="catalog-menu-title-icon"><i class="icon-back"></i></div><div class="catalog-menu-title-text"><?=GetMessage("BM_CATALOG_SHORT")?></div></div>
		<ul id="menu">			
			<?$previousLevel = 0;					
			foreach($arResult as $arItem) {
				
				if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
					echo str_repeat("</ul></div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
				if($arItem["IS_PARENT"]) {?>
					<li<?=($arItem["SELECTED"] ? " class='active'" : "")?> data-entity="dropdown" data-text="<?=$arItem["TEXT"]?>">
						<<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?>span data-replace-content="<?=$arItem['LINK']?>"<?else:?>a href="<?=$arItem['LINK']?>"<?endif;?>>
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="catalog-menu-pic">
									<img src ="/bitrix/images/arturgolubev.lazyload/pixel.gif" data-lazyload-src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?>
								<?
								$arCode = explode('/',$arItem["LINK"]);
								?>
								<?/*<span class="catalog-menu-text" show-content="<?=$arItem["TEXT"]?>"></span>*/?>
								<span data-href-content="<?=$arItem['LINK']?>" class="catalog-menu-text show_<?=$arCode[2]?>"></span>
							<? else: ?>
								<span class="catalog-menu-text"><?=$arItem["TEXT"]?></span>
							<? endif; ?>
							<span class="catalog-menu-arrow"><i class="icon-arrow-right"></i></span>
						<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?></span><? else: ?></a><? endif; ?>
						<div class="catalog-menu-dropdown-menu" data-entity="dropdown-menu">
							<ul>						
				<?} else {?>
					<li<?=$arItem["SELECTED"] ? " class='active'" : ""?> data-text="<?=$arItem["TEXT"]?>">
						<<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?>span data-replace-content="<?=$arItem['LINK']?>"<?else:?>a href="<?=$arItem['LINK']?>"<?endif;?>>
							<?if(!empty($arItem["PARAMS"]["ICON"])) {?>
								<span class="catalog-menu-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
							<?} elseif(is_array($arItem["PARAMS"]["PICTURE"])) {?>
								<span class="catalog-menu-pic">
									<img src ="/bitrix/images/arturgolubev.lazyload/pixel.gif" data-lazyload-src="<?=$arItem['PARAMS']['PICTURE']['SRC']?>" width="<?=$arItem['PARAMS']['PICTURE']['WIDTH']?>" height="<?=$arItem['PARAMS']['PICTURE']['HEIGHT']?>" alt="<?=$arItem['PARAMS']['PICTURE']['ALT']?>" title="<?=$arItem['PARAMS']['PICTURE']['TITLE']?>" />
								</span>
							<?}?>
							<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?>
								<?
								$arCode = explode('/',$arItem["LINK"]);
								?>
								<?/*<span class="catalog-menu-text" show-content="<?=$arItem["TEXT"]?>"></span>*/?>
								<span data-href-content="<?=$arItem['LINK']?>" class="catalog-menu-text show_<?=$arCode[2]?>"></span>
							<? else: ?>
								<span class="catalog-menu-text"><?=$arItem["TEXT"]?></span>
							<? endif; ?>
						<? if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): ?></span><? else: ?></a><? endif; ?>
					</li>
				<?}
				$previousLevel = $arItem["DEPTH_LEVEL"];						
			}
			if($previousLevel > 1)
				echo str_repeat("</ul></div></li>", ($previousLevel - 1));?>
		</ul>
	</div>

	<script type="text/javascript">
		BX.message({
			MAIN_MENU: '<?=GetMessageJS("BM_MAIN_MENU")?>',
			CATALOG_FULL: '<?=GetMessageJS("BM_CATALOG_FULL")?>'
		});
		var <?=$obName?> = new JCCatalogMenu({
			container: '<?=$containerName?>'
		});
	</script>
<?/*	
	<style>
		<?
		foreach($arResult as $arItem):
			if(!$arItem["SHOW"] || ($arItem["DEPTH_LEVEL"]>1 && $arItem["PARAMS"]["HIDE_MENU_INDEX"] && $arItem["PARAMS"]["URL"]=='product') ): 
				$arCode = explode('/',$arItem["LINK"]);
				?>
				.show_<?=$arCode[2]?>:before{
					content:'<?=$arItem["TEXT"]?>';
				}
			<? endif;
		endforeach; ?>
	</style>
*/?>

<?}?>


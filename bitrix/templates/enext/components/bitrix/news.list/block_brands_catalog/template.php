<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;

$elementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$elementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$elementDeleteParams = array("CONFIRM" => GetMessage("BRANDS_ITEM_DELETE_CONFIRM"));?>

<div class="brands-wrapper">
	<div class="container-ws hide_container">
		<div class="row block-brands">			
			<?foreach($arResult["ITEMS"] as $arItem) {
				$this->AddEditAction($arItem["ID"], $arItem["EDIT_LINK"], $elementEdit);
				$this->AddDeleteAction($arItem["ID"], $arItem["DELETE_LINK"], $elementDelete, $elementDeleteParams);?>
				<div class="col-xs-6 col-md-2" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<a class="brands-item" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
						<span class="brands-item-title"><?=$arItem["NAME"]?></span>						
					</a>
				</div>
			<?}?>			
		</div>
	


	</div>
		<div class="brands-buttons show_brand_all">
			<div  class="btn btn-default" ><?=$arParams["ALL_LINK_TITLE"]?></div >
		</div>
</div>
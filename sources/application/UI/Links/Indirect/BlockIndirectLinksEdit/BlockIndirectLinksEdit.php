<?php
/**
 * @copyright   Copyright (C) 2010-2020 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

namespace Combodo\iTop\Application\UI\Links\Indirect\BlockIndirectLinksEdit;


use Combodo\iTop\Application\UI\Base\Component\Button\ButtonFactory;
use Combodo\iTop\Application\UI\Base\Component\Input\InputFactory;
use Combodo\iTop\Application\UI\Base\Component\Toolbar\Toolbar;
use Combodo\iTop\Application\UI\Base\Layout\UIContentBlock;
use Dict;
use MetaModel;

/**
 * Class BlockIndirectLinksEdit
 *
 * @package Combodo\iTop\Application\UI\Links\Indirect\BlockIndirectLinksEdit
 */
class BlockIndirectLinksEdit extends UIContentBlock
{
	// Overloaded constants
	public const BLOCK_CODE = 'ibo-block-indirect-links-edit';
	public const DEFAULT_JS_ON_READY_TEMPLATE_REL_PATH = 'application/links/indirect/block-indirect-links-edit/layout';

	/** @var int */
	public $iInputId;
	/** @var string */
	public $sLinkedSetId;
	/** @var string */
	public $sClass;
	/** @var string */
	public $sAttCode;
	/** @var string */
	public $sNameSuffix;
	/** @var bool */
	public $bDuplicates;
	/** @var string containing a js object name */
	public $oWizHelper;
	/** @var string */
	public $sExtKeyToRemote;
	/** @var bool */
	public $bJSDoSearch;

	/** @var string */
	public $sFormPrefix;
	/** @var string */
	public $sRemoteClass;


	public function AddControls()
	{
		$this->AddSubBlock(InputFactory::MakeForHidden("{$this->sFormPrefix}{$this->iInputId}", '', "{$this->sFormPrefix}{$this->iInputId}"));

		$oToolbar = new Toolbar(null, 'ibo-datatable--selection-validation-buttons-toolbar');
		$this->AddSubBlock($oToolbar);
		$oRemoveButton = ButtonFactory::MakeForSecondaryAction(Dict::S('UI:RemoveLinkedObjectsOf_Class'), null, null, false, "{$this->sLinkedSetId}_btnRemove");
		$oRemoveButton->SetOnClickJsCode("oWidget{$this->iInputId}.RemoveSelected();");
		$oToolbar->AddSubBlock($oRemoveButton);

		$oAddButton = ButtonFactory::MakeForSecondaryAction(Dict::Format('UI:AddLinkedObjectsOf_Class', MetaModel::GetName($this->sRemoteClass)), null, null, false, "{$this->sLinkedSetId}_btnAdd");
		$oAddButton->SetOnClickJsCode("oWidget{$this->iInputId}.AddObjects();");
		$oToolbar->AddSubBlock($oAddButton);

		// To prevent adding forms inside the main form
		$oDeferredBlock = new UIContentBlock("dlg_{$this->sLinkedSetId}", 'ibo-block-indirect-links--edit--dialog');
		$this->AddDeferredBlock($oDeferredBlock);
	}
}
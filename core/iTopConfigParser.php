<?php
/**
 * Created by Bruno DA SILVA, working for Combodo
 * Date: 31/12/2019
 * Time: 12:29
 */

use PhpParser\Node\Expr\Assign;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class iTopConfigParser
{

	/** @var \PhpParser\Node[] */
	private $aInitialNodes;

	/** @var \PhpParser\Node[] */
	private $aVisitedNodes;

	/** @var string|null  */
	private $oException = null;
	/**
	 * @var array
	 */
	private $aVarsMap;

	/**
	 * iTopConfigValidator constructor.
	 *
	 * @param $sConfig
	 * @param \PhpParser\Parser|null $oParser
	 *
	 * @throws \Exception
	 */
	public function __construct($sConfig)
	{
		$oParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

		$this->browseFile($oParser, $sConfig);
	}

	/**
	 * @return array
	 */
	public function GetVarsMap()
	{
		return $this->aVarsMap;
	}

	/**
	 * @param $arrayName
	 * @param $key
	 *
	 * @return array
	 */
	public function GetVarValue($arrayName, $key)
	{
		if (!array_key_exists($arrayName, $this->aVarsMap)){
			return array('found' => false);
		}
		$arrayValue = $this->aVarsMap[$arrayName];
		if (!array_key_exists($key, $arrayValue)){
			return array('found' => false);
		}
		return array('found' => true,
			'value' => $arrayValue[$key]);
	}

	/**
	 * @param \PhpParser\Parser $oParser
	 * @param $sConfig
	 *
	 * @return \Combodo\iTop\Config\Validator\ConfigNodesVisitor
	 */
	private function browseFile(\PhpParser\Parser $oParser, $sConfig)
	{
		$prettyPrinter = new Standard();

		$this->aVarsMap = array(
			'MySettings' => array(),
			'MyModuleSettings' => array(),
			'MyModules' => array(),
		);

		try
		{
			$aNodes = $oParser->parse($sConfig);
		}
		catch (\Error $e)
		{
			$sMessage = Dict::Format('config-parse-error', $e->getMessage(), $e->getLine());
			$this->oException = new \Exception($sMessage, 0, $e);
		}

		foreach ($aNodes as $oAssignation)
		{
			if (! $oAssignation instanceof Assign)
			{
				continue;
			}

			$sCurrentRootVar = $oAssignation->var->name;
			if (!array_key_exists($sCurrentRootVar, $this->aVarsMap))
			{
				continue;
			}
			$aCurrentRootVarMap =& $this->aVarsMap[$sCurrentRootVar];

			foreach ($oAssignation->expr->items as $oItem)
			{
				$sValue = $prettyPrinter->prettyPrintExpr($oItem->value);
				$aCurrentRootVarMap[$oItem->key->value] = $sValue;
			}
		}
	}
}
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * This is the catalog catalogajaxratingfield extension file.
 *
 * PHP version 5
 * @copyright  Christian Schiffler 2009
 * @author     Christian Schiffler  <c.schiffler@cyberspectrum.de> 
 * @package    CatalogAjaxRatingField
 * @license    GPL 
 * @filesource
 */

class CatalogAjaxRatingWidget extends Widget
{
	protected $strTemplate = 'form_widget';
	
	protected $intRatingUnitWidth = 30;
	
	
	public function validate()
	{
		return parent::validate();
	}
	
	
	public function generate()
	{
		$GLOBALS['TL_CSS']['rating'] = 'system/modules/catalogajaxratingfield/html/rating.css';
		
		$intRatingWidth = $this->value * $this->intRatingUnitWidth;
		
		$strUrl  = preg_replace('@(\?|&)q=[^&]*&j=[^&]@', '', $this->Environment->request);
		$strUrl .= strpos($strUrl, '?') === false ? '?' : '&';
		
		
		$return  = '<div class="ratingblock">';
		$return .= '<div id="unit_long'.$this->strId.'">';
		$return .= '<ul id="unit_ul'.$this->strId.'" class="unit-rating" style="width:'.$this->intRatingUnitWidth*$this->size.'px;">';
		$return .= '<li class="current-rating" style="width:'.$intRatingWidth.'px;" title="'.sprintf($GLOBALS['TL_LANG']['catalogajaxratingfield']['votevalue'], $this->value, $this->size). '">'.sprintf($GLOBALS['TL_LANG']['catalogajaxratingfield']['votevalue'], $this->value, $this->size). '</li>';
		
		if (!$this->voted)
		{
			for( $i=1; $i<=$this->size; $i++)
			{
				$return .= '<li class="rater"><a href="'. $strUrl .'q=rating&amp;ratecat='.$this->catId.'&amp;rateitem='.$this->itemId.'&amp;value='.$i.'" title="'.sprintf($GLOBALS['TL_LANG']['catalogajaxratingfield']['votewithvalue'], $i, $this->size).'" class="r'.$i.'-unit rater" rel="nofollow" onclick="return false">'.$i.'</a></li>';
			}
		}
		
		$return .= '</ul></div></div>';
		
		$return .= "
		<script type=\"text/javascript\">
//<![CDATA[
			window.addEvent('domready', function() {
				$$('#unit_ul" . $this->strId . " a.rater').addEvent('click', function() {
					url=this.href + '&amp;isAjax=1';
					new Request({
						url: url,
						onComplete: function(txt, xml) {
							var text='".sprintf($GLOBALS['TL_LANG']['catalogajaxratingfield']['votevalue'], 'xxx', $this->size)."';
							text=text.replace( /xxx/, txt);
							var html='<li class=\"current-rating\" style=\"width:' + (txt * " . $this->intRatingUnitWidth . ") + 'px;\" title=' + text + '>' + text + '<\/li>'
							$('unit_ul" . $this->strId . "').set('html', html);
						}
					}).send();
					$('unit_ul" . $this->strId . "').set('html', '<div class=\"loading\"><\/div>');
					
				});
				
			});
//]]>
	</script>
		";
		
		return $return;
	}
}

// class to manipulate the field info to be as we want it to be, to render it and to make editing possible.
class CatalogAjaxRatingField extends Backend {

	public function calculate($sum, $votecount)
	{
		return round($sum/$votecount, 2);
	}

	public function parseValue($id, $k, $raw, $blnImageLink, $objCatalog, $objCatalogInstance)
	{
		// we have to determine the vote count and if the current user is allowed to cast a vote.
		$objVotes=$this->Database->prepare("SELECT *, COUNT(*) AS totalVotes, SUM(value) AS sumValue FROM tl_catalog_rating WHERE cat_id=? AND item_id=? GROUP BY cat_id, item_id")
							->execute($objCatalog->pid, $objCatalog->id);
		// if there are votes for this item, calculate them
		if($objVotes->next()) {
			$votecount=$objVotes->totalVotes;
			$votesum=$objVotes->sumValue;
			$value=$this->calculate($votesum, $votecount);
			// check if this IP has already voted today.
			$hasVoted=$this->Database->prepare("SELECT id, ip FROM tl_catalog_rating WHERE cat_id=? AND item_id=? AND ip=? AND time>=?")
								->execute($objCatalog->pid, $objCatalog->id, $_SERVER['REMOTE_ADDR'], (time()-(60*60*24)))
								->next();
		} else { // if no votes are in the database for this item, we can't do anything.
			$votecount=0;
			$votesum=0;
			$value=0;
			$hasVoted=false;
		}
		// disable voting at all for everything except for ModuleCatalogReader
		$hasVoted= $hasVoted || (!($objCatalogInstance instanceof ModuleCatalogReader));

		// Catch voting!
		if (($this->Input->get('q') == 'rating') && ($objCatalog->pid==$this->Input->get('ratecat')) && ($objCatalog->id==$this->Input->get('rateitem')))
		{
			if (!$hasVoted) {
				$this->Database->prepare("INSERT INTO tl_catalog_rating (cat_id, item_id, value, ip, time) VALUES (?, ?, ?, ?, ?)")
							   ->execute($objCatalog->pid, $objCatalog->id, $this->Input->get('value'), $_SERVER['REMOTE_ADDR'], time()); 
							   // Would love to use $this->Environment->ip here but this is the internal IP of an network if behind NAT, therefore useless.
				$votecount++;
				$votesum+=$this->Input->get('value');
				$value=$this->calculate($votesum, $votecount);
				// now we are pretty dirty in here, we have to update ourselves "on the fly".
				$table=$this->Database->prepare("SELECT tableName FROM tl_catalog_types WHERE id=?")
						->execute($objCatalog->pid);
				if($table->next()) {
					$this->Database->prepare("UPDATE ".$table->tableName." SET ".$k."=? WHERE id=?")
							->execute($this->calculate($votesum, $votecount), $objCatalog->id);
				}
			}
		 
			if ($this->Input->get('isAjax') == '1')
			{
				$objVotes=$this->Database->prepare("SELECT *, COUNT(*) AS totalVotes, SUM(value) AS sumValue FROM tl_catalog_rating WHERE cat_id=? AND item_id=? GROUP BY cat_id, item_id")
							->execute($objCatalog->pid, $objCatalog->id);
				$objVotes->next();
				echo round($objVotes->sumValue/$objVotes->totalVotes, 2);
				exit;
			}
			else
			{
				$this->redirect(preg_replace('@(\?|&)q=[^&]*&j=[^&]@', '', $this->Environment->request));
			}
		}

		$objWidth=$this->Database->prepare("SELECT ajaxratingfield FROM tl_catalog_fields WHERE pid=? AND colName=?")
							->execute($objCatalog->pid, $k);
		$ajaxWidget = new CatalogAjaxRatingWidget(array
										 (
										  'value'=>$value,
										  'size' => 5,
										  'strId' => $objCatalog->pid . '_' . $objCatalog->id,
										  'catId' => $objCatalog->pid,
										  'itemId' => $objCatalog->id,
										  'voted' => $hasVoted,
										  'tableless'	=> true,
										  'intRatingUnitWidth' => $objWidth->ajaxratingfield,
										  ));
		$html = $ajaxWidget->parse();
		return array
				(
				 	'items'	=> array($html),
					'values' => false,
				 	'html'  => $html,
				);
	}
}
?>
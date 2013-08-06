<?php

namespace qtism\data\storage\xml\marshalling;

use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\state\ResponseDeclarationCollection;
use qtism\data\ExtendedAssessmentItemRef;
use qtism\data\storage\xml\marshalling\AssessmentItemRefMarshaller;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * A Marshaller aiming at marshalling/unmarshalling ExtendedAssessmentItemRefs.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExtendedAssessmentItemRefMarshaller extends AssessmentItemRefMarshaller {
	
	/**
	 * Marshall a ExtendedAssessmentItemRef object into its DOMElement representation.
	 * 
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = parent::marshall($component);
		
		foreach ($component->getResponseDeclarations() as $responseDeclaration) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($responseDeclaration);
			$element->appendChild($marshaller->marshall($responseDeclaration));
		}
		
		foreach ($component->getOutcomeDeclarations() as $outcomeDeclaration) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeDeclaration);
			$element->appendChild($marshaller->marshall($outcomeDeclaration));
		}
		
		if ($component->hasResponseProcessing() === true) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getResponseProcessing());
			$respProcElt = $marshaller->marshall($component->getResponseProcessing());
			$element->appendChild($respProcElt);
		}
		
		self::setDOMElementAttribute($element, 'adaptive', $component->isAdaptive());
		self::setDOMElementAttribute($element, 'timeDependent', $component->isTimeDependent());
		
		return $element;
	}
	
	/**
	 * Unmarshall an extended version of an assessmentItemRef DOMElement into 
	 * a ExtendedAssessmentItemRef object.
	 * 
	 * @return ExtendedAssessmentItemRef A ExtendedAssessmentItemRef object.
	 */
	protected function unmarshall(DOMElement $element) {
		$baseComponent = parent::unmarshall($element);
		$identifier = $baseComponent->getIdentifier();
		$href = $baseComponent->getHref();
		
		$compactAssessmentItemRef = new ExtendedAssessmentItemRef($identifier, $href);
		$compactAssessmentItemRef->setRequired($baseComponent->isRequired());
		$compactAssessmentItemRef->setFixed($baseComponent->isFixed());
		$compactAssessmentItemRef->setPreConditions($baseComponent->getPreConditions());
		$compactAssessmentItemRef->setBranchRules($baseComponent->getBranchRules());
		$compactAssessmentItemRef->setItemSessionControl($baseComponent->getItemSessionControl());
		$compactAssessmentItemRef->setTimeLimits($baseComponent->getTimeLimits());
		$compactAssessmentItemRef->setTemplateDefaults($baseComponent->getTemplateDefaults());
		$compactAssessmentItemRef->setWeights($baseComponent->getWeights());
		$compactAssessmentItemRef->setVariableMappings($baseComponent->getVariableMappings());
		
		$responseDeclarationElts = self::getChildElementsByTagName($element, 'responseDeclaration');
		$responseDeclarations = new ResponseDeclarationCollection();
		foreach ($responseDeclarationElts as $responseDeclarationElt) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($responseDeclarationElt);
			$responseDeclarations[] = $marshaller->unmarshall($responseDeclarationElt);
		}
		$compactAssessmentItemRef->setResponseDeclarations($responseDeclarations);
		
		$outcomeDeclarationElts = self::getChildElementsByTagName($element, 'outcomeDeclaration');
		$outcomeDeclarations = new OutcomeDeclarationCollection();
		foreach ($outcomeDeclarationElts as $outcomeDeclarationElt) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeDeclarationElt);
			$outcomeDeclarations[] = $marshaller->unmarshall($outcomeDeclarationElt);
		}
		$compactAssessmentItemRef->setOutcomeDeclarations($outcomeDeclarations);
		
		$responseProcessingElts = self::getChildElementsByTagName($element, 'responseProcessing');
		if (count($responseProcessingElts) === 1) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($responseProcessingElts[0]);
			$compactAssessmentItemRef->setResponseProcessing($marshaller->unmarshall($responseProcessingElts[0]));
		}
		
		if (($adaptive = static::getDOMElementAttributeAs($element, 'adaptive', 'boolean')) !== null) {
			$compactAssessmentItemRef->setAdaptive($adaptive);
		}
		
		if (($timeDependent = static::getDOMElementAttributeAs($element, 'timeDependent', 'boolean')) !== null) {
			$compactAssessmentItemRef->setTimeDependent($timeDependent);
		}
		else {
			$msg = "The mandatory attribute 'timeDependent' is missing from element '" . $element->nodeName . "'.";
			throw new UnmarshallingException($msg, $element); 
		}
		
		return $compactAssessmentItemRef;
	}
}
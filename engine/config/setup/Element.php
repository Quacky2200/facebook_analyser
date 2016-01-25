<?php
class Element{
	public $tagname;
	public $elements;
	public $attributes;
	public $inline = false;
	public function __construct($tagname, $attributes = null, $elements = null, $inline = false){
		$this->tagname = $tagname;
		$this->attributes = $attributes ?: array();
		$this->elements = $elements ?: array();
		$this->inline = $inline;
	}
	private function recurseElements($elements){
		if(is_array($elements)){
			$HTML = "";
			foreach($elements as $element){
				$HTML .= $this->recurseElements($element);
			}
			return $HTML;
		} else if (is_subclass_of($elements, 'Element') || $elements instanceof Element){
			return $elements->toHTML();
		} else {
			return $elements;
		}
	}
	public function toHTML(){
		$HTML = '<' . $this->tagname . " " . $this->getAttributes();
		if($this->inline){
			return $HTML . '/>';
		} else {
			$HTML .= ">";
			$HTML .= $this->recurseElements($this->elements);
			// if(is_array($this->elements)){
			// 	foreach ($this->elements as $item){
			// 		if(is_subclass_of($item, 'Element') || $item instanceof Element){
			// 			$HTML .= $item->toHTML();
			// 		} else {
			// 			var_dump($item);
			// 			// try{
			// 			// 	$HTML .= $item;
			// 			// } catch (Exception $e){
			// 			// 	throw new Exception("Non-Element cannot be converted to HTML");
			// 			// }
			// 		}
			// 	}
			// } else {
			// 	try{
			// 		$HTML .= (string)$this->elements;
			// 	} catch (Exception $e){
			// 		throw new Exception("Non-Element cannot be converted to HTML");
			// 	}
			// }
			return $HTML . "</" . $this->tagname . '>';
		}
	}
	public function getAttributes(){
		if(is_array($this->attributes)){
			$attributes = "";
			foreach($this->attributes as $key=>$value){
				$attributes .= $key . "=\"" . $value . "\" ";
			}
			return $attributes;
		}
		return null;

	}
}
?>
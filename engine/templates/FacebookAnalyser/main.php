<?php

class FacebookAnaylser extends Template{
	public function getName(){
		return __CLASS__;
	}
	public function getKudos(){
		return array(
			new Kudos("Aidan", "Cammies", "Team Leader & Programmer"),
			new Kudos("Shaun", "George", "Programmer & Facebook Intergration Researcher"),
			new Kudos("Melissa", "Whiting", "Researcher, Programmer, Designer & Team Co-Ordinator"),
			new Kudos("Annelies", "Gibson", "Researcher, Programmer, Designer & Co-Ordinator"),
			new Kudos("Winston", "Ellis", "Facebook Intergration Researcher & Programmer"),
			new Kudos("Gareth Thomas", "Facebook Intergration Researcher & Programmer"),
			new Kudos("Ian", "", "Facebook Intergration Researcher & Designer"),
			new Kudos("Chris Wheatland", "Facebook Intergration Researcher & Designer"),
			new Kudos("Matthew", "James", "Design Research & Creator/Lead Programmer w/ Engine")
		);
	}
	public function getPages(){
		return array(
			new Home(),
			new Authentication()
		);
	}
}
/*
	*Temporary*
	Waiting to find a new home
*/
class Home extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURLRegex(){
		return "/^(Home)$/"
	}
	public function run($Template){
		echo $this->getName() . "->(Run code)";
	}
	public function show($Template){
		echo "<br/>" . $this->getName() , "->(Show template)";
	}
}
class Authentication extends Page{
	public function getName(){
		return "Login";
	}
	public function getURLRegex(){
		return "/^(Login)$/"
	}
	public function run($Template){
		echo $this->getName() . "->(Run code)";
	}
	public function show($Template){
		echo "<br/>" . $this->getName() . "->(Show template)";
	}
}
return new Setup();
?>
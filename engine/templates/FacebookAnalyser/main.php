<?php
require('SDK.php');
require('Home.php');
require('User.php');
require('Analyse.php');
class FacebookAnalyser extends Template{
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
			new Analyse()
		);
	}
}
return new FacebookAnalyser();
?>
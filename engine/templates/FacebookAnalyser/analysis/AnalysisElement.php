<?php
	abstract class AnalysisElement{
		//We expect to get an analysis array output
		//This allows us to seperate the analysis process into serveral different ones if require and to keep
		//the analysis code relevent
		public abstract function analyse($data);
	}
?>
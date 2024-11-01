<?php
/**
 * Progress bar for a lengthy PHP process
 * http://spidgorny.blogspot.com/2012/02/progress-bar-for-lengthy-php-process.html
 */
class FONE_ProgressBar {
	var $percentDone = 0;
	var $pbid;
	var $pbarid;
	var $tbarid;
	var $textid;
	var $decimals = 1;
	function __construct($percentDone = 0) {
		$this->pbid = 'pb';
		$this->pbarid = 'progress-bar';
		$this->tbarid = 'transparent-bar';
		$this->textid = 'pb_text';
		$this->percentDone = $percentDone;

		header('X-Accel-Buffering: no');
		ini_set('max_execution_time', 600); //600 seconds = 10 minutes
		ini_set('memory_limit','128M');
		set_time_limit(0); 
		ob_end_flush();
	}
	function FONE_render() {
		print($this->FONE_getContent());
		$this->FONE_flush();
	}
	function FONE_getContent() {
		$this->percentDone = floatval($this->percentDone);
		$percentDone = number_format($this->percentDone, $this->decimals, '.', '') .'%';
		return '
		<div style="width: 500px;">
			<div id="'.$this->pbid.'" class="pb_container">
				No cierre esta ventana hasta que finalize el proceso...
				<div id="'.$this->textid.'" class="'.$this->textid.'">'.$percentDone.'</div>
				<div class="pb_bar">
					<div id="'.$this->pbarid.'" class="pb_before"
					style="width: '.$percentDone.';"></div>
					<div id="'.$this->tbarid.'" class="pb_after"></div>
				</div>
				<br style="height: 1px; font-size: 1px;"/>
			</div>
		</div>
		<style>
			.pb_container {
				position: relative;
				margin-top:10px;
			}
			.pb_bar {
				width: 100%;
				height: 2.8em;
				border: 1px solid silver;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomleft: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_before {
				float: left;
				height: 2.8em;
				background-color: #43b6df;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-bottomleft: 5px;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-bottom-left-radius: 5px;
			}
			.pb_after {
				float: left;
				background-color: #FEFEFE;
				-moz-border-radius-topright: 5px;
				-moz-border-radius-bottomright: 5px;
				-webkit-border-top-right-radius: 5px;
				-webkit-border-bottom-right-radius: 5px;
			}
			.pb_text {
				padding-top: 0.4em;
				position: absolute;
				left: 35%;
				height:100px;
				font-size:20px;
			}
		</style>'."\r\n";
	}

	function FONE_setProgressBarProgress($percentDone, $text = '') {
		$this->percentDone = $percentDone;
		//$text = $text ? $text : number_format($this->percentDone, $this->decimals, '.', '').'%';
		if ($text!=''){
			$text = $text.' '.number_format($this->percentDone, $this->decimals, '.', '').'%';
		}else{
			$text = $text ? $text : number_format($this->percentDone, $this->decimals, '.', '').'%';
		}
		print('
		<script type="text/javascript">
		if (document.getElementById("'.$this->pbarid.'")) {
			document.getElementById("'.$this->pbarid.'").style.width = "'.$percentDone.'%";');
		if ($percentDone == 100) {
			//print('document.getElementById("'.$this->pbid.'").style.display = "none";');
		} else {
			print('document.getElementById("'.$this->tbarid.'").style.width = "'.(100-$percentDone).'%";');
		}
		if ($text) {
			print('document.getElementById("'.$this->textid.'").innerHTML = "'.htmlspecialchars($text).'";');
		}
		print('}</script>'."\n");
		$this->FONE_flush();
	}
	function FONE_hidebar(){
		print('
		<script type="text/javascript">
		document.getElementById("'.$this->pbid.'").style.display = "none";
		</script>'."\n");
	}
	function FONE_flush() {
		//print str_pad('', intval(ini_get('output_buffering')))."\n";
		//ob_end_flush();
		flush();
	}
}
?>
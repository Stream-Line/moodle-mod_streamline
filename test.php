<?php
//$response = http_get("http://www.example.com/", array("timeout"=>1), $info);
//print_r($info);

	function definition_after_data($newmsform) {
        $mform = $newmsform->_form;
		
        if ($mform->isSubmitted()) {
            $someelem = $mform->getElement('salt');
            $value = $someelem->getValue();
			echo $value & 'Hello world';
            // Do whatever checking you need
           //$someelem->setValue($someothervalue);
            // etc.
            //  add some new elements...
    }
	
	}
?>
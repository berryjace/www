<?php
class AFFPDF extends TCPDF {
    var $htmlHeader;

    public function setHtmlHeader($htmlHeader) {
        $this->htmlHeader = $htmlHeader;
    }

    public function Header() {
        $html = $this->htmlHeader;
        $this->writeHTML($html,false, false, false, false,'');
    }
}
?>

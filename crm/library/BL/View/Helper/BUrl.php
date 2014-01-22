<?php

class BL_View_Helper_BUrl extends Zend_View_Helper_Abstract {

    public function BUrl() {
        return $this;
    }

    public function currentUrl() {
        return Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
    }

    public function prep_url($str = '') {
        if ($str == 'http://' OR $str == '') {
            return '';
        }

        $url = parse_url($str);

        if (!$url OR !isset($url['scheme'])) {
            $str = 'http://' . $str;
        }

        return $str;
    }

    public function url_title($str, $separator = 'dash', $lowercase = FALSE) {
        if ($separator == 'dash') {
            $search = '_';
            $replace = '-';
        } else {
            $search = '-';
            $replace = '_';
        }

        $trans = array(
            '&\#\d+?;' => '',
            '&\S+?;' => '',
            '\s+' => $replace,
            '[^a-z0-9\-\._]' => '',
            $replace . '+' => $replace,
            $replace . '$' => $replace,
            '^' . $replace => $replace,
            '\.+$' => ''
        );

        $str = strip_tags($str);

        foreach ($trans as $key => $val) {
            $str = preg_replace("#" . $key . "#i", $val, $str);
        }

        if ($lowercase === TRUE) {
            $str = strtolower($str);
        }

        return trim(stripslashes($str));
    }

    public static function full_url() {
        return Zend_Controller_Front::getInstance()->getBaseUrl();
    }

    public function absoluteUrl($url="") {
        $url = ($url == "") ? $this->currentUrl() : $url;
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;
        return $url;
    }

    public function site_url($url="") {
        return $this->absoluteUrl($this->full_url() . "/" . $url);
    }

    public function redirect($uri = '', $method = 'location', $http_response_code = 302) {
        if (!preg_match('#^https?://#i', $uri)) {
            $uri = $this->site_url($uri);
        }

        switch ($method) {
            case 'refresh' : header("Refresh:0;url=" . $uri);
                break;
            default : header("Location: " . $uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

    /**
     * Function to SSL redirect if it's not in SSL mode
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function ssl_redirect() {
        if ($_SERVER ["SERVER_PORT"] != 443) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoUrl(str_replace("http://", "https://", $this->absoluteUrl()));
        }
    }

    /**
     * Function to SSL redirect if it's not in SSL mode
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function non_ssl_redirect() {
        if ($_SERVER ["SERVER_PORT"] != 80) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoUrl(str_replace("https://", "http://", $this->absoluteUrl()));
        }
    }

    /*
      $nr = number of results
      $pp = results per page
      $pnp = page navigation pages
      $pn = current page
      $url = base url to append navigation to
      $sr = starting row
     */
    public function navLinks($nr, $sr, $pp, $pnp, $pn, $url) {
        $url=htmlentities($url);
        $pnav = '';
        $link = '';
        $start = '<ul id="pagination-digg">';
        $previous = '';
        $next = '';
        $end = '';
        
        if ($pn >= 2) {
            $previous .= " <li><a href=\"" . $url . "sr=" . ( $sr - $pp );
            $previous .= "&pp=" . $pp . "&page=" . ( $pn - 1) . "\">&laquo; Back</a></li>";
        }

        if ($pn < $nr and ( $pn * $pp) < $nr) {
            $next .= "<li><a href=\"" . $url . "sr=" . ( $sr + $pp );
            $next .= "&pp=" . $pp . "&page=" . ( $pn + 1) . "\">Next &raquo;</a></li> ";
        }

        if ($nr > $pp) {
            $tp = $nr / $pp;

            if ($tp != intval($tp)) {
                $tp = intval($tp) + 1;
            }

            $cp = 0;

            while ($cp++ < $tp) {
                if (( $cp < $pn - $pnp or $cp > $pn + $pnp) and $pnp != 0) {
                    if ($cp == 1) {
                        $start .= " <li><a href=\"" . $url;
                        $start .= "sr=0&";
                        $start .= "pp=" . $pp . "&page=1\">&laquo; Start</a></li> ";
                    }

                    if ($cp == $tp) {
                        $end .= "<li><a href=\"" . $url;
                        $end .= "sr=";
                        $end .= ( $tp - 1 ) * $pp . "&pp=" . $pp . "&page=";
                        $end .= $tp . "\">End &raquo;</a></li>";
                    }
                } else {
                    if ($cp == $pn) {
                        $link .= '<li class="active"><span class="selNav">' . $cp . '</span></li>';
                    } else {
                        $link .= " <li><a href=\"" . $url;
                        $link .= "sr=" . ( $cp - 1) * $pp;
                        $link .= "&pp=" . $pp . "&page=" . $cp . "\"> $cp </a></li> ";
                    }
                }
            }

            $end  .='</ul>';
            $pnav .= $start;
            $pnav .= $previous;
            $pnav .= $link;
            $pnav .= $next;
            $pnav .= $end;            
        }

        if ($nr == 0) {
            $nom = 0;
        } else {
            $nom = 1;
        }

        $pnav .= " Showing " . ( $sr + $nom );

        if ($pp > 1) {
            $pnav .= " - ";

            if ($sr + $nom + $pp < $nr) {
                $pnav .= ( $sr + $nom + $pp ) - 1;
            } else {
                $pnav .= $nr;
            }
        }
        $pnav .= " of " . $nr . " ";        
        return ((int)$nom > 0) ? $pnav : "";
    }

     /**
     * Function to get the url before the last slash
     * @author Zea
     * @version 0.1
     * @copyright Blueliner Marketing
     * @access pulic
     * @param 
     * @return void
     * for the url http://localhost/amc/vendor it will give http://localhost/amc
     */
    public function last_slahs_omit() {
        $url = $this->absoluteUrl($this->full_url());
        return substr($url, 0, (strlen ($url)) - (strlen (strrchr($url,'/'))));
    }

    public function __toString() {
        return (string) $this->currentUrl();
    }

}
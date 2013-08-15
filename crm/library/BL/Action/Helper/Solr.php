<?php

class BL_Action_Helper_Solr extends Zend_Controller_Action_Helper_Abstract {

    protected $_em;

    public function __construct() {
        $this->init_solr();
        $doctrineContainer = Zend_Registry::get('doctrine');
        $this->_em = $doctrineContainer->getEntityManager();
    }

    /**
     * Function to initialize Solr Autoloader
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function init_solr() {
        require('Solarium/Autoloader.php');
        Solarium_Autoloader::register();
    }

    /**
     * Function to get solr config from the application.ini
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function get_solr_config() {
        $utils = $this->_actionController->getHelper("BUtilities");
        $config = array(
            'adapteroptions' => array(
                'host' => $utils->config_item('solr', 'host'),
                'port' => $utils->config_item('solr', 'port'),
                'path' => '/solr/',
            )
        );
        return $config;
    }

    /**
     * Function to check if the server is running or not
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function check_server($config) {
        $client = new Solarium_Client($config);
        $ping = $client->createPing();
        try {
            $client->ping($ping);
        } catch (Solarium_Exception $e) {
            die("Error : Search Server is unavailable");
        }
    }

    /**
     * Function to add/edit an Event document in Solr Indexing. Update is 
     * autmatically detected by Solr. So no edit function is necessary.
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function add_event_doc($event_id, $batch = false) {
        $event = $this->_em->find("ZB\Entity\Event", (int) $event_id);
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $geo_loc = $event->geolocation;
        $doc->id = "e-" . $event->id;
        $doc->type = "event";
        $doc->sort_i = 1;
        $doc->name = $event->name;
        $doc->host_name = strtolower($event->owner_id->first_name)." ".strtolower($event->owner_id->last_name);
        $doc->email = $event->email;
        $doc->picture = $event->picture;
        $doc->website = $event->website;
        if (($event->start_date <> "") && intval($event->start_date->format("Y-m-d"))) {            
            $doc->sdate = $event->start_date->format("Y-m-d") . "T00:00:00Z";
        }
        if (($event->end_date <> "") && intval($event->end_date->format("Y-m-d"))) {
            $doc->edate = $event->end_date->format("Y-m-d") . "T00:00:00Z";
        }
        $doc->phone = $event->main_phone;
        $doc->address = $event->address_line1 . "\n" . $event->address_line2;
        $doc->address.=($event->city <> "")  ? "\n,".$event->city : ""; 
        $doc->address.=($event->state <> "")  ? " ".$event->state : ""; 
        $doc->cost = $event->cost;
        $doc->zip = $event->zip;
        $doc->details = $event->details;
        if (!is_null($geo_loc)) {
            $doc->location = $geo_loc;
        }
        $bwords = array();
        foreach ($event->EventBuzzword as $bword) {
                    $doc->addField("tags", trim(strtolower($bword->buzzword_id->buzzword)));
        }
        $update->addDocument($doc);
        $update->addCommit();
        $result = $client->update($update);
        return $result;
    }

    /**
     * Function to add/edit an Group document in Solr Indexing. Update is 
     * autmatically detected by Solr. So no edit function is necessary.
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function add_group_doc($group_id) {
        $group = $this->_em->find("ZB\Entity\Group", (int) $group_id);
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $doc->id = "g-" . $group->id;
        $doc->type = "group";
        $doc->sort_i = 2;
        $doc->name = $group->name;
        $doc->host_name = strtolower($group->admin_id->first_name)." ".strtolower($group->admin_id->last_name);
        $doc->picture = $group->image;
        $doc->website = $group->web_url;
        if (intval($group->creation_date->format("Y-m-d"))) {
            $doc->regdate = $group->creation_date->format("Y-m-d") . "T" . $group->creation_date->format("H:i:s") . "Z";
        }
        $doc->zip = $group->zipcode;
        $doc->details = $group->description;
        $bwords = array();
        foreach ($group->GroupBuzzword as $bword) {
                    $doc->addField("tags", trim(strtolower($bword->buzzword_id->buzzword)));
        }
        $update->addDocument($doc);
        $update->addCommit();
        $result = $client->update($update);
        return $result;
    }

    /**
     * Function to add/edit a deal document to solr db
     * edit is autmatically detected by Solr. So no edit function is necessary.
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function add_deal_doc($deal_id) {
        $deal = $this->_em->find("ZB\Entity\Deal", (int) $deal_id);
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $doc->id = "d-" . $deal->id;
        $doc->type = "deal";
        $doc->sort_i = 3;
        $doc->host_name = strtolower($deal->owner_id->first_name)." ".strtolower($deal->owner_id->last_name);
        $doc->name = $deal->name;
        if (intval($deal->start_date->format("Y-m-d"))) {
            $doc->sdate = $deal->start_date->format("Y-m-d") . "T00:00:00Z";
        }
        if (intval($deal->end_date->format("Y-m-d"))) {
            $doc->edate = $deal->end_date->format("Y-m-d") . "T00:00:00Z";
        }
        $doc->picture = $deal->picture;
        $doc->details = $deal->description;
        foreach ($deal->DealBuzzword as $bword) {
                    $doc->addField("tags", trim(strtolower($bword->buzzword_id->buzzword)));
        }
        $update->addDocument($doc);
        $update->addCommit();
        $result = $client->update($update);
        return $result;
    }

    /**
     * Function to add/edit an Person's profile document in Solr Indexing.      
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return String
     */
    public function add_profile_doc($profile_id) {
        $profile = $this->_em->find("ZB\Entity\User", (int) $profile_id);
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $doc = $update->createDocument();
        $geo_loc = null; // User table doesn't have that. 
        $doc->id = "p-" . $profile->id;
        $doc->type = "profile";
        $doc->sort_i = 4;
        $doc->name = $profile->first_name . " " . $profile->last_name;
        $doc->email = $profile->email;
        $doc->picture = $profile->picture;
        $doc->website = $profile->website;
        if (intval($profile->reg_date->format("Y-m-d"))) {
            $doc->regdate = $profile->reg_date->format("Y-m-d") . "T00:00:00Z";
        }
        $doc->phone = $profile->phone;
        $doc->address = $profile->address_line1 . "\n" . $profile->address_line2;
        $doc->zip = $profile->zipcode;
        $doc->details = $profile->bio;
        if (!is_null($geo_loc)) {
            $doc->location = $geo_loc;
        }
        $update->addDocument($doc);
        $update->addCommit();
        $result = $client->update($update);
        return $result;
    }

    /**
     * Function to Delete a doc from Solr Index by ID
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Object Result Object after deleting
     */
    public function delete_doc($doc_id) {
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $update->addDeleteById($doc_id);
        $update->addCommit();
        $result = $client->update($update);
        return $result;
    }

    public function delete_query($index_type) {
        $client = new Solarium_Client($this->get_solr_config());
        $update = $client->createUpdate();
        $update->addDeleteQuery('type:' . $index_type);
        $update->addCommit();
        $result = $client->update($update);
        return $result->getStatus();
    }

    /**
     * Function to Get Geolocation from Google Geocoding API. Add/Edit of events 
     * will not likely to go over 50000/day. So it's safe
     * @author Mahbub
     * @copyright Blueliner Marketing
     * @version 0.1
     * @access public
     * @return Array
     */
    public function get_address($q) {
        $q = rawurlencode($q);
        $client = new Zend_Http_Client('http://maps.googleapis.com/maps/api/geocode/json?address=' . $q . '&sensor=true', array(
                    'maxredirects' => 0,
                    'timeout' => 30));
        /**
         * For Yahoo use this
         * http://where.yahooapis.com/v1/places.q('10001')?appid=Z2MTImzV34HWiBtH3itQbYPhBJDeuJnbgVtprSP9mJdryD6u9hWBmieaHbY0o8g-
         */
        $response = json_decode($client->request('POST')->getBody());
        if ($response->status == "OK") {
            $address = reset($response->results);
            return $address;
        }
        return false;
    }

}
<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Trunking\V1\Trunk;

use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;

class OriginationUrlContext extends InstanceContext {
    /**
     * Initialize the OriginationUrlContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $trunkSid The trunk_sid
     * @param string $sid The sid
     * @return \Twilio\Rest\Trunking\V1\Trunk\OriginationUrlContext 
     */
    public function __construct(Version $version, $trunkSid, $sid) {
        parent::__construct($version);
        
        // Path Solution
        $this->solution = array(
            'trunkSid' => $trunkSid,
            'sid' => $sid,
        );
        
        $this->uri = '/Trunks/' . rawurlencode($trunkSid) . '/OriginationUrls/' . rawurlencode($sid) . '';
    }

    /**
     * Fetch a OriginationUrlInstance
     * 
     * @return OriginationUrlInstance Fetched OriginationUrlInstance
     */
    public function fetch() {
        $params = Values::of(array());
        
        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );
        
        return new OriginationUrlInstance(
            $this->version,
            $payload,
            $this->solution['trunkSid'],
            $this->solution['sid']
        );
    }

    /**
     * Deletes the OriginationUrlInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Update the OriginationUrlInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return OriginationUrlInstance Updated OriginationUrlInstance
     */
    public function update($options = array()) {
        $options = new Values($options);
        
        $data = Values::of(array(
            'Weight' => $options['weight'],
            'Priority' => $options['priority'],
            'Enabled' => $options['enabled'],
            'FriendlyName' => $options['friendlyName'],
            'SipUrl' => $options['sipUrl'],
        ));
        
        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );
        
        return new OriginationUrlInstance(
            $this->version,
            $payload,
            $this->solution['trunkSid'],
            $this->solution['sid']
        );
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Trunking.V1.OriginationUrlContext ' . implode(' ', $context) . ']';
    }
}
<?php
namespace EmailValidator;

class Validator {
    public static $blacklistedDomains;
    public static $debounceApiKey;
    private $rulesets = [
        'by_email_domain' => 'validateByEmailDomain'
    ];
    public static $usedRule = [
        'by_email_domain'
    ];
    private $useDebounce = false;

    /* @description
     * validate email if it is not inside blacklisted domains
     * @return boolean
     */
    public function validate($email) {
        if ($this->useDebounce) {
            $urlValidate = "https://api.debounce.io/v1/?api=".self::$debounceApiKey."&email=".$email;
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $urlValidate);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $return = curl_exec($ch);
            return $return;
        } else {
            $status = 0;
            foreach(self::$usedRule as $ruleset) {
                if(($this)->{$this->rulesets[$ruleset]}($email)) {
                    $status = $status + 1;
                }
            }

            $return = json_encode(array(
                'success' => "$status"
            ));

            return $return;
        }
    }

    /* @description
     * add rule by key
     * @return boolean
     */

    public function ruleConfig($arrayOfRule) {
       return array_push(self::$usedRule, $arrayOfRule); 
    }

    /* @description
     * validate email by domain name
     * @return boolean
     */
    private function validateByEmailDomain($email) {
        $domain = explode('@', $email)[1];
        if (in_array($domain, self::$blacklistedDomains)) { 
            return false;
        } else {
            return true;
        }
    }

    /* @description
     * validate email by 3rd party debounce
     * @return boolean
     */

    public function useDebounce() {
        $this->useDebounce = true;
    }

    /* @description
     * set blacklisted domains
     * @return boolean
     */
    public function setBlacklistedDomains($domains = array()) {
        return self::$blacklistedDomains = $domains;
    }

    /* @description
     * get blacklisted domains
     * @return array
     */

    public function getBlacklistedDomains() {
        return self::$blacklistedDomains;
    }

    /* @description
     * append domain to blacklisteddomains
     * @return boolean
     */

    public function appendBlacklistedDomain($domain) {
        return array_push(self::$blacklistedDomains, $domain);
    }
}

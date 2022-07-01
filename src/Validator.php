<?php
namespace EmailValidator;

class Validator {
    public static $blacklistedDomains;

    /* @description
     * validate email if it is not inside blacklisted domains
     * @return boolean
     */
    public function validate($email) {
        $domain = explode('@', $email)[1];
        if (in_array($domain, self::$blacklistedDomains)) { 
            return false;
        } else {
            return true;
        }
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

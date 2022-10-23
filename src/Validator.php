<?php
namespace EmailValidator;
use Exception;

class Validator {
    public static $blacklistedDomains;
    protected $email;
    protected $username;
    protected $domain;
    protected $debounceApiKey;
    protected $debounceHost = "https://api.debounce.io/v1/";

    /* @description
     * Set debounceApiKey
     * @return string $debounceApiKey 
     */
    public function setDebounceApiKey($debounceApiKey) {
        return $this->debounceApiKey = $debounceApiKey;
    }

    /* @description
     * Set debounceHost for Mock API Test 
     * @return string $debounceHost
     */
    public function setDebounceHost($debounceHost) {
        return $this->debounceHost = $debounceHost;
    }

    /* @description
     * Validate email use debounce Integration
     * @return array $result
     */

    public function validateDebounce($email = null) {
        if (!$this->email || !$this->debounceApiKey) throw new Exception("Email and API Key must be set");

        $client = new \GuzzleHttp\Client();
        $urlDebounce = $this->debounceHost . "?api=" . $this->debounceApiKey . "&email=" . $this->email; 
        try {
            $response = $client->request('GET', $urlDebounce, [
                'headers' => [
                    'accept' => 'application/json',
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $response->getBody();
    }

    /* @description
     * Internally validate email by blacklisted domains array
     * Optional, should use db instead
     * @return boolean
     */
    public function validate($email = null) {
        $domain = explode('@', $email ? $email : $this->email)[1];
        return in_array($domain, self::$blacklistedDomains);
    }

    /* @description
     * Set Email string
     * @return void 
     */

    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
            $this->domain = explode('@', $email)[1];
            $this->username = explode('@', $email)[0];
        } else {
            throw new Exception("The email string is invalid");
        }
    }

    /* @description
     * Get Email string
     * @return string $email
     */
    public function getEmail() {
        return $this->email;
    }

    /* @description
     * Get Domain from Email string
     * @return string $domain
     */
    public function getDomain() {
        return $this->domain;
    }

    /* @description
     * Get Username string from Email string
     * @return string $username
     */
    public function getUsername() {
        return $this->username;
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

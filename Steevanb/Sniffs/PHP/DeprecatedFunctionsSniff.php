<?php

class Steevanb_Sniffs_PHP_DeprecatedFunctionsSniff extends Generic_Sniffs_PHP_DeprecatedFunctionsSniff
{
    /**
     * Fonctions autorisées, pour l'utilisation de php-mcrypt
     * @var array
     */
    protected $allowedDeprecatedFunctions = [
        'mcrypt_get_iv_size',
        'mcrypt_create_iv',
        'mcrypt_encrypt',
        'mcrypt_decrypt'
    ];

    public function __construct()
    {
        parent::__construct();

        foreach (array_keys($this->forbiddenFunctions) as $functionName) {
            if (in_array($functionName, $this->allowedDeprecatedFunctions)) {
                unset($this->forbiddenFunctions[$functionName]);
            }
        }
    }
}

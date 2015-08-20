<?php

    /**
     * Namespace
     * 
     */
    namespace Plugin\MailChimp;

    /**
     * Data
     * 
     */

    // API credentials
    $credentials = array(
        'apiKey' => '***',
    );

    // Lists
    $lists = array(
        'all' => 'abcd'
    );

    /**
     * Config storage
     * 
     */

    // Store
    \Plugin\Config::add(
        'TurtlePHP-MailChimpPlugin',
        array(
            'credentials' => $credentials,
            'lists' => $lists
        )
    );

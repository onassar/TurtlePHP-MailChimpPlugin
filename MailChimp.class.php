<?php

    // namespace
    namespace Plugin;

    // dependency check
    if (class_exists('\\Plugin\\Config') === false) {
        throw new \Exception(
            '*Config* class required. Please see ' .
            'https://github.com/onassar/TurtlePHP-ConfigPlugin'
        );
    }

    // dependency check
    if (class_exists('\\Drewm\\MailChimp') === false) {
        throw new \Exception(
            '*MailChimp* class required. Please see ' .
            'https://github.com/drewm/mailchimp-api'
        );
    }

    /**
     * MailChimp
     * 
     * MailChimp plugin for TurtlePHP
     * 
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class MailChimp
    {
        /**
         * _configPath
         *
         * @var    string
         * @access protected
         * @static
         */
        protected static $_configPath = 'config.default.inc.php';

        /**
         * _initiated
         *
         * @var    boolean
         * @access protected
         * @static
         */
        protected static $_initiated = false;

        /**
         * _resource
         *
         * @var    \Drewm\MailChimp
         * @access protected
         * @static
         */
        protected static $_resource;

        /**
         * _add
         *
         * @note   The set_error_handler and retore_error_handler calls below
         *         should allow the application logic to flow uninterrupted
         * @static
         * @access protected
         * @param  string $listId
         * @param  array $details
         * @return array|false
         */
        public static function _add($listId, array $details)
        {
            // Handle case where something (eg. connection) fails
            set_error_handler(function() {});
            $response = self::$_resource->call(
                'lists/subscribe',
                array(
                    'id'                => $listId,
                    'email'             => array('email' => $details['email']),
                    'merge_vars'        => $details['vars'],
                    'double_optin'      => false,
                    'update_existing'   => true,
                    'replace_interests' => false,
                    'send_welcome'      => false
                )
            );
            restore_error_handler();
            if (
                isset($response['status'])
                && isset($response['error'])
                && $response['error']
            ) {
                error_log(print_r($response['error'], true));
                return false;
            }
            return $response;
        }

        /**
         * _remove
         *
         * @note   The set_error_handler and retore_error_handler calls below
         *         should allow the application logic to flow uninterrupted
         * @note   200 status check is because CM sends a 201 upon successful
         *         addition of an email address
         * @static
         * @access protected
         * @param  string $listId
         * @param  string $email
         * @return false|array
         */
        public static function _remove($listId, $email)
        {
            // Handle case where something (eg. connection) fails
            set_error_handler(function() {});
            $response = self::$_resource->call(
                'lists/unsubscribe',
                array(
                    'id'                => $listId,
                    'email'             => array('email' => $email),
                    'delete_member'     => false,
                    'send_goodbye'      => false,
                    'send_notify'       => false
                )
            );
            restore_error_handler();
            if (
                isset($response['status'])
                && isset($response['error'])
                && $response['error']
            ) {
                error_log(print_r($response['error'], true));
                return false;
            }
            return $response;
        }

        /**
         * add
         *
         * @static
         * @access public
         * @param  string $listKey
         * @param  array $details
         * @return CS_REST_Wrapper_Result|false
         */
        public static function add($listKey, array $details)
        {
            $config = getConfig('TurtlePHP-MailChimpPlugin');
            $listId = $config['lists'][$listKey];
            $data = array(
                'email' => $details['email'],
                'vars' => $details
            );
            $response = self::_add($listId, $data);
            if ($response === false) {
                error_log(
                    'Error when attempting to add *' . ($details['email']) .
                    '* to MailChimp (list: ' . ($listId) . ')'
                );
            }
            return $response;
        }

        /**
         * remove
         *
         * @static
         * @access public
         * @param  string $listKey
         * @param  string $email
         * @return CS_REST_Wrapper_Result|false
         */
        public static function remove($listKey, $email)
        {
            $config = getConfig('TurtlePHP-MailChimpPlugin');
            $listId = $config['lists'][$listKey];
            $response = self::_remove($listId, $email);
            if ($response === false) {
                error_log(
                    'Error when attempting to remove *' . ($email) .
                    '* from MailChimp (list: ' . ($listId) . ')'
                );
            }
            return $response;
        }

        /**
         * init
         * 
         * @access public
         * @static
         * @return void
         */
        public static function init()
        {
            if (is_null(self::$_initiated) === false) {
                self::$_initiated = true;
                require_once self::$_configPath;
                $config = getConfig('TurtlePHP-MailChimpPlugin');
                $key = $config['credentials']['key'];
                self::$_resource = (new \Drewm\MailChimp($key));
            }
        }

        /**
         * setConfigPath
         * 
         * @access public
         * @param  string $path
         * @return void
         */
        public static function setConfigPath($path)
        {
            self::$_configPath = $path;
        }
    }

    // Config
    $info = pathinfo(__DIR__);
    $parent = ($info['dirname']) . '/' . ($info['basename']);
    $configPath = ($parent) . '/config.inc.php';
    if (is_file($configPath)) {
        MailChimp::setConfigPath($configPath);
    }

<?php
    /**
     * Class ClassmapAutoloader.php
     *
     * This is a super simple classmap autoloader to enable the fastest kind of autoload
     * when not using PSR.
     *
     * It uses a class map (a glorified hashtable) to find the classes it needs. Just make
     * sure you define a classmap file somewhere (in the form of a key => value array where
     * key is the class name and value is the full path to it) and declare the path to that
     * file in a global constant called CLASSMAP_PATH.
     *
     * @author  Swader
     * @since   January 2013
     * @version 1.0
     *
     */
    class ClassmapAutoloader
    {
        /**
         * Define a global namespace constant called CLASSMAP_PATH to point this
         * autoloader to the classmap
         *
         * @var array
         */
        static protected $aClassMap = array();

        public static function seek($sClassName)
        {
            if (!self::$aClassMap) {
                $aClassMap = array();
                require_once \CLASSMAP_PATH;
                self::$aClassMap = $aClassMap;
            }

            if (isset(self::$aClassMap[$sClassName])) {
                require_once self::$aClassMap[$sClassName];

                return true;
            }

            return false;
        }
    }

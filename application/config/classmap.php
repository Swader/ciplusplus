<?php

    /**
     * See the ClassmapAutoloader class for more information.
     * Default location is in APP_PATH/libraries/core_extended
     */

    $sCoreExtendedPath = APPPATH . 'libraries/core_extended';

    $aClassMap = array(
        'ExtendedController' => $sCoreExtendedPath . '/ExtendedController.php',
        'ExtendedModel'      => $sCoreExtendedPath . '/ExtendedModel.php',
        'Parser'             => $sCoreExtendedPath . '/Parser.php',
        'View'               => $sCoreExtendedPath . '/View.php'
    );

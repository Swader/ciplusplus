<?php
    /**
     * The View class is used to add layout and tags to views
     *
     * @category      View
     * @package       Core
     * @license       BSD License
     * @version       1.0.0.0
     * @since         2012-01-17
     * @author        Bruno Škvorc <bruno@skvorc.me>
     */
    class View
    {

        protected $sLayout = null;
        protected $sLayoutFragments = array();

        protected $aLocalMeta = array();

        protected $sViewFile = '';
        protected $sViewFolder = '';

        protected $aTagValues = array();

        protected $controller;

        /**
         * The constructor loads the site configuration and sets the site title
         */
        public function __construct(ExtendedController $controller)
        {
            $controller->config->load('site', false, false);
            $this->aTagValues['title'] = $controller->config->item('title');
            $this->controller          = $controller;
            $this->parser              = new Parser();
        }

        /**
         * Use this method to change the name of the view file you'd like to render
         * Useful when several actions share the same view file
         *
         * @param string $sName
         *
         * @return View
         */
        public function setTemplateFile($sName)
        {
            $this->sViewFile = $sName;

            return $this;
        }

        /**
         * Use this method to change the directory in which the app should
         * look for the view file. By default, it will correspond to the controller
         * path, but in the views folder. For example, if a controller called "welcome"
         * is in the controllers/admin folder, the app will look for the view in
         * views/admin/welcome/
         *
         * @param string $sString
         *
         * @return View
         */
        public function setTemplateFolder($sString)
        {
            $this->sViewFolder = $sString;

            return $this;
        }

        /**
         * Returns the set view file.
         * If no custom view file was set, returns the expected default
         *
         * @return string
         */
        public function getTemplateFile()
        {
            if (empty($this->sViewFile)) {
                $this->sViewFile = $this->controller->router->method;
            }

            return $this->sViewFile;
        }

        /**
         * Returns the set view folder.
         * If no custom view folder was set, returns the default one it will expect upon rendering
         *
         * @return string
         */
        public function getTemplateFolder()
        {
            if (empty($this->sViewFolder)) {
                return APPPATH . 'views/' . $this->controller->router->directory . '/' . $this->controller->router->class . '/';
            }

            return $this->sViewFolder;
        }

        /**
         * Overwrites site's title attribute with new value
         *
         * @param string $sString
         *
         * @return View
         */
        public function setTitle($sString)
        {
            $this->aTagValues['title'] = $sString;

            return $this;
        }

        /**
         * Appends a given string to the title.
         * I.E. If the title is "My Site" and "- Profiles" is passed into this
         * method, the new title will be "Profiles - My Site".
         *
         * @param string $sString
         *
         * @return View
         */
        public function appendToTitle($sString)
        {
            $this->aTagValues['title'] = $this->aTagValues['title'] . $sString;

            return $this;
        }

        /**
         * Prepends a given string to the title.
         * I.E. If the title is "My Site" and "Profiles - " is passed into this
         * method, the new title will be "Profiles - My Site".
         *
         * @param string $sString
         *
         * @return View
         */
        public function prependToTitle($sString)
        {
            $this->aTagValues['title'] = $sString . $this->aTagValues['title'];

            return $this;
        }

        /**
         * Returns the currently set title
         *
         * @return string
         */
        public function getTitle()
        {
            return $this->aTagValues['title'];
        }

        /**
         * Resets the title back to its default value
         *
         * @return View
         */
        public function resetTitle()
        {
            $this->aTagValues['title'] = $this->controller->config->item('title');

            return $this;
        }

        /**
         * Any defined tag gets rendered into the layout and the view.
         * For example, if a view template has a tag {{title}} and the
         * tag "title" is defined with a value, that tag is automatically
         * replaced with the value on rendering.
         *
         * @param string $sTag
         * @param mixed  $sValue
         *
         * @return View
         * @throws Exception
         */
        public function tagValueSet($sTag, $sValue)
        {
            if (in_array($sTag, array('content', 'title', 'meta'))) {
                throw new Exception('"content", "title" and "meta" are protected tags and cannot be used.');
            }

            if (is_scalar($sTag) && is_scalar($sValue)) {
                $this->aTagValues[$sTag] = $sValue;

                return $this;
            }
            throw new Exception('Both values must be scalar in order to be parsable.');
        }

        /**
         * Bulk function for tagValueSet(). Array needs to contain key => value pairs
         *
         * @param array $aArray
         *
         * @return View
         */
        public function tagValueSetMulti($aArray)
        {
            foreach ($aArray as $sKey => &$mValue) {
                $this->tagValueSet($sKey, $mValue);
            }

            return $this;
        }

        /**
         * Removes a set tag
         *
         * @param string $sTag
         *
         * @return View
         */
        public function tagValueRemove($sTag)
        {
            if (array_key_exists($sTag, $this->aTagValues)) {
                unset($this->aTagValues[$sTag]);
            }

            return $this;
        }

        /**
         * Returns the value of the defined tag or null if none is set
         *
         * @param string $sTag
         *
         * @return mixed
         */
        public function tagValueGet($sTag)
        {
            if (array_key_exists($sTag, $this->aTagValue)) {
                return $this->aTagValues[$sTag];
            } else {
                return null;
            }
        }

        /**
         * Renders the view, parses tags, extracts variables
         *
         * @param array $aData
         *
         * @return bool
         * @throws Exception
         */
        public function render($aData = array())
        {
            extract($aData);

            $sLayoutFile = $this->getLayoutFolder() . 'layout.php';
            if (!is_readable($sLayoutFile)) {
                throw new \Exception('Layout file not found: ' . $sLayoutFile);
            } else {
                ob_start();
                require_once $sLayoutFile;
                $sRenderedLayout = ob_get_clean();
            }

            $sFile = ($this->getTemplateFile() == '')
                ? $this->controller->router->method : $this->getTemplateFile();

            $sPath = $this->getTemplateFolder() . $sFile . '.php';

            if (!is_readable($sPath)) {
                throw new \Exception('File not found: ' . $sPath);
            } else {
                ob_start();
                require_once $sPath;
                $sRenderedView = ob_get_clean();
            }

            $this->parser->doParseRef($this->aTagValues, $sRenderedView);
            $this->aTagValues['content'] = $sRenderedView;
            $this->aTagValues['meta']    = $this->returnRenderedMeta();
            $this->parser->doParseRef($this->aTagValues, $sRenderedLayout);

            echo $sRenderedLayout;

            return true;
        }

        /**
         * Resets layout to default, as if you never did anything with it.
         * This means the default layout is used.
         *
         * @return ExtendedController
         */
        public function resetLayout()
        {
            $this->sLayout = null;

            return $this;
        }

        /**
         * Sets the layout file to be used as the theme
         *
         * @param $sName
         *
         * @return ExtendedController
         */
        public function setLayout($sName)
        {
            $this->sLayout = (string)$sName;

            return $this;
        }

        /**
         * Returns the defined layout, or null if not defined
         * If layout is null or "default", default layout is used.
         *
         * @return null
         */
        public function getLayout()
        {
            return $this->sLayout;
        }

        /**
         * Returns the path to the folder of the defined layout
         *
         * @return string
         */
        protected function getLayoutFolder()
        {
            if (empty($this->sLayout)) {
                $this->setLayout('default');
            }

            return LAYOUTS_FOLDER . $this->getLayout() . '/';
        }

        /**
         * Fetches and renders a layout fragment
         *
         * @param string  $sName
         * @param array   $aData
         *
         * @return View
         */
        public function renderLayoutFragment($sName, $aData = array())
        {
            echo $this->parser->doParse($this->aTagValues, $this->fetchLayoutFragment($sName, $aData));

            return $this;
        }

        /**
         * Fetches a layout fragment and returns it for rendering or storing
         *
         * @param string $sFragmentName
         * @param array  $aData
         *
         * @return string
         * @throws Exception
         */
        public function fetchLayoutFragment($sFragmentName, $aData = array())
        {
            if (is_array($aData)) {
                extract($aData);
            }
            $sPath = $this->getLayoutFolder() . 'fragments/' . $sFragmentName . '.php';
            if (!is_readable($sPath)) {
                throw new \Exception('File fragment ' . $sFragmentName . ' not found in ' . $sPath);
            } else {
                ob_start();
                require_once $sPath;

                return ob_get_clean();
            }
        }

        public function addMeta($aArray)
        {

        }

        /**
         * Renders the meta and returns it as a string
         *
         * @return string
         */
        protected function returnRenderedMeta()
        {
            $aMeta = $this->getMeta();
            if (!empty($aMeta)) {
                ob_start();
                foreach ($aMeta as $sName => &$aValues) {
                    if (
                        is_string($sName)
                        && is_array($aValues)
                        && !empty($aValues)
                        && isset($aValues['content'])
                        && isset($aValues['attr'])
                    ) {
                        echo '<meta ' . $aValues['attr'] . '="' . $sName . '" content="' . $aValues['content'] . '" />';
                    }
                }

                return ob_get_clean();
            }

            return '';
        }

        /**
         * Loads the general config meta and returns it merged with the locally defined meta from
         * the controller child instance
         *
         * @return array
         */
        public function getMeta()
        {
            return array_merge($this->controller->config->item('meta'), $this->aLocalMeta);
        }

    }

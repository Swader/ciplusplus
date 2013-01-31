<?php
    /**
     * See info.txt
     *
     * The ExtendedController has been extended with layout functionality.
     * See the /views/layout folder for a demo of a Twitter Bootstrap based
     * theme.
     *
     * @category      Core
     * @package       Core
     * @license       BSD License
     * @version       1.0.0.0
     * @since         2012-01-17
     * @author        Bruno Škvorc <bruno@skvorc.me>
     */
    class ExtendedController extends CI_Controller
    {

        protected $sLayout = null;
        protected $sLayoutFragments = array();

        protected $aLocalMeta = array();

        protected $sViewFile = '';
        protected $sViewFolder = '';

        protected $sTitle = '';
        protected $aTagValues = array();

        protected $view;

        /**
         * The constructor loads the site configuration and sets the site title
         */
        public function __construct()
        {
            parent::__construct();
            $this->view = new View($this);
            $this->config->load('site', false, false);
            $this->sTitle = $this->config->item('title');
            $this->tagValueSet('title', $this->sTitle);
        }

        /**
         * Use this method to change the name of the view file you'd like to render
         * Useful when several actions share the same view file
         *
         * @param string $sName
         *
         * @return ExtendedController
         */
        public function setViewFile($sName)
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
         * @return ExtendedController
         */
        public function setViewFolder($sString)
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
        public function getViewFile()
        {
            if (empty($this->sViewFile)) {
                $this->sViewFile = $this->router->method;
            }

            return $this->sViewFile;
        }

        /**
         * Returns the set view folder.
         * If no custom view folder was set, returns the default one it will expect upon rendering
         *
         * @return string
         */
        public function getViewFolder()
        {
            if (empty($this->sViewFolder)) {
                return APPPATH . 'views/' . $this->router->directory . '/' . $this->router->class . '/';
            }

            return $this->sViewFolder;
        }

        /**
         * Overwrites site's title attribute with new value
         *
         * @param string $sString
         *
         * @return ExtendedController
         */
        public function setTitle($sString)
        {
            $this->sTitle = $sString;

            return $this;
        }

        /**
         * Appends a given string to the title.
         * I.E. If the title is "My Site" and "- Profiles" is passed into this
         * method, the new title will be "Profiles - My Site".
         *
         * @param string $sString
         *
         * @return ExtendedController
         */
        public function appendToTitle($sString)
        {
            $this->sTitle = $this->sTitle . $sString;

            return $this;
        }

        /**
         * Prepends a given string to the title.
         * I.E. If the title is "My Site" and "Profiles - " is passed into this
         * method, the new title will be "Profiles - My Site".
         *
         * @param string $sString
         *
         * @return ExtendedController
         */
        public function prependToTitle($sString)
        {
            $this->sTitle = $sString . $this->sTitle;

            return $this;
        }

        /**
         * Returns the currently set title
         *
         * @return string
         */
        public function getTitle() {
            return $this->sTitle;
        }

        public function tagValueSet($sTag, $sValue) {

        }

        public function tagValueRemove($sTag) {

        }

        public function tagValueGet($sTag) {

        }

        protected function render($aData = array(), $sFile = '', $sAltPath = '')
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

            $aTagValues = array(
                'title' => $this->getTitle(),
                'meta' => $this->getMeta(),
                ''
            );


            $sFile = (empty($sFile)) ? $this->router->method : $sFile;
            $sPath = $this->getViewFolder() . $sFile . '.php';
            if (!empty($sAltPath)) {
                $sPath = $sAltPath;
            }
            if (!is_readable($sPath)) {
                throw new \Exception('File not found: ' . $sPath);
            } else {
                require_once $sPath;
            }

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
         * @param       $sName
         * @param array $aData
         *
         * @return ExtendedController
         */
        public function renderLayoutFragment($sName, $aData = array())
        {
            echo $this->fetchLayoutFragment($sName, $aData);

            return $this;
        }

        /**
         * Fetches a layout fragment and returns it for rendering or storing
         *
         * @param       $sFragmentName
         * @param array $aData
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

        protected function renderMeta()
        {
            $aMeta = $this->getMeta();
            if (!empty($aMeta)) {
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
            }

            return $this;
        }

        /**
         * Loads the general config meta and returns it merged with the locally defined meta from
         * the controller child instance
         *
         * @return array
         */
        protected function getMeta()
        {
            return array_merge($this->config->item('meta'), $this->aLocalMeta);
        }

    }

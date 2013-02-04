<?php
/**
 * The View class is used to add layout and tags to views. It also handles view and layout fragments
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

    /** @var null */
    protected $sLayout = null;
    /** @var array */
    protected $sLayoutFragments = array();

    /** @var array */
    protected $aLocalMeta = null;

    /** @var string */
    protected $sViewFile = '';
    /** @var string */
    protected $sViewFolder = '';

    /** @var array */
    protected $aTagValues = array();

    /** @var \ExtendedController */
    protected $controller;

    /** @var array */
    protected $aProtectedTags = array('title', 'meta', 'content');

    /** @var array */
    public $data = array();

    /**
     * The constructor loads the site configuration and sets the site title
     */
    public function __construct(ExtendedController $controller)
    {
        $controller->config->load('site', false, false);
        $this->aTagValues['title'] = $controller->config->item('title');
        $this->controller = $controller;
        $this->getMeta();
        $this->parser = new Parser();
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
        if (in_array($sTag, $this->aProtectedTags)) {
            throw new Exception('"content", "title" and "meta" are protected tags and cannot be used.');
        }

        if (is_scalar($sTag) && (is_scalar($sValue) || (is_object($sValue) && method_exists($sValue, '__toString')))) {
            $this->aTagValues[$sTag] = $sValue;

            return $this;
        }
        throw new Exception('Tag => Values must be scalar => scalar pairs, or value must be object with __toString method.');
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
        if (array_key_exists($sTag, $this->aTagValues)) {
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
        $aMergedData = array_merge($aData, $this->data);
        extract($aMergedData);

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
            throw new \Exception('View file not found: ' . $sPath);
        } else {
            ob_start();
            require_once $sPath;
            $sRenderedView = ob_get_clean();
        }

        foreach ($aMergedData as $key => &$value) {
            $sKeyName = 'var_'.$key;
            $this->aTagValues[$sKeyName] = $value;
        }

        $aTags = array_merge($this->parser->extractTags(array($sRenderedLayout, $sRenderedView)));
        foreach ($aTags as &$sTag) {
            if (strpos($sTag, 'vf_') === 0) {
                // View fragment tag detected
                $sFragmentContent = $this->fetchViewFragment(str_replace('vf_', '', $sTag), $this->aTagValues);
            } else if (strpos($sTag, 'lf_') === 0) {
                // Layout fragment tag detected
                $sFragmentContent = $this->fetchLayoutFragment(str_replace('lf_', '', $sTag), $this->aTagValues);
            }
            if (isset($sFragmentContent)) {
                $this->parser->doParseRef($this->aTagValues, $sFragmentContent);
                $this->aTagValues[$sTag] = $sFragmentContent;
            }
        }

        $this->parser->doParseRef($this->aTagValues, $sRenderedView);
        $this->aTagValues['content'] = $sRenderedView;
        $this->aTagValues['meta'] = $this->returnRenderedMeta();
        $this->parser->doParseRef($this->aTagValues, $sRenderedLayout);

        echo $sRenderedLayout;
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
            throw new \Exception('Layout fragment ' . $sFragmentName . ' not found in ' . $sPath);
        } else {
            ob_start();
            require_once $sPath;

            return ob_get_clean();
        }
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
    public function fetchViewFragment($sFragmentName, $aData = array())
    {
        if (is_array($aData)) {
            extract($aData);
        }
        $sPath =  \VIEW_FRAGMENTS_FOLDER . $sFragmentName . '.php';
        if (!is_readable($sPath)) {
            throw new \Exception('View fragment ' . $sFragmentName . ' not found in ' . $sPath);
        } else {
            ob_start();
            require_once $sPath;

            return ob_get_clean();
        }
    }

    /**
     * Adds a meta entry to the site
     * @param string $sName The name of the meta entry (e.g. viewport)
     * @param array $aArray Array of the meta's values.
     * MUST contain keys "attr" (e.g. "name"), and "content" (e.g. "width=device-width")
     *
     * Example use: $this->view->addMeta('viewport', array('attr' => 'name', 'content' => 'width=device-width'))
     *
     * Meta needs to be unique per name, so a new one set to the same name will overwrite the old one.
     *
     * @throws Exception
     * @return View
     */
    public function addMeta($sName, $aArray)
    {
        if (isset($aArray['attr']) && isset($aArray['content'])) {
            $this->aLocalMeta[$sName] = $aArray;
        } else {
            throw new \Exception('Invalid meta format. Please read docs.');
        }
        return $this;
    }

    /**
     * Removes the meta entry of a given name
     * @param string $sName
     * @return View
     */
    public function removeMeta($sName) {
        if (isset($this->aLocalMeta[$sName])) {
            unset($this->aLocalMeta[$sName]);
        }
        return $this;
    }

    /**
     * Renders the meta and returns it as a string
     *
     * @return string
     */
    protected function returnRenderedMeta()
    {
        if (!empty($this->aLocalMeta)) {
            ob_start();
            foreach ($this->aLocalMeta as $sName => &$aValues) {
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
        if ($this->aLocalMeta === null) {
            $this->aLocalMeta = $this->controller->config->item('meta');
        }
        return $this->aLocalMeta;
    }

}

<?php
/**
 * The ExtendedController has been extended with request method functionality.
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

    /** @var \View */
    protected $view;

    /**
     * The constructor loads the site configuration and sets the site title
     */
    public function __construct()
    {
        parent::__construct();
        $this->view = new View($this);
    }

    /**
     * Retrieves the specified POST parameter, if it exists. If not, the default is retrieved.
     * If a $ctype is specified, the param is also checked against this condition. If this test returns false, the default is returned.
     *
     * @param string $value Name of param to retrieve
     * @param mixed $default The default value that gets returned in case the param does not exist and/or fails the ctype check.
     * @param string $ctype See ctype functions for options. Options are cytpe function name suffixes (e.g. 'alnum' or 'digit')
     * @return mixed
     */
    protected function getPostParam($value, $default = null, $ctype = null)
    {
        return $this->getParam($value, $default, $ctype, 'POST');
    }

    /**
     * Retrieves the specified GET parameter, if it exists. If not, the default is retrieved.
     * If a $ctype is specified, the param is also checked against this condition. If this test returns false, the default is returned.
     *
     * @param string $value Name of param to retrieve
     * @param mixed $default The default value that gets returned in case the param does not exist and/or fails the ctype check.
     * @param string $ctype See ctype functions for options. Options are cytpe function name suffixes (e.g. 'alnum' or 'digit')
     * @return mixed
     */
    protected function getGetParam($value, $default = null, $ctype = null)
    {
        return $this->getParam($value, $default, $ctype, 'GET');
    }

    /**
     * Retrieves the specified FILE parameter, if it exists. If not, the default is retireved.
     *
     * @param string $value Name of param to retrieve
     * @param mixed $default The default value that gets returned in case the param does not exist and/or fails the ctype check.
     * @return mixed
     */
    protected function getFileParam($value, $default = null)
    {
        return $this->getParam($value, $default, null, 'FILE');
    }

    /**
     * Retrieves the specified parameter, if it exists. If not, the default is retrieved.
     * If a $ctype is specified, the param is also checked against this condition. If this test returns false, the default is returned.
     *
     * @param string $value Name of param to retrieve
     * @param mixed $default The default value that gets returned in case the param does not exist and/or fails the ctype check.
     * @param string $ctype See ctype functions for options. Options are cytpe function name suffixes (e.g. 'alnum' or 'digit')
     * @param string $method Can be POST, GET, FILE or * (* means any, in this order: GET, POST, FILE)
     * @throws \Exception
     * @return mixed
     */
    protected function getParam($value, $default = null, $ctype = null, $method = '*')
    {
        $param = $default;

        switch (strtoupper($method)) {
            case "GET":
                if (isset($_GET[$value]))
                    $param = $_GET[$value];
                break;
            case "POST":
                if (isset($_POST[$value]))
                    $param = $_POST[$value];
                break;
            case "FILE":
                if (isset($_FILES[$value]))
                    $param = $_FILES[$value];
                break;
            case "*":
                if (isset($_GET[$value]))
                    $param = $_GET[$value];
                elseif (isset($_POST[$value]))
                    $param = $_POST[$value]; elseif (isset($_FILES[$value]))
                    $param = $_FILES[$value];
                break;
            default:
                break;
        }

        if ($ctype) {
            $sFunctionName = 'ctype_' . $ctype;
            if (!function_exists($sFunctionName)) {
                throw new \Exception('Invalid ctype function: ' . $sFunctionName);
            } else {
                if (!$sFunctionName($param)) {
                    $param = $default;
                }
            }
        }

        return $param;
    }

    /**
     * Fetches one array of all params, merged GET, POST and FILES. The order of importance is ascending, which means POST overrides GET, and FILES overrides POST.
     * @return array
     */
    protected function getAllParams()
    {
        return array_merge($_GET, $_POST, $_FILES);
    }

    /**
     * Redirects user to the page he came from. Useful when logging in to return user to whichever site he used to log in.
     * If the referrer is identical to the current page, the user is redirected to the home page to avoid an infinite loop.
     * @return void
     */
    protected function redirectToReferer()
    {
        $url = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if ($_SERVER['HTTP_REFERER'] == $url) {
            $this->_redirect($this->config->item('base_url'));
        }
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Redirects to any url
     * @param  $url
     * @return void
     */
    protected function redirect($url)
    {
        if (strpos($url, "/") === 0)
            $url = rtrim(WEBPATH, "/") . $url;
        header("Location: " . $url);
        return;
    }

    /**
     * Is the request method POST?
     * @return bool
     */
    protected function requestIsPost()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST');
    }

    /**
     * Is the request method GET?
     * @return bool
     */
    protected function requestIsGet()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'GET');
    }

    /**
     * Is the request method PUT?
     * @return bool
     */
    protected function requestIsPut()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'PUT');
    }

    /**
     * Is the request method DELETE?
     * @return bool
     */
    protected function requestIsDelete()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'DELETE');
    }


}

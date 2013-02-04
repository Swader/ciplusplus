<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 */
class Welcome extends ExtendedController
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *         http://example.com/index.php/welcome
     *    - or -
     *         http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this->view->render();
    }

    public function demo()
    {

        /*
         * Uncomment this to create the table
        $this->load->model('Test_model', '', true);
        $oModel = new Test_model();
        $oModel->createTable();
        */

        $this->view->render();
    }

    public function urldemo() {
        $aUrlParams = $this->uri->uri_to_assoc(3);
        if (!isset($aUrlParams['param'])) {
            $aUrlParams['param'] = 1;
        }

        $this->load->model('Test_model', '', true);

        $this->view->render(array(
            'databaseData' => $this->Test_model->getValue($aUrlParams['param']),
            'id' => $aUrlParams['param']
        ));
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

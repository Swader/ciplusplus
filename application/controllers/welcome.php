<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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

            $aUrlParams = $this->uri->uri_to_assoc(3);
            if (!isset($aUrlParams['id'])) {
                $aUrlParams['id'] = 1;
            }

            $this->load->model('Test_model', '', true);

            $this->view->render(array(
                'databaseData' => $this->Test_model->getValue($aUrlParams['id']),
                'id'           => $aUrlParams['id']
            ));

        }

        public function bootstrapdemo() {
            $this->view->render();
        }

    }

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

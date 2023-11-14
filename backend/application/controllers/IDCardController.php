<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class IDCardController extends CI_Controller
{
    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('IDCard', 'id_card');

        $this->load->helper('cookie');
        $this->load->dbforge();
        $this->load->library('image_lib');
    }

    public function index()
    {
        $filter = $this->input->get();

        $data = $this->id_card->findAll($filter);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function getPaginated()
    {
        $filter = $this->input->get();

        $data = $this->id_card->findAllPaginated($filter);
        if (!$data) {
            $data = [];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function show($id)
    {
        $data = $this->id_card->find($id);
        if (!$data) {
            $data = (object)[];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function store()
    {
        $data = $this->input->post();
        $method = $data['method'];
        unset($data['method']);

        $config['upload_path']          = './uploads/photo/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|jfif';
        // $config['max_size']             = 5000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        // $config['file_name'] = time() . "_" . preg_replace('/[^A-Za-z0-9.]/', "", $_FILES['photo']['name']);
        $config['file_name'] = "$data[nik]." . explode(".",  $_FILES['photo']['name'])[1];
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('photo')) {
            // echo 'error';
            // $error = json_encode(array('error' => $this->upload->display_errors()));
            var_dump($this->upload->display_errors()) ;exit;

            // $this->load->view('upload_form', $error);
        } else {
            // $data = array('upload_data' => $this->upload->data());
            // echo 'success';
            // $this->load->view('upload_success', $data);
            $data['photo'] = "/$config[file_name]";
            if ($method == 1) {
                $image_data = $this->upload->data();
                list($width, $height) = getimagesize($image_data['full_path']);

                // $this->load->library('imagick_lib');
                // $this->imagick_lib->readImage($image_data['full_path']);
                // $this->imagick_lib->cropImage(100, 100, 0, 0);
                $config['image_library'] = 'gd2';
                $config['source_image'] = $image_data['full_path'];
                $config['maintain_ratio'] = FALSE;
                $config['width'] = $height * 3 / 4;
                $config['height'] = $height;
                $config['x_axis'] = ($width / 2) - ($config['width'] / 2);
                $config['y_axis'] = 0;
                $this->image_lib->initialize($config);
                $this->image_lib->crop();
                $this->image_lib->clear();
            }
        }
        // exit;

        if ($this->id_card->save($data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'ID Card stored successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'ID Card already registered.'
                ]));
        }
    }

    public function updateAndMerge()
    {
        $companyCode = $this->input->post('company_code');
        $config['upload_path']          = './uploads/xls/';
        $config['allowed_types']        = 'xls|xlsx|csv';
        // $config['max_size']             = 5000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        $retVal = 1;
        if (!empty($_FILES['revision']['name'])) {
            // $idCard = $this->id_card->find($id);
            // unlink("./uploads/photo$idCard[photo]");
            $config['file_name'] = time() . "_" . preg_replace('/[^A-Za-z0-9.]/', "", $_FILES['revision']['name']);
            // $config['file_name'] = $idCard['nik'].".".explode(".",  $_FILES['photo']['name'])[1];
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('revision')) {
                $error = json_encode(array('error' => $this->upload->display_errors()));
                // echo 5;
                $retVal = 0;
                // $this->load->view('upload_form', $error);
            } else {
                $file_data     = $this->upload->data();
                $file_name     = $config['upload_path'] . $file_data['file_name'];
                $arr_file     = explode('.', $file_name);
                $extension     = end($arr_file);
                if ('csv' == $extension) {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                } else if ('xls' == $extension) {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                } else
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

                // $fields = array(
                //     'nik' => array(
                //         'type' => 'VARCHAR',
                //         'constraint' => '8',
                //         'unique' => TRUE,
                //     ),
                //     'name' => array(
                //         'type' => 'VARCHAR',
                //         'constraint' => '50',
                //         'null'=>true,
                //     ),
                //     'cost_center' => array(
                //         'type' => 'VARCHAR',
                //         'constraint' => '50',
                //         'null'=>true,
                //     ),
                //     'induction_date' => array(
                //         'type' => 'DATE',
                //         'null'=>true,
                //     ),
                //     'company_code' => array(
                //         'type' => 'VARCHAR',
                //         'constraint' => '5',
                //         'null'=>true,
                //     ),
                // );
                $currUID = uniqid();
                // $tmpTable = "tmp_$currUID";
                // $this->dbforge->add_field($fields)->create_table($tmpTable);

                $spreadsheet     = $reader->load($file_name);
                $sheet_data     = $spreadsheet->getActiveSheet()->toArray();
                $list             = [];
                foreach ($sheet_data as $key => $val) {
                    if ($key != 0) {
                        $list[] = [
                            'nik'                    => $val[0],
                            'name'            => $val[1],
                            'cost_center'                => $val[2],
                            'induction_date'                    => $val[3],
                            'company_code'                    => $companyCode,
                            'uid'                    => $currUID,
                        ];
                    }
                }
                if (file_exists($file_name))
                    unlink($file_name);
                if (count($list) > 0) {
                    // var_dump($list);exit;
                    $result     = $this->id_card->saveStagged($list);
                    if ($result) {
                        if (!$this->id_card->mergeStagged($currUID)) {
                            // echo 4;
                            $retVal = 0;
                        }
                    } else {
                        // echo 3;
                        $retVal = 0;
                    }
                    // $this->id_card->deleteStagged($currUID);
                } else {
                    // echo 2;
                    $retVal = 0;
                }
                // $this->dbforge->drop_table($tmpTable, TRUE);
            }
        } else {
            // echo 1;
            $retVal = 0;
        }


        if ($retVal) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'ID Card revised successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'Error. Please check the uploaded file again.'
                ]));
        }
    }

    public function import()
    {
        $path         = 'documents/users/';
        $json         = [];
        $this->upload_config($path);
        if (!$this->upload->do_upload('file')) {
            $json = [
                'error_message' => showErrorMessage($this->upload->display_errors()),
            ];
        } else {
            $file_data     = $this->upload->data();
            $file_name     = $path . $file_data['file_name'];
            $arr_file     = explode('.', $file_name);
            $extension     = end($arr_file);
            if ('csv' == $extension) {
                $reader     = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader     = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $spreadsheet     = $reader->load($file_name);
            $sheet_data     = $spreadsheet->getActiveSheet()->toArray();
            $list             = [];
            foreach ($sheet_data as $key => $val) {
                if ($key != 0) {
                    $result     = $this->user->get(["country_code" => $val[2], "mobile" => $val[3]]);
                    if ($result) {
                    } else {
                        $list[] = [
                            'name'                    => $val[0],
                            'country_code'            => $val[1],
                            'mobile'                => $val[2],
                            'email'                    => $val[3],
                            'city'                    => $val[4],
                            'ip_address'            => $this->ip_address,
                            'created_at'             => $this->datetime,
                            'status'                => "1",
                        ];
                    }
                }
            }
            if (file_exists($file_name))
                unlink($file_name);
            if (count($list) > 0) {
                $result     = $this->user->add_batch($list);
                if ($result) {
                    $json = [
                        'success_message'     => showSuccessMessage("All Entries are imported successfully."),
                    ];
                } else {
                    $json = [
                        'error_message'     => showErrorMessage("Something went wrong. Please try again.")
                    ];
                }
            } else {
                $json = [
                    'error_message' => showErrorMessage("No new record is found."),
                ];
            }
        }
        echo json_encode($json);
    }

    public function update($id)
    {
        $data = $this->input->post();
        $method = $data['method'];
        unset($data['method']);

        $config['upload_path']          = './uploads/photo/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|jfif';
        // $config['max_size']             = 5000;
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);


        if (!empty($_FILES['photo']['name'])) {
            $idCard = $this->id_card->find($id);
            if (isset($idCard['photo'])) {
                if (file_exists("./uploads/photo$idCard[photo]")) {
                    unlink("./uploads/photo$idCard[photo]");
                }
            }
            // $config['file_name'] = time() . "_" . preg_replace('/[^A-Za-z0-9.]/', "", $_FILES['photo']['name']);
            $config['file_name'] = "$idCard[nik]." . explode(".",  $_FILES['photo']['name'])[1];
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('photo')) {
                // $error = json_encode(array('error' => $this->upload->display_errors()));

                // $this->load->view('upload_form', $error);
            } else {
                // $data = array('upload_data' => $this->upload->data());

                // $this->load->view('upload_success', $data);
                $data['photo'] = "/$config[file_name]";
                if ($method == 1) {
                    $image_data = $this->upload->data();
                    list($width, $height) = getimagesize($image_data['full_path']);


                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $image_data['full_path'];
                    $config['maintain_ratio'] = FALSE;
                    $config['width'] = $height * 3 / 4;
                    $config['height'] = $height;
                    $config['x_axis'] = ($width / 2) - ($config['width'] / 2);
                    $config['y_axis'] = 0;
                    $this->image_lib->initialize($config);
                    $this->image_lib->crop();
                    $this->image_lib->clear();
                }
            }
        }


        if ($this->id_card->update($id, $data)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'ID Card updated successfully'
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'message' => 'ID Card already registered.'
                ]));
        }
    }

    public function delete($id)
    {
        $idCard = $this->id_card->find($id);
        if (isset($idCard['photo'])) {
            unlink("./uploads/photo$idCard[photo]");
        }
        if ($this->id_card->destroy($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'message' => 'ID Card deleted successfully'
                ]));
        }
    }

    public function downloadTemplate()
    {
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        // $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        //     ->setLastModifiedBy('Maarten Balliauw')
        //     ->setTitle('Office 2007 XLSX Test Document')
        //     ->setSubject('Office 2007 XLSX Test Document')
        //     ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
        //     ->setKeywords('office 2007 openxml php')
        //     ->setCategory('Test result file');

        // Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Nik')
            ->setCellValue('B1', 'Name')
            ->setCellValue('C1', 'Cost Center')
            ->setCellValue('D1', 'Induction Date');


        foreach (range('B', 'D') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        //fill color skublue
        $spreadsheet->getActiveSheet()->getStyle('A1:D1')
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('bbdefb');

        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);


        // Miscellaneous glyphs, UTF-8
        // $spreadsheet->setActiveSheetIndex(0)
        //     ->setCellValue('A4', 'Miscellaneous glyphs')
        //     ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

        // Rename worksheet
        // $spreadsheet->getActiveSheet()->setTitle('Simple');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Xlsx)
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment;filename="01simple.xlsx"');
        // header('Cache-Control: max-age=0');
        // // If you're serving to IE 9, then the following may be needed
        // header('Cache-Control: max-age=1');

        // // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        // header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        // header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'op' => 'ok',
            'file' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
            // 'image' => FCPATH . 'public/uploads/images/' . $newName, 'contoh' => __DIR__ . '/resources/logo_ubuntu_transparent.png', '__DIR__' => __DIR__
        );

        die(json_encode($response));
    }
}

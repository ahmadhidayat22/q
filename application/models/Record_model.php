<?php
use GuzzleHttp\Client;

class Record_model extends CI_Model {

    private $client;

    public function __construct() {
        $this->client = new Client([
            // TODO: Tambahkan Base URL API
            'base_uri' => "http://localhost:3000",
        ]);
    }

    public function getDataCount() {
        $response = $this->client->request('GET', '/dashboard', []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result[0];
    }

    public function getLastTenRecords() {
        $response = $this->client->request('GET', '/getlast10records', []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function getTopExpense() {
        $response = $this->client->request('GET', '/gettopexpense', []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function getAllRecords() {
        $response = $this->client->request('GET', '/getrecords', []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function getRecordById($id) {
        $response = $this->client->request('GET', '/getrecord/'.$id, []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result[0];
    }

    public function insertRecord() {
        $type = $this->input->post('recordtype');
        $amount = $this->input->post('amount');

        if ($type == "expense") {
            $amount = $amount * -1;
        }

        $attachment = null;
        if (!empty($_FILES['attachment']['name'])) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'gif|jpg|png';
            $config['max_size']             = 2048;
            $config['file_name']            = time() . '_' . $_FILES['attachment']['name'];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $attachment = $this->upload->data('file_name');
            } else {
                $attachment = null;
            }
        }
        $files= null;
        if (!empty($_FILES['attachment']['tmp_name'])) {
            $files = fopen($_FILES['attachment']['tmp_name'], 'r');
        }
        error_log(print_r($files, true));
        

        $response = $this->client->request('POST', '/insertrecord', [
            'multipart' => [
                [
                    'name' => 'amount',
                    'contents' => $amount
                ],
                [
                    'name' => 'name',
                    'contents' => $this->input->post('name')
                ],
                [
                    'name' => 'date',
                    'contents' => $this->input->post('date')
                ],
                [
                    'name' => 'notes',
                    'contents' => $this->input->post('notes')
                ],
                [
                    'name' => 'attachment',
                    'contents' => $files
                ]
            ]
        ]);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }
    public function updateRecord($id) {
        $type = $this->input->post('recordtype');
        $amount = $this->input->post('amount');

        if ($type == "expense") {
            $amount = $amount * -1;
        }

        $files;
        if ($_FILES['attachment']['tmp_name'] !== "") {
            $files = fopen($_FILES['attachment']['tmp_name'], 'r');
        }

        $response = $this->client->request('PUT', '/editrecord/'.$id, [
            'multipart' => [
                [
                    'name' => 'amount',
                    'contents' => $amount
                ],
                [
                    'name' => 'name',
                    'contents' => $this->input->post('name')
                ],
                [
                    'name' => 'date',
                    'contents' => $this->input->post('date')
                ],
                [
                    'name' => 'notes',
                    'contents' => $this->input->post('notes')
                ],
                [
                    'name' => 'attachment',
                    'contents' => $files
                ]
            ]
        ]);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function deleteRecord($id) {
        $response = $this->client->request('DELETE', '/deleterecord/'.$id, []);
        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }
}
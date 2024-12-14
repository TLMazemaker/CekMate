<?php

require APPPATH . '/libraries/REST_Controller.php';

require_once "vendor/autoload.php";
        
use Google\Cloud\Storage\StorageClient;
use Restserver\Libraries\REST_Controller;

class Data_deteksi extends REST_Controller
{

    /**
     * CONSTRUCTOR | LOAD MODEL
     *
     * @return Response
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('Data_deteksi_model');
        $this->load->helper('api_helper');
        $this->load->library('form_validation');
    }

    /**
     * SHOW | GET method.
     *
     * @return Response
     */
    public function list_get()
    {
        check_authorization();

        $token_validation = $this->authorization_token->validateToken();
        $users_id = $token_validation["data"]->uid;

        $data = $this->Data_deteksi_model->list($users_id);

        $this->response([
            'status' => TRUE,
            'data' => $data
        ], REST_Controller::HTTP_OK);
    }

    public function show_get($id = 0)
    {
        check_authorization();

        if (empty($id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Data deteksi id param not found.',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $data = $this->Data_deteksi_model->show($id);

        $token_validation = $this->authorization_token->validateToken();
        $users_id = $token_validation["data"]->uid;

        if (empty($data)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Data deteksi not found.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        if ($data["users_id"]!==$users_id) {
            $this->response([
                'status' => FALSE,
                'message' => 'Data deteksi not found.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $this->response([
            'status' => TRUE,
            'data' => $data
        ], REST_Controller::HTTP_OK);
    }

	private function send_image_to_ml($api_ml_url, $imagePath)
	{
		// Create a cURL request to send the image to the ML URL
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $api_ml_url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, [
			'file' => new CurlFile($imagePath)
		]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
	
		// Handle the response from the ML service
		if ($response === false) {
			$error = curl_error($curl);
			curl_close($curl);
			throw new Exception("Failed to send image to ML service: $error");
		}
	
		curl_close($curl);
	
		// Parse and return the processed result
		$processedResult = json_decode($response, true);
		if (!$processedResult) {
			throw new Exception("Failed to decode the processed result from ML service.");
		}
	
		//return $processedResult['value'];
		return $processedResult;
	}

    /**
     * INSERT | POST method.
     *
     * @return Response
     */
    public function insert_post()
	 {
        check_authorization();

        $token_validation = $this->authorization_token->validateToken();
        $users_id = $token_validation["data"]->uid;

        //$input = $this->input->post();

        #$foto_mata_sebelum = $this->input->post('foto_mata_sebelum');
        $foto_mata_sebelum_name = $_FILES['foto_mata_sebelum']['name'];
        $foto_mata_sebelum_tmp_name = $_FILES['foto_mata_sebelum']['tmp_name'];

        
        try {
            $storage = new StorageClient([
                'keyFilePath' => 'cekmate-0f1c195007f5.json',
            ]);
        
            $bucketName = 'cekmate_data_deteksi';
            #$fileName = $foto_mata_sebelum_name;
            $fileName = date('Y-m-d_H-i-s',time()) . '_' . $users_id . '.' . pathinfo($foto_mata_sebelum_name, PATHINFO_EXTENSION);
            $bucket = $storage->bucket($bucketName);
            $object = $bucket->upload(
                fopen($foto_mata_sebelum_tmp_name, 'r'),
                [
                    'name' => $fileName,
                ]
            );
            //echo "File uploaded successfully. File path is: https://storage.googleapis.com/$bucketName/$fileName";

			//BUAT GET API ML
			 // Send the image to the ML URL for processing
			 $ml_url = 'https://apiml-dot-cekmate.et.r.appspot.com/predict';
			 $processedResult = $this->send_image_to_ml($ml_url, $foto_mata_sebelum_tmp_name);

            $foto_mata_path = $fileName;
			$foto_mata_url = "https://storage.googleapis.com/$bucketName/$fileName";
			$result = $processedResult['result'];
			$confidence = $processedResult['confidence'];
            //$this->Data_deteksi_model->insert($foto_mata_sebelum, $users_id);
			$this->Data_deteksi_model->insert($foto_mata_path, $foto_mata_url, $users_id, $result, $confidence);
    
            $this->response([
                'status' => TRUE,
                'message' => 'Data deteksi created successfully.'
            ], REST_Controller::HTTP_OK);

		} catch(Exception $e) {
            //echo $e->getMessage();
            $this->response([
                'status' => FALSE,
                'message' => $e->getMessage()
            ], REST_Controller::HTTP_OK);
        }

    }



    /**
     * UPDATE | PUT method.
     *
     * @return Response
     */
    /*public function update_post($id)
    {
        check_authorization();

        $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]|max_length[20]');
        $this->form_validation->set_rules('price', 'Price', 'required|decimal');

        if (!$this->form_validation->run()) {
            $this->response([
                'status' => FALSE,
                'message' => 'Validation Error.',
                'errors' => $this->form_validation->error_array()
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        $input = $this->input->post();
        $response = $this->Data_deteksi_model->update($input, $id);

        if (empty($response)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Not updated'
            ], REST_Controller::HTTP_OK);
        }

        $this->response([
            'status' => TRUE,
            'message' => 'Data deteksi updated successfully.'
        ], REST_Controller::HTTP_OK);
    }*/

    /**
     * DELETE method.
     *
     * @return Response
     */
    public function delete_delete($id)
    {
        check_authorization();

        $data = $this->Data_deteksi_model->show($id);

        $token_validation = $this->authorization_token->validateToken();
        $users_id = $token_validation["data"]->uid;

        if ($data["users_id"]!==$users_id) {
            $this->response([
                'status' => FALSE,
                'message' => 'Data deteksi not found.',
            ], REST_Controller::HTTP_NOT_FOUND);
        }else{
            $response = $this->Data_deteksi_model->delete($id);

            if (empty($response)) {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Not deleted'
                ], REST_Controller::HTTP_NOT_FOUND);
            }

            $this->response([
                'status' => TRUE,
                'message' => 'Data deteksi deleted successfully.'
            ], REST_Controller::HTTP_OK);
        }
    }
}

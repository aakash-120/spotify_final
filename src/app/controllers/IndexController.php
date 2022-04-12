<?php
//  require_once ""
require_once "../vendor/autoload.php";

use GuzzleHttp\Client;

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        $id = "829c80e105f54dd48f1af2758c73619b";
        $secret = "ae4dd45d49304d1692d89a45a5f75dca";
        $scope = "playlist-read-collaborative playlist-modify-public playlist-read-private playlist-modify-private";
        $redirect = "http://localhost:8080/index/spotify";
        $url = "https://accounts.spotify.com/authorize?response_type=code&client_id=$id&scope=$scope&redirect_uri=$redirect";
        $this->view->url = $url;
    }


    public function spotifyAction()
    {


        $code = $_GET['code'];
        $this->session->code = $code;
        $client_id = "829c80e105f54dd48f1af2758c73619b";
        $client_secret = "ae4dd45d49304d1692d89a45a5f75dca";
        $url = "https://accounts.spotify.com";
        $headers = [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($client_id . ":" . $client_secret)
        ];
        $client = new Client(
            [
                'base_uri' => $url,
                'headers' => $headers
            ]
        );
        $query = ["grant_type" => 'authorization_code', 'code' => $code, 'redirect_uri' => 'http://localhost:8080/index/spotify'];
        $response = $client->request('POST', '/api/token', ['form_params' => $query]);
        $response = $response->getBody();
        $response = json_decode($response, true);
        $access = $response['access_token'];
        $this->session->access = $access;
        $this->session->set("access", $access);

        $clients = new Client();
        $response = $clients->get('https://api.spotify.com/v1/me?access_token=' . $access . '');
        $body = $response->getBody();
        $body = json_decode($body, true);
        $id = $body['id'];
        $this->session->set("id", $id);
    }




    public function createplaylistAction()
    {
        
        $playlistName = $this->request->getPost('playlist');
        $description = $this->request->getPost('description');

        $id = ($this->session->get('id'));
        $url = "https://api.spotify.com/";
        $val = $this->request->getpost();
        $access = $this->session->get('access');
        $client = new Client(

            [
                'base_uri' => $url,
                'headers' => ['Authorization' => 'Bearer ' . $access]

            ]
        );
        $args = [
            'name' => $val['playlist'],
            'description' => $val['description'],
            'public' => 'false'
        ];
        $response = $client->request('POST', '/v1/users/' . $id . '/playlists', ['body' => json_encode($args)]);
        $response =  $response->getBody();
        $response = json_decode($response, true);
        $playlistid = ($response['id']);
        $this->session->set("playid", $playlistid);
        $this->response->redirect('index/search?name=playlist');
    }




    public function searchAction()
    {

        $access = $this->session->get('access');

        if (isset($_GET['name'])) {
          echo "isset name"; 
        //   $uri=$this->request->get('uri');
        //   echo $uri;
          $id = ($this->session->get('id'));
          $clientt = new Client();
          $response1 = $clientt->get('https://api.spotify.com/v1/users/' . $id . '/playlists?access_token=' . $access . '');
          $playlist = $response1->getBody();
          $playlist = json_decode($playlist, true);

        //   echo "<pre>";
        //   print_r($playlist);
        // die;
          $this->view->playlist = $playlist;
          
        }

        if (isset($_POST['search'])) {
            $search = $_POST['search'];
        }

        if (isset($_POST['album'])) {
            $selected_field = $_POST['album'];
            $authorization = "Authorization: Bearer " . $access;
            $spotifyURL = 'https://api.spotify.com/v1/search?q=' . urlencode($search) . '&type=album';

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $spotifyURL);
            curl_setopt($ch2,  CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x) Gecko/20041107 Firefox/x.x");
            curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
            $json2 = curl_exec($ch2);
            $json2 = json_decode($json2);
            curl_close($ch2);

            echo '<pre>' . print_r($json2, true) . '</pre>';
            $this->view->album = $json2;
        }

        if (isset($_POST['artist'])) {
            $selected_field = $_POST['artist'];
            $authorization = "Authorization: Bearer " . $access;
            $spotifyURL = 'https://api.spotify.com/v1/search?q=' . urlencode($search) . '&type=artist';
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $spotifyURL);
            curl_setopt($ch2,  CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x) Gecko/20041107 Firefox/x.x");
            curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
            $json2 = curl_exec($ch2);
            $json2 = json_decode($json2);
            curl_close($ch2);

            echo '<pre>' . print_r($json2, true) . '</pre>';
            $this->view->artist = $json2;
        }



        if (isset($_POST['track'])) {
            $selected_field = $_POST['track'];
            $authorization = "Authorization: Bearer " . $access;
            $spotifyURL = 'https://api.spotify.com/v1/search?q=' . urlencode($search) . '&type=track';

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $spotifyURL);
            curl_setopt($ch2,  CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x) Gecko/20041107 Firefox/x.x");
            curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
            $json2 = curl_exec($ch2);
            $json2 = json_decode($json2);
            curl_close($ch2);

            echo '<pre>' . print_r($json2, true) . '</pre>';
            $this->view->track = $json2;
        }
    }
}

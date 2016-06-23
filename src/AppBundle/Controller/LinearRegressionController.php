<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LinearRegressionController extends Controller
{
    /**
     * @Route("/predict", name="homepage")
     * @Method("POST")
     */
    public function predictAction(Request $request)
    {

        $response = new JsonResponse();
        $serializer = $this->container->get('serializer');
        $array = json_decode($request->getContent(), true);
        $data = json_encode(array(
             "Inputs" => array(
                "input1" => array("ColumnNames"=> array("hero_0",
                "hero_1",
                "hero_2",
                "hero_3",
                "hero_4",
                "hero_5",
                "hero_6",
                "hero_7",
                "hero_8",
                "hero_9",
                "radiant_win"),
                "Values"=>array($array)
                )
             ),
             "GlobalParameters"=>json_decode ("{}")
        ));

        $ch = curl_init("https://ussouthcentral.services.azureml.net/workspaces/5db9881656944377969d0cb99a136018/services/4ee66ed9721340c78717be17e07b338c/execute?api-version=2.0&details=true");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                                                                                     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
          
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data),
            'Authorization:Bearer BivWi4FeUhIht/s2e6PfOC4aTdkLrFJzGcyyBZrzCtN5DOqqDxWNFdRIoctQV4ODJChMi4liLEyN7aly9rMeew==',
            'Accept: application/json'                                                                     
        )); 

        $data = curl_exec($ch);
        $data = json_decode($data, true);

        if(isset($data["Results"]["output1"]["value"]["Values"])) {
            $data = $data["Results"]["output1"]["value"]["Values"];
            $data = $data[0][11];
        }

        $res = $serializer->serialize($data, 'json');
        $response->setStatusCode(200);
        $response->setContent($res);

        return $response; 
    }
}

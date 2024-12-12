<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class XXEController extends Controller
{
    public function index(Request $request)
    {
        $payload = $request->input("payload");
        $result = "";

        // Kiem tra payload co rong hay khong
        if (!empty($payload)) {
            // Vo hieu hoa cac thuc the ben ngoai
            libxml_disable_entity_loader(true);

            //  Chuyen xu ly loi ve noi bo thay vi hien thi ra ngoai
            libxml_use_internal_errors(true);
            
            // Tao doi tuong DOMDocument
            $dom = new \DOMDocument();

            try {
                $dom->loadXML($payload, LIBXML_NOENT | LIBXML_NONET);
                $result = $dom->getElementsByTagName("payload")->item(0)->nodeValue ?? "";
            } catch (\Exception $e) {
                $result = "Invalid XML format";
            }
        }
        return view("xxe")->with('result', $result);
    }
}
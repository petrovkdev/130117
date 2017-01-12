<?php

require_once("../conf/bootstrap.php");

//читаем данные и HTTP-запроса, строим из них XML по схеме
$hreq = new HTTP_Request2Xml("schemas/TestApp/DocumentListRequest.xsd");

$req=new TestApp_DocumentListRequest();
if (!$hreq->isEmpty()) {
	$hreq->validate();
	$req->fromXmlStr($hreq->getAsXML());
}

// формируем xml-ответ
$xw = new XMLWriter();
$xw->openMemory();
$xw->setIndent(TRUE);
$xw->startDocument("1.0", "UTF-8");
$xw->writePi("xml-stylesheet", "type=\"text/xsl\" href=\"stylesheets/TestApp/DocumentList.xsl\"");
$xw->startElementNS(NULL, "DocumentListResponse", "urn:ru:ilb:meta:TestApp:DocumentListResponse");
$req->toXmlWriter($xw);
// Если есть входные данные, проведем вычисления и выдадим ответ
if (!$hreq->isEmpty()) {
	$pdo=new PDO("mysql:host=127.0.0.1;dbname=testapp;charset=UTF-8","testapp","1qazxsw2",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	//prior to PHP 5.3.6, the charset option was ignored. If you're running an older version of PHP, you must do it like this:
	$pdo->exec("set names utf8");
	$query = "SELECT * FROM document  WHERE docDate BETWEEN :dateStart AND :dateEnd AND displayName LIKE :strHas";
	$sth=$pdo->prepare($query);
	$sth->execute(array(":dateStart"=>$req->dateStart,":dateEnd"=>$req->dateEnd, ":strHas"=> "%{$req->strHas}%"));
	while($row=$sth->fetch(PDO::FETCH_ASSOC)) {
		$doc = new TestApp_Document();
		$doc->fromArray($row);
		$doc->toXmlWriter($xw);		
		
	}
}

$xw->endElement();
$xw->endDocument();


$res  = $xw->flush();

$format = isset($_REQUEST["format"]) ? $_REQUEST["format"] : "html";

if (!in_array($format, array("html", "pdf"))) {
    throw new Exception("Unknown format $format", 450);
}


$headers = array("Content-type: application/xml");

if ($format == "pdf") {

    $xmldom = new DOMDocument();
    $xmldom->loadXML($res);
    $xsldom = new DomDocument();
    $xsldom->load("stylesheets/TestApp/DocumentFoList.xsl");
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsldom);
    $res = $proc->transformToXML($xmldom);	
	
     if ($format == "pdf") {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $url = "http://tomcat-bystrobank.rhcloud.com/fopservlet/fopservlet";
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $res);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code != 200) {
            throw new Exception($res . PHP_EOL . $url . " " . curl_error($ch), 450);
        }
		curl_close($ch);
		
        $attachmentName = "report.pdf";
        $headers = array(
            "Content-Type: application/pdf",
            "Content-Disposition: inline; filename*=UTF-8''" . $attachmentName
        );
		
    } 
	
}

foreach ($headers as $h) {
	//Вывод ответа клиенту
    header($h);
}

echo $res;

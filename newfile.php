

<?php
//$con = mysql_connect("127.0.0.1:8808","root","22GqUVCpvQsCjLc2");
//global $con;
$con = mysqli_connect
("127.0.0.1:3306", "root", "EQsa4Sxu4AX5cSqa", "paper");
if (!$con)
{
    die('数据库连接失败: ' . mysql_error());
}
else
{
    
    # Use the Curl extension to query Google and get back a page of results
   // $url = "http://www.studyez.com/kuaijizheng/lnst/all/201507/1920418.htm";
    $url = "http://www.studyez.com/kuaijizheng/lnst/all/201507/1920431.htm";
    
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $html = curl_exec($ch);
    curl_close($ch);
    
    # Create a DOM parser object
    $dom = new DOMDocument();
    
    # Parse the HTML from Google.
    # The @ before the method call suppresses any warnings that
    # loadHTML might throw because of invalid HTML in the page.
    @$dom->loadHTML($html);
    //insert paperInfo
    $paperID = uniqid();
    $paperType = "历年真题";
    $questionType = "";
    function insertIntoPaperInfo($title) {
       $sqlName = "INSERT INTO
        paperinfo (paperID,paperType,title) ";
    global $paperID;
    global $paperType;
    $sqlValue = "VALUES('$paperID','$paperType','$title')";
    $sql = $sqlName.$sqlValue;
    echo ($sql);
    global $con;
    mysqli_query($con, $sql);
    }
function  insertIntoQuestion($questionID,$paperID,$paperType,$questionType,$title) {
    $sqlName = "INSERT INTO
        question (questionID,paperID,paperType,questionType,title
 )";
    $sqlValue = "VALUES('$questionID','$paperID',
    '$paperType','$questionType','$title')";
    $sql = $sqlName.$sqlValue;
    echo ($sql);
    global $con;
    mysqli_query($con, $sql);
}
   

function  insertIntoQuestionOption($questionID,$optionContent) {
    $sqlName = "INSERT INTO
        questionoption (questionID,optionContent)";
    $sqlValue = "VALUES('$questionID','$optionContent')";
    $sql = $sqlName.$sqlValue;
    echo ($sql);
    global $con;
    mysqli_query($con, $sql);
}    
    
    function processElement(DOMElement $element){
        $isOption = FALSE;
        $attri =  $element->getAttribute('style');
       
    foreach($element->childNodes as $child){
        if($child instanceOf DOMText){
            if (!$isOption) { 
                //insert paperinfo table              
                if ($attri =="text-align: center;") {
                     echo "paperTitle: ".$child->nodeValue;
                     
                     insertIntoPaperInfo($child->nodeValue);
                     } else {
                  #insert into question table: title
                echo $child->nodeValue,PHP_EOL;
                echo "not option,insert to question table";
                global $paperID;
                global $paperType;
                 
                 $nodeValue = $child->nodeValue;
                 global $questionType;
               $isTypeDescription = false;
                 if (strpos($nodeValue,'单项选择题') !== false) {
                     $isTypeDescription = true;
                     $questionType = "单选";
                 }
                 if (strpos($nodeValue,'多项选择题') !== false) {
                    $isTypeDescription = true;
                     $questionType = "多选";
                 }
                 if (strpos($nodeValue,'判断题') !== false) {
                    $isTypeDescription = true;
                     $questionType = "判断";
                 }
                 if (strpos($nodeValue,'计算分析题') !== false) {
                    $isTypeDescription = true;
                     $questionType = "计算分析";
                 }
                 if (!$isTypeDescription) {
                     global $questionID;
                     $questionID = uniqid();
   insertIntoQuestion($questionID, $paperID, $paperType, $questionType, $child->nodeValue);
                 }
                    
                     }
            } else {
                echo $child->nodeValue,PHP_EOL;
                echo "is option,insert to option table";
                insertIntoQuestionOption($questionID, $child->nodeValue);
            }
            
            
        }elseif($child instanceOf DOMElement){
            switch($child->nodeName){
            case 'br':
                echo 'BREAK: ',PHP_EOL;
                $isOption = TRUE;
                break;
           /* case 'p':
                echo 'PARAGRAPH: ',PHP_EOL;
                processElement($child);
                echo 'END OF PARAGRAPH;',PHP_EOL;
                break;*/
            // etc.
            // other cases:
            default:
                processElement($child);
            }
        }
    }

    }
    
     $domElements = $dom->getElementsByTagName('p');
     $index = $domElements->length;
     $index = $index - 2;
     echo "index ".$index;
     //print_r($domElements[2]);
        for ($i = 1; $i < $index; $i++) {          
            $domElement = $domElements->item($i);
            processElement($domElement);
        }
    //foreach ($dom->getElementsByTagName('p') as ){ 
       // print_r($domElement);
      
   // processElement($domElement);
    /*$childeNodes = $domElement->childNodes;
    echo $childeNodes->length;
    foreach ($childeNodes as $child){
        print_r($child);
    }*/
    
    //}
}  
                  
    $con->close();
  
   
    ?>

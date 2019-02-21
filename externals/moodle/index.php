<?php
$serviceKey="lkhjglkh78435vlsd";
$key=$_REQUEST['key'];
    if ($key != md5($serviceKey.date('Ymd'))) {
    echo json_encode ('no valid key');
     die;
     }

require_once ('../config.php');
include ($CFG->dirroot . '/lib/adodb/adodb.inc.php');

    $db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
    $db->debug = false;
    $db->Connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
 
$elenco=array();
 $rs = $db->Execute("SELECT username,idnumber,firstname,lastname,email,zenpwd FROM mdl_user ORDER BY lastname ASC, firstname ASC");
while (!$rs->EOF) {
    $id=strtolower($rs->fields[0]);
    if (!preg_match("/^[a-z]{3}[0-9]{5}$/",$id)) {
         $rs->MoveNext();
         continue;
    } 
        if (strlen($rs->fields[1])==0) {
     $group=substr($rs->fields[0],0,strlen($rs->fields[0])-5);
    } else {
    $group=substr($rs->fields[1],0,strlen($rs->fields[1])-5);
    }
    /*
     * Password encrypt: http://stackoverflow.com/questions/1289061/best-way-to-use-php-to-encrypt-and-decrypt
     */
    
    $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $rs->fields[5], MCRYPT_MODE_CBC, md5(md5($key))));
         $elenco[]=
                 array(
                     'id'=>  $id,
                     'firstname' => trim(ucwords(strtolower($rs->fields[2]))),
                     'lastname' => trim(ucwords(strtolower($rs->fields[3]))),
                     'email' => trim(strtolower($rs->fields[4])),
                     'group'=> strtoupper(trim($group)),
                     'password'=> $encrypted
                 );
         $rs->MoveNext();
}

echo json_encode($elenco);
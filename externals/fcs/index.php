<?php
$serviceKey = "lkhjglkh78435vlsd";
$key = $_REQUEST['key'];
if ($key != md5($serviceKey . date('Ymd'))) {
    echo json_encode('no valid key');
    die;
}

include ( './lib/adodb5/adodb.inc.php');
set_time_limit(0);

    $db = ADONewConnection('mysql'); # eg 'mysql' or 'postgres'
    $db->debug = false;

      $ignore = array(':', '/', '\\', ',', '=', '+', '<', '>', ';', '"', '#', "'", '(', ')', "'", "\x00", '?', '.', '-', '!',')','(',';','\'','"','/','#');
$sql =  $db->Connect("localhost", "issm_it_form", "p4ss", "issm_it_form");
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$sql = "SELECT col_2, col_3, col_4, col_10, col_1 FROM ft_form_3 ORDER BY col_3 ASC, col_4 ASC";
$recordSet = $db->Execute($sql);
$elenco = array();
while (!$recordSet->EOF) {
    $elenco[] =
            array(
                'id' => $recordSet->fields['col_10'],
                'firstname' => trim(ucwords(strtolower($recordSet->fields['col_4']))),
                'lastname' => trim(ucwords(strtolower($recordSet->fields['col_3']))),
                'email' => trim(strtolower($recordSet->fields['col_1'])),
                'group' => strtolower(trim(str_replace ($ignore,'',preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $recordSet->fields['col_2']))))
    );
    $recordSet->MoveNext();
}
$recordSet->Close();
$db->Close();
echo json_encode($elenco);

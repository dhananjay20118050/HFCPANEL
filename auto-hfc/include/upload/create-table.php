<?php 

//require_once '../import.php';

$table = "hfccustdata";
$cols = ['TRNREFNO', 'BRCODE', 'SBCODE', 'BNKSRL', 'APPLNO', 'NAME', 'ADD1', 'ADD2', 'ADD3', 'CITY', 'STATE', 'PIN', 'TYPE', 'TENURE', 'CATE', 'FOLIO', 'EMPCODE', 'STATUS', 'AMOUNT', ' PAYMODE', 'INSTNO', 'INSTDT', 'PANGIR1', 'DOB', 'NGNAME', 'BANKAC', 'BANKNM', 'BCITY', 'MICR', 'GNAME', 'GPAN', 'ACTYPE', 'RTGSCOD', 'NNAME', 'NADD1', 'NADD2', 'NADD3', 'NCITY', 'NPIN', 'ENCL', 'TELNO', 'JH1NAME', 'JH2NAME', 'JH1PAN', 'JH2PAN', 'JH1RELATION', 'JH2RELATION', 'HLDINGPATT', 'SUBTYPE', 'EMAILID', 'MOBILENO', 'IFSC'];

$sql_col = "";
foreach ($cols as $col) {
    $sql_col .= '`'.$col.'` VARCHAR(255) ,';
}

$sql = "CREATE TABLE IF NOT EXISTS `$table` (
  `id` INT(255) , $sql_col
  `filename` VARCHAR(100) ,
  `cifid_1` VARCHAR(100) ,
  `cifid_2` VARCHAR(100) ,
  `cifid_3` VARCHAR(100) ,
  `is_existing_cust_1` INT(1) NOT NULL ,
  `is_existing_cust_2` INT(1) NOT NULL ,
  `is_existing_cust_3` INT(1) NOT NULL ,
  `edit_cifid_1` INT(1) NOT NULL ,
  `edit_cifid_2` INT(1) NOT NULL ,
  `edit_cifid_3` INT(1) NOT NULL ,
  `AccountNo` VARCHAR(100) NOT NULL ,
  PRIMARY KEY (`TRNREFNO`)  )
ENGINE = InnoDB; ALTER TABLE `hfccustdata` CHANGE `id` `id` INT(255) NOT NULL AUTO_INCREMENT UNIQUE;";

echo $sql;


?>
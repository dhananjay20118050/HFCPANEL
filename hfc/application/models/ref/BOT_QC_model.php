<?php

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverSelectInterface;
use Facebook\WebDriver\WebDriverWindow;

class BOT_QC_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('BOT_Model', 'bot');
        $this->load->model('BOT_CAM_Model', 'bot_cam');
    }

    public function startQC($data){
        
        $app = $data['app'];
        $userId = $data['userId'];

        /* Check for pending applications for this user */
        $sql = "SELECT a.txtAppFormNo as txtAppFormNo, c.apsNo as apsNo, d.fullName as auto_user, e.fullName as bde_user FROM bot_qc_tracking a LEFT JOIN bot_aps_tracking b ON a.txtAppFormNo = b.txtAppFormNo LEFT JOIN pldataentry c ON c.applicationNo = a.txtAppFormNo LEFT JOIN coreusers d ON d.userId = b.start_userId LEFT JOIN coreusers e ON e.userId = c.userId WHERE a.userId = '$userId' and b.qc_status = 'P'";

        $rows = $this->db->query($sql)->result_array();
        if(count($rows) != 0){
            $response = array('status' => 'P', 'app' => $rows[0]['txtAppFormNo'], 'aps' => $rows[0]['apsNo'], 'auto_user' => $rows[0]['auto_user'], 'bde_user' => $rows[0]['bde_user']);
            $sql = "UPDATE bot_qc_tracking set `start_time` = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
            $qparent = $this->db->query($sql);
            return $response;
        }
        /* Ends */

        $sql = "SELECT * FROM bot_qc_tracking WHERE txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();

        $localIP = getHostByName(getHostName());

        if(count($rows) == 0){
            $sql = "INSERT INTO bot_qc_tracking (`txtAppFormNo`, `ip_address`, `userId`, `start_time`) VALUES('$app','$localIP','$userId', '".date("Y-m-d H:i:s")."')";
            $qparent = $this->db->query($sql);
            $sql = "UPDATE bot_aps_tracking set `qc_status` = 'P' WHERE txtAppFormNo = '$app'";
            $qparent = $this->db->query($sql);
            $response['status'] = 'Y';
            return $response;
        }else{
            $response['status'] = 'A';
        }
        return $response;
    }

    public function saveQCRemarks($data){
        $sql = "UPDATE bot_qc_tracking set `end_time` = '".date("Y-m-d H:i:s")."', `remarks` = '".$data['remarks']."', `aps_status` = '".$data['aps_status']."' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        $sql = "UPDATE bot_aps_tracking set `qc_status` = 'Y' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        return 'Y';
    }

    public function reallocateQC($data){
        $sql = "UPDATE bot_aps_tracking set `qc_status` = 'N' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        $sql = "DELETE FROM bot_qc_tracking WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        return 'Y';
    }

    public function startCAM($data){
        
        $app = $data['app'];
        $userId = $data['userId'];

        /* Check for pending applications for this user */
        $sql = "SELECT a.txtAppFormNo as txtAppFormNo, c.apsNo as apsNo, d.fullName as auto_user, e.fullName as bde_user FROM bot_cam_tracking a LEFT JOIN bot_aps_tracking b ON a.txtAppFormNo = b.txtAppFormNo LEFT JOIN pldataentry c ON c.applicationNo = a.txtAppFormNo LEFT JOIN coreusers d ON d.userId = b.start_userId LEFT JOIN coreusers e ON e.userId = c.userId WHERE a.userId = '$userId' and b.cam_status = 'P'";

        $rows = $this->db->query($sql)->result_array();
        if(count($rows) != 0){
            $response = array('status' => 'P', 'app' => $rows[0]['txtAppFormNo'], 'aps' => $rows[0]['apsNo'], 'auto_user' => $rows[0]['auto_user'], 'bde_user' => $rows[0]['bde_user']);
            $sql = "UPDATE bot_cam_tracking set `start_time` = '".date("Y-m-d H:i:s")."' WHERE txtAppFormNo = '".$rows[0]['txtAppFormNo']."'";
            $qparent = $this->db->query($sql);
            return $response;
        }
        /* Ends */

        $sql = "SELECT * FROM bot_cam_tracking WHERE txtAppFormNo = '$app'";
        $rows = $this->db->query($sql)->result_array();

        $localIP = getHostByName(getHostName());

        if(count($rows) == 0){
            $sql = "INSERT INTO bot_cam_tracking (`txtAppFormNo`, `ip_address`, `userId`, `start_time`) VALUES('$app','$localIP','$userId', '".date("Y-m-d H:i:s")."')";
            $qparent = $this->db->query($sql);
            $sql = "UPDATE bot_aps_tracking set `cam_status` = 'P' WHERE txtAppFormNo = '$app'";
            $qparent = $this->db->query($sql);
            $response['status'] = 'Y';
            return $response;
        }else{
            $response['status'] = 'A';
        }
        return $response;
    }

    public function saveCAMRemarks($data){
        $sql = "UPDATE bot_cam_tracking set `end_time` = '".date("Y-m-d H:i:s")."', `remarks` = '".$data['remarks']."', `aps_status` = '".$data['aps_status']."' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        $sql = "UPDATE bot_aps_tracking set `cam_status` = 'Y' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        return 'Y';
    }

    public function reallocateCAM($data){
        $sql = "UPDATE bot_aps_tracking set `cam_status` = 'N' WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        $sql = "DELETE FROM bot_cam_tracking WHERE txtAppFormNo = '".$data['app']."'";
        $qparent = $this->db->query($sql);
        return 'Y';
    }


}
?>
-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 31, 2019 at 12:55 PM
-- Server version: 5.7.23
-- PHP Version: 7.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hfc`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_GetAlternateBankingDetail`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetAlternateBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
   select p.applicationno,a.apsno,p.firstname,p.secondary_bank_name,p.secondry_account_number,p.secondry_account_type
 from pldataentry p inner join pl_apsformdata a on p.applicationno=a.txtappformno WHERE p.applicationno in 
(select a.txtappformno from pl_apsformdata where a.apsno=apsNoo);
END$$

DROP PROCEDURE IF EXISTS `sp_GetApplicantandOfficeTVR`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetApplicantandOfficeTVR` (IN `apsNoo` VARCHAR(50))  BEGIN
   select firstname,tel from pldataentry WHERE apsNo=apsNoo ;   
END$$

DROP PROCEDURE IF EXISTS `sp_GetApp_Details`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sp_GetApp_Details` (IN `apsNoo` VARCHAR(50))  BEGIN
select b.txtAppFormNo,a.apsno,b.app_score,b.cpcs,
if(ifnull(b.cpcs,0) >0, 'Matched','Not Matched')as cpcs_status ,b.de_dupe,
if(ifnull(b.de_dupe,0)>0,'Matched','Not Matched')as de_dupe_status,b.app_id,b.cibil_Vintage, 
(case when b.cibil_Vintage<12 then 'ultrathin' when b.cibil_Vintage<24 then 'thin' else 'thick' end)as cibil_vintage_Status,b.pq_offer,
(case when ifnull(pq_offer,'N')='Y' then 'Yes' else 'No' end)as PQ_Status
from pl_apscamdata b
inner join pl_apsformdata a on b.txtAppFormNo=a.txtappformno 
where b.txtAppFormNo=(select txtappformno from pl_apsformdata where apsno=apsNoo) and b.isCamDataSaved=1;
END$$

DROP PROCEDURE IF EXISTS `sp_GetBankingDetail`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
select firstname,primary_bank_name,primary_account_number,occupational_detail,name_prefix from pldataentry WHERE apsNo=apsNoo ; 
  
END$$

DROP PROCEDURE IF EXISTS `sp_GetBankingDetail_ForAVG`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sp_GetBankingDetail_ForAVG` (IN `apsNoo` VARCHAR(50))  BEGIN

select * from 
(select b.txtAppFormNo,a.apsno,cast(b.month as UNSIGNED)month,b.year,
 b.bal_5,b.bal_15,b.bal_25,b.month_avg_bal,b.times_of_emi from pl_apsbankdata b
inner join pl_apsformdata a on b.txtAppFormNo=a.txtappformno 
where b.txtAppFormNo=(select txtappformno from pl_apsformdata where apsno=apsNoo)
order by cast(b.month as UNSIGNED) desc
limit 3)Query
order by month;

END$$

DROP PROCEDURE IF EXISTS `sp_GetBDTDeatils`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetBDTDeatils` (IN `apsNoo` VARCHAR(50))  BEGIN 
   
select P.Credit_facility_amount,P.tenure_month,SUBSTRING_INDEX(P.customer_code,'-',1) as customer_code,SUBSTRING_INDEX(P.surrogate_code,'-',1) as surrogate_code,P.promo_code,P.education,P.company_employer_name,
P.employer_desgnator,d.emp_desc,P.exp_current_job,P.exp_total_job,P.previous_employer_name,P.date_of_birth,P.marital_status,P.residence_is,P.no_of_year,P.primary_bank_name,
P.primary_account_number,ed.qualification_desc,res.residenceis_desc ,c.CategoryValue   from pldataentry P
inner join pleducationalqualification_master ed on P.education=ed.qualification_code
inner join plresidenceis_master res on P.residence_is=res.residenceis_code
Left join plemployerdesignator_master d on P.employer_desgnator=d.emp_code
Left join plcategoryvalue c on P.spouse_occupation=c.Categorycode
WHERE P.apsNo=apsNoo ; 
END$$

DROP PROCEDURE IF EXISTS `sp_GetCombinedBankingDetail`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetCombinedBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
   select firstname,secondary_bank_name,secondry_account_number,customer_category,primary_branch from pldataentry WHERE apsNo=apsNoo ;    
END$$

DROP PROCEDURE IF EXISTS `sp_GetDoctor_SelfEmployed`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetDoctor_SelfEmployed` (IN `apsNoo` VARCHAR(50))  BEGIN     
select credit_facility_amount from pldataentry WHERE apsNo=apsNoo ; 
END$$

DROP PROCEDURE IF EXISTS `sp_GetEsclationDetail`$$
CREATE DEFINER=`root`@`%` PROCEDURE `sp_GetEsclationDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
select P.apsNo,P.credit_facility_amount,P.tenure_month,P.education,P.company_employer_name,P.exp_current_job,P.previous_employer_name,
P.spouse_occupation,P.residence_is,P.other,P.employer_desgnator,P.exp_total_job,P.residence_type,P.residence_is,P.no_of_year,P.primary_bank_name,
P.primary_account_number,SUBSTRING_INDEX(P.customer_code,'-',1) as customer_code,SUBSTRING_INDEX(P.surrogate_code,'-',1) as surrogate_code,a.txtAddressOne_4,d.emp_desc,ed.qualification_desc,res.residenceis_desc,
P.promo_code,
(case when trim(left(Ifnull(P.promo_code,'NA'),2))='NA' then 'Fresh' when P.promo_code='' then 'Fresh' else P.promo_code end)as Promo_Status
from pldataentry P inner join pl_apsformdata a on P.applicationno=a.txtappformno 
Left join plemployerdesignator_master d on P.employer_desgnator=d.emp_code 
inner join pleducationalqualification_master ed on P.education=ed.qualification_code
inner join plresidenceis_master res on P.residence_is=res.residenceis_code
WHERE  P.apsNo = (select apsno from pl_apsformdata where apsno=apsNoo) ;  
        
END$$

DROP PROCEDURE IF EXISTS `sp_GetReferenceTVR`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetReferenceTVR` (IN `apsNoo` VARCHAR(50))  BEGIN    
 select reference1_name,reference1_phone,reference1_add,kyc_verification_emp_designation,reference1_relationship,reference2_name,
reference2_phone,reference2_add,reference2_relationship,concat(std_code,'-',tel) As tel,residentmobile,concat(companystd,'-',company_tel) As company_tel,exp_current_job,no_of_year,exp_total_job 
 from pldataentry  WHERE apsNo=apsNoo ; 
END$$

DROP PROCEDURE IF EXISTS `sp_GetSalariedDetail`$$
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetSalariedDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
select CONCAT(p.firstname,' ',p.middle_name,'',p.surname) As Name, p.credit_facility_amount,p.tenure_month from pldataentry p inner join pl_apsformdata a on p.applicationno=a.txtappformno 
WHERE a.apsNo in (select a.apsno from pl_apsformdata where a.apsno=apsNoo);
update pldataentry  set apsno=(select apsno from pl_apsformdata where apsno=apsNoo) 
where applicationno=(select txtappformno from pl_apsformdata where apsno=apsNoo);
       
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bot_aps_tracking`
--

DROP TABLE IF EXISTS `bot_aps_tracking`;
CREATE TABLE IF NOT EXISTS `bot_aps_tracking` (
  `tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `TRNREFNO` varchar(255) DEFAULT NULL,
  `start_time` varchar(45) DEFAULT NULL,
  `end_time` varchar(45) DEFAULT NULL,
  `status` varchar(2) DEFAULT NULL,
  `last_process_entry` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `userid` varchar(11) DEFAULT NULL,
  `upload_datetime` varchar(45) DEFAULT NULL,
  `upload_user` int(11) DEFAULT NULL,
  `is_pan_checked` int(1) NOT NULL DEFAULT '0',
  `is_logged_in` int(1) NOT NULL DEFAULT '0',
  `is_processed` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`tracking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_aps_tracking`
--

INSERT INTO `bot_aps_tracking` (`tracking_id`, `TRNREFNO`, `start_time`, `end_time`, `status`, `last_process_entry`, `ip_address`, `userid`, `upload_datetime`, `upload_user`, `is_pan_checked`, `is_logged_in`, `is_processed`) VALUES
(1, '1234565', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(2, '1234566', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(3, '1234567', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(4, '1234568', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(5, '1234569', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(6, '1234570', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(7, '1234571', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(8, '1234572', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:27', 1, 0, 0, NULL),
(9, '1234573', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:28', 1, 0, 0, NULL),
(10, '1234574', NULL, NULL, 'N', 0, '10.1.42.209', NULL, '2019-10-31 07:17:28', 1, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bot_aps_trackingold`
--

DROP TABLE IF EXISTS `bot_aps_trackingold`;
CREATE TABLE IF NOT EXISTS `bot_aps_trackingold` (
  `tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `txtAppFormNo` varchar(255) DEFAULT NULL,
  `apsNo` varchar(255) DEFAULT NULL,
  `start_time` varchar(45) DEFAULT NULL,
  `end_time` varchar(45) DEFAULT NULL,
  `start_userId` int(11) DEFAULT NULL,
  `resume_userId` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `last_process_entry` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `isCibilDownloaded` int(1) DEFAULT '0',
  `isSourcing` int(1) DEFAULT '0',
  `IsCamGen` int(11) DEFAULT NULL,
  `qc_status` varchar(1) DEFAULT 'N',
  `cam_status` varchar(1) DEFAULT 'N',
  `is_auto_qc_done` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tracking_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_aps_trackingold`
--

INSERT INTO `bot_aps_trackingold` (`tracking_id`, `txtAppFormNo`, `apsNo`, `start_time`, `end_time`, `start_userId`, `resume_userId`, `status`, `last_process_entry`, `ip_address`, `isCibilDownloaded`, `isSourcing`, `IsCamGen`, `qc_status`, `cam_status`, `is_auto_qc_done`) VALUES
(1, '1234567', NULL, '2019-06-12 16:57:38', '2019-08-06 15:47:25', 0, NULL, 'E', 0, '10.1.42.209', 0, 0, NULL, 'N', 'N', 0),
(2, '51008456385', NULL, '2019-06-12 16:57:38', NULL, 0, NULL, 'N', 1, '10.1.42.209', 0, 0, NULL, 'N', 'N', 0),
(3, '51008456347', NULL, '2019-06-12 16:57:38', NULL, 0, NULL, 'N', 1, '10.1.42.209', 0, 0, NULL, 'N', 'N', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bot_control_mst`
--

DROP TABLE IF EXISTS `bot_control_mst`;
CREATE TABLE IF NOT EXISTS `bot_control_mst` (
  `control_id` int(5) NOT NULL,
  `desc` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`control_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_control_mst`
--

INSERT INTO `bot_control_mst` (`control_id`, `desc`) VALUES
(1, 'Input Box'),
(2, 'Button'),
(3, 'Select'),
(4, 'Link'),
(5, 'Wait for URL'),
(6, 'Wait for Alert'),
(7, 'Wait for Implicit Time'),
(8, 'Wait for Element Visibility'),
(9, 'Switch Frame'),
(10, 'Switch Default Frame'),
(11, 'Switch Window'),
(12, 'Radio'),
(13, 'Function');

-- --------------------------------------------------------

--
-- Table structure for table `bot_error_logs`
--

DROP TABLE IF EXISTS `bot_error_logs`;
CREATE TABLE IF NOT EXISTS `bot_error_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `exception_class` varchar(255) DEFAULT NULL,
  `TRNREFNO` varchar(100) DEFAULT NULL,
  `exception_dtl` varchar(5000) DEFAULT NULL,
  `datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `error_section` varchar(100) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `screenshot_path` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_error_logs`
--

INSERT INTO `bot_error_logs` (`log_id`, `exception_class`, `TRNREFNO`, `exception_dtl`, `datetime`, `error_section`, `userId`, `screenshot_path`) VALUES
(1, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == #usertxt\n', '2019-10-24 05:53:52', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_23_52.png'),
(2, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:54:16', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_24_16.png'),
(3, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:54:39', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_24_39.png'),
(4, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:55:02', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_25_02.png'),
(5, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:55:26', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_25_26.png'),
(6, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:55:49', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_25_49.png'),
(7, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-10-24 05:56:14', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_26_14.png'),
(8, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-10-24 06:00:26', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_30_26.png'),
(9, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:01:41', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_31_41.png'),
(10, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-10-24 06:04:10', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_34_10.png'),
(11, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:06:16', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_36_16.png'),
(12, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:07:54', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_37_54.png'),
(13, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:08:46', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_38_46.png'),
(14, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:23:05', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_11_53_05.png'),
(15, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-10-24 06:33:52', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_03_52.png'),
(16, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1046', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-10-24 06:38:30', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_08_30.png'),
(17, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:40:38', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_10_38.png'),
(18, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', '', '2019-10-24 06:42:39', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_12_39.png'),
(19, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1046', 'Currently focused window has been closed.\n', '2019-10-24 07:04:18', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_34_18.png'),
(20, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == #moreInfoContainer\n', '2019-10-24 07:07:17', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_37_17.png'),
(21, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1046', 'Currently focused window has been closed.\n', '2019-10-24 07:12:35', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_42_35.png'),
(22, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'tempFrm\']\n', '2019-10-24 07:16:45', 'Frame Not Found', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_46_45.png'),
(23, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'AccountBO.PhoneEmail.PhoneEmailType\']\n', '2019-10-24 07:22:48', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_52_48.png'),
(24, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1046', 'Unable to find element with css selector == *[name=\'EntityDocumentBO.DocTypeCode\']\n', '2019-10-24 07:28:48', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1046_2019_10_24_12_58_48.png'),
(25, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1015', 'Unable to find element with css selector == #moreInfoContainer\n', '2019-10-24 07:33:55', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1015_2019_10_24_13_03_55.png'),
(26, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:18', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_18.png'),
(27, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:23', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_23.png'),
(28, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_27.png'),
(29, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:32', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_32.png'),
(30, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:35', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_35.png'),
(31, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1017', '', '2019-10-24 07:37:38', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1017_2019_10_24_13_07_38.png');

-- --------------------------------------------------------

--
-- Table structure for table `bot_ip_logins`
--

DROP TABLE IF EXISTS `bot_ip_logins`;
CREATE TABLE IF NOT EXISTS `bot_ip_logins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_ip_logins`
--

INSERT INTO `bot_ip_logins` (`id`, `ip_address`, `username`, `password`) VALUES
(1, '10.27.175.127', 'he000049', 'vikas@123'),
(2, '10.27.175.127', 'HE000827', 'User@123');

-- --------------------------------------------------------

--
-- Table structure for table `bot_process_dtl`
--

DROP TABLE IF EXISTS `bot_process_dtl`;
CREATE TABLE IF NOT EXISTS `bot_process_dtl` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `process_dtl_id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `process_dtl_desc` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_process_dtl`
--

INSERT INTO `bot_process_dtl` (`id`, `process_dtl_id`, `process_id`, `process_dtl_desc`) VALUES
(1, 1, 1, 'Login Process'),
(2, 2, 1, 'Data Entry Process'),
(3, 3, 1, 'Applicant Personal'),
(4, 4, 1, 'Applicant Personal Other Details'),
(5, 5, 1, 'Address'),
(6, 6, 1, 'Work Detail'),
(7, 7, 1, 'Income Expense'),
(8, 8, 1, 'Bank'),
(9, 9, 1, 'References'),
(10, 10, 1, 'Asset & Loan Details'),
(11, 11, 1, 'Other Charges'),
(12, 12, 1, 'Change Stage'),
(13, 13, 1, 'Pre-sanction'),
(15, 14, 1, 'Download Cibil'),
(16, 15, 1, 'Logout');

-- --------------------------------------------------------

--
-- Table structure for table `bot_process_mst`
--

DROP TABLE IF EXISTS `bot_process_mst`;
CREATE TABLE IF NOT EXISTS `bot_process_mst` (
  `process_id` int(5) NOT NULL,
  `process_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_process_mst`
--

INSERT INTO `bot_process_mst` (`process_id`, `process_name`) VALUES
(1, 'HFC login'),
(2, 'HFC CRM');

-- --------------------------------------------------------

--
-- Table structure for table `bot_rejected_apps`
--

DROP TABLE IF EXISTS `bot_rejected_apps`;
CREATE TABLE IF NOT EXISTS `bot_rejected_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txtAppFormNo` varchar(20) NOT NULL,
  `remarks` varchar(500) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bot_sequence_dtl`
--

DROP TABLE IF EXISTS `bot_sequence_dtl`;
CREATE TABLE IF NOT EXISTS `bot_sequence_dtl` (
  `seq_id` int(11) NOT NULL,
  `process_dtl_id` int(11) DEFAULT NULL,
  `seq_no` int(11) DEFAULT NULL,
  `selector_desc` varchar(45) DEFAULT NULL,
  `selector_type` varchar(45) DEFAULT NULL,
  `selector_id` varchar(500) DEFAULT NULL,
  `selector_value` varchar(1000) DEFAULT NULL,
  `default_value` varchar(1000) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `control_id` int(11) DEFAULT NULL,
  `isDel` int(1) DEFAULT NULL,
  `isSleep` int(11) DEFAULT '0',
  `parent_model` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`seq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_sequence_dtl`
--

INSERT INTO `bot_sequence_dtl` (`seq_id`, `process_dtl_id`, `seq_no`, `selector_desc`, `selector_type`, `selector_id`, `selector_value`, `default_value`, `model`, `control_id`, `isDel`, `isSleep`, `parent_model`) VALUES
(1, 1, 1, 'Website Login Link', 'href', '', 'https://ijprsunt7-04-ld18.icicibankltd.com:8212/SSO/ui/SSOLogin.jsp', '', '', 4, 0, 0, ''),
(2, 1, 2, 'Site Secure Link', 'id', 'moreInfoContainer', 'More information', '', '', 2, 0, 0, ''),
(3, 1, 3, 'Website Secure Link', 'id', 'overridelink', '', '', '', 2, 0, 0, ''),
(4, 1, 4, 'User ID Wait', 'id', 'usertxt', '', '', '', 8, 0, 0, ''),
(5, 1, 5, 'User ID', 'id', 'usertxt', 'HE000049', '', '', 1, 0, 0, ''),
(6, 1, 6, 'Password', 'id', 'passtxt', 'vikas@123', '', '', 1, 0, 0, ''),
(7, 1, 7, 'Login Button', 'id', 'Submit', '', '', '', 2, 0, 0, ''),
(8, 1, 8, 'Solution Select', 'name', '', 'appSelect', 'FINCORE', '', 3, 0, 0, ''),
(9, 1, 9, 'Accept Alert', '', '', '', '', '', 6, 0, 0, ''),
(10, 1, 10, 'Menu Shortcut', 'id', 'menuname', 'HOAACTD', '', '', 1, 0, 0, ''),
(11, 1, 11, 'CIF ID', 'id', 'sLnk2', 'javascript:shoCifId(objForm.CifId,\'ctrl\',\'F\',objForm.custName)', '', '', 13, 0, 0, ''),
(15, 2, 15, 'Solution Select', 'name', '', 'appSelect', 'CRM', '', 3, 0, 0, ''),
(16, 2, 16, 'Accept Alert', '', '', '', '', '', 6, 0, 0, ''),
(17, 2, 17, 'CIF Retail', 'id', 'spanFor1', '', '', '', 2, 0, 0, ''),
(18, 2, 18, 'New Entity', 'id', 'spanFor2', '', '', '', 2, 0, 0, ''),
(19, 2, 19, 'Customer', 'id', 'subviewspanFor20', '', '', '', 2, 0, 0, ''),
(20, 2, 20, 'Basic Info', 'name', '', 'AccountModBO.Gender', 'Male', '', 3, 0, 0, ''),
(21, 2, 21, 'Title', 'name', '', 'AccountModBO.Salution_code', '', '', 1, 0, 0, ''),
(22, 2, 22, 'First Name', 'name', '', 'AccountModBO.Cust_First_Name', '', '', 1, 0, 0, ''),
(23, 2, 23, 'Middle Name', 'name', '', 'AccountModBO.Cust_Middle_Name', '', '', 1, 0, 0, ''),
(24, 2, 24, 'Last Name', 'name', '', 'AccountModBO.Cust_Last_Name', '', '', 1, 0, 0, ''),
(25, 2, 25, 'Short Name', 'name', '', 'AccountModBO.Short_Name', '', '', 1, 0, 0, ''),
(26, 2, 26, 'Date Of Birth', 'name', '', '3_AccountB0.Cust_DOB', '', '', 1, 0, 0, ''),
(27, 1, 27, 'Minor Indicator', 'name', '', 'AccountModBO.CustomerMinor', 'N', '', 3, 0, 0, ''),
(28, 1, 28, 'Non-Resident Indicator', 'name', '', 'AccountModBO.CustomerNREFLG', 'N', '', 3, 0, 0, ''),
(29, 1, 29, 'Non-Resident Indicator', 'name', '', 'AccountModBO.StaffFlag', 'N', '', 3, 0, 0, ''),
(30, 1, 30, 'Availed Trade Services', 'name', '', 'AccountModBO.TFPartyFlag', 'N', '', 3, 0, 0, ''),
(31, 2, 31, 'Tax Deducted At Source', 'name', '', 'AccountB0.Tds_tbl', '', '', 1, 0, 0, ''),
(32, 1, 32, 'Purge Allowed', 'name', '', 'AccountModBO.PurgeFlag', 'N', '', 3, 0, 0, ''),
(33, 2, 33, 'Relationship Opening Date', 'name', '', '3_AccountB0.RelationshipOpeningDate', '', '', 1, 0, 0, ''),
(34, 1, 34, 'Save Button', 'name', 'saveBut', '', '', '', 2, 0, 0, ''),
(35, 1, 35, 'Tab Contact Detail', 'id', 'tab_tpageCont3', '', '', '', 2, 0, 0, ''),
(36, 1, 36, 'Mailing Address Click', 'class', 'sbttn', '', '', '', 2, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `corerole`
--

DROP TABLE IF EXISTS `corerole`;
CREATE TABLE IF NOT EXISTS `corerole` (
  `roleId` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `isDel` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleId`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `corerole`
--

INSERT INTO `corerole` (`roleId`, `roleName`, `description`, `isDel`, `system`) VALUES
(1, 'Super Admin', '', 0, 1),
(2, 'HFC Admin', '', 0, 0),
(3, 'HFC Users', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `coreusers`
--

DROP TABLE IF EXISTS `coreusers`;
CREATE TABLE IF NOT EXISTS `coreusers` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(255) NOT NULL,
  `emailId` varchar(255) NOT NULL,
  `passWord` varchar(255) NOT NULL,
  `isDel` int(11) NOT NULL DEFAULT '0',
  `roleId` int(11) NOT NULL,
  `creationDate` date NOT NULL,
  `empID` varchar(255) NOT NULL,
  `modifyOn` datetime NOT NULL,
  `deletionDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `deletedBy` int(11) NOT NULL,
  `isLogin` int(11) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=1281 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coreusers`
--

INSERT INTO `coreusers` (`userId`, `fullName`, `emailId`, `passWord`, `isDel`, `roleId`, `creationDate`, `empID`, `modifyOn`, `deletionDate`, `createdBy`, `deletedBy`, `isLogin`) VALUES
(1, 'HFC RPA Admin', 'hfc@icici.com', 'hfc@1234', 0, 1, '2016-08-03', '', '2019-06-07 14:46:11', '2017-10-24 19:10:22', 1, 0, 0),
(2, 'HFC Admin ', 'admin@hfc.com', 'vara123', 0, 2, '2016-08-07', '', '2018-10-29 16:03:26', '2017-10-24 19:10:22', 1, 0, 0),
(3, 'HFC User', 'user@hfc.com', 'User@123', 0, 3, '0000-00-00', 'HE000827', '2019-01-07 18:36:24', '2017-10-24 19:10:22', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hfccustdata`
--

DROP TABLE IF EXISTS `hfccustdata`;
CREATE TABLE IF NOT EXISTS `hfccustdata` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `TRNREFNO` varchar(255) NOT NULL,
  `BRCODE` varchar(255) DEFAULT NULL,
  `SBCODE` varchar(255) DEFAULT NULL,
  `BNKSRL` varchar(255) DEFAULT NULL,
  `APPLNO` varchar(255) DEFAULT NULL,
  `NAME` varchar(255) DEFAULT NULL,
  `ADD1` varchar(255) DEFAULT NULL,
  `ADD2` varchar(255) DEFAULT NULL,
  `ADD3` varchar(255) DEFAULT NULL,
  `CITY` varchar(255) DEFAULT NULL,
  `STATE` varchar(255) DEFAULT NULL,
  `PIN` varchar(255) DEFAULT NULL,
  `TYPE` varchar(255) DEFAULT NULL,
  `TENURE` varchar(255) DEFAULT NULL,
  `CATE` varchar(255) DEFAULT NULL,
  `FOLIO` varchar(255) DEFAULT NULL,
  `EMPCODE` varchar(255) DEFAULT NULL,
  `STATUS` varchar(255) DEFAULT NULL,
  `AMOUNT` varchar(255) DEFAULT NULL,
  `PAYMODE` varchar(255) DEFAULT NULL,
  `INSTNO` varchar(255) DEFAULT NULL,
  `INSTDT` varchar(255) DEFAULT NULL,
  `PANGIR1` varchar(255) DEFAULT NULL,
  `DOB` varchar(255) DEFAULT NULL,
  `NGNAME` varchar(255) DEFAULT NULL,
  `BANKAC` varchar(255) DEFAULT NULL,
  `BANKNM` varchar(255) DEFAULT NULL,
  `BCITY` varchar(255) DEFAULT NULL,
  `MICR` varchar(255) DEFAULT NULL,
  `GNAME` varchar(255) DEFAULT NULL,
  `GPAN` varchar(255) DEFAULT NULL,
  `ACTYPE` varchar(255) DEFAULT NULL,
  `RTGSCOD` varchar(255) DEFAULT NULL,
  `NNAME` varchar(255) DEFAULT NULL,
  `NADD1` varchar(255) DEFAULT NULL,
  `NADD2` varchar(255) DEFAULT NULL,
  `NADD3` varchar(255) DEFAULT NULL,
  `NCITY` varchar(255) DEFAULT NULL,
  `NPIN` varchar(255) DEFAULT NULL,
  `ENCL` varchar(255) DEFAULT NULL,
  `TELNO` varchar(255) DEFAULT NULL,
  `JH1NAME` varchar(255) DEFAULT NULL,
  `JH2NAME` varchar(255) DEFAULT NULL,
  `JH1PAN` varchar(255) DEFAULT NULL,
  `JH2PAN` varchar(255) DEFAULT NULL,
  `JH1RELATION` varchar(255) DEFAULT NULL,
  `JH2RELATION` varchar(255) DEFAULT NULL,
  `HLDINGPATT` varchar(255) DEFAULT NULL,
  `SUBTYPE` varchar(255) DEFAULT NULL,
  `EMAILID` varchar(255) DEFAULT NULL,
  `MOBILENO` varchar(255) DEFAULT NULL,
  `IFSC` varchar(255) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `cifid_1` varchar(100) DEFAULT NULL,
  `cifid_2` varchar(100) DEFAULT NULL,
  `cifid_3` varchar(100) DEFAULT NULL,
  `is_existing_cust_1` int(1) DEFAULT NULL,
  `is_existing_cust_2` int(1) DEFAULT NULL,
  `is_existing_cust_3` int(1) DEFAULT NULL,
  `edit_cifid_1` int(1) DEFAULT NULL,
  `edit_cifid_2` int(1) DEFAULT NULL,
  `edit_cifid_3` int(1) DEFAULT NULL,
  `AccountNo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`TRNREFNO`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hfccustdata`
--

INSERT INTO `hfccustdata` (`id`, `TRNREFNO`, `BRCODE`, `SBCODE`, `BNKSRL`, `APPLNO`, `NAME`, `ADD1`, `ADD2`, `ADD3`, `CITY`, `STATE`, `PIN`, `TYPE`, `TENURE`, `CATE`, `FOLIO`, `EMPCODE`, `STATUS`, `AMOUNT`, `PAYMODE`, `INSTNO`, `INSTDT`, `PANGIR1`, `DOB`, `NGNAME`, `BANKAC`, `BANKNM`, `BCITY`, `MICR`, `GNAME`, `GPAN`, `ACTYPE`, `RTGSCOD`, `NNAME`, `NADD1`, `NADD2`, `NADD3`, `NCITY`, `NPIN`, `ENCL`, `TELNO`, `JH1NAME`, `JH2NAME`, `JH1PAN`, `JH2PAN`, `JH1RELATION`, `JH2RELATION`, `HLDINGPATT`, `SUBTYPE`, `EMAILID`, `MOBILENO`, `IFSC`, `filename`, `cifid_1`, `cifid_2`, `cifid_3`, `is_existing_cust_1`, `is_existing_cust_2`, `is_existing_cust_3`, `edit_cifid_1`, `edit_cifid_2`, `edit_cifid_3`, `AccountNo`) VALUES
(1, '1234565', 'ICICI Securities', '', '1234565', '1234565', 'MR HRISHIKESH P KADAM', 'A 2804, DOSTI VIHAR,VIJETA', 'VARTAK NAGAR', 'CADBURY JUNCTION', 'THANE', 'MAHARASHTRA', '400606', 'Y', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'AFQWB8898C', '30/08/1997', '', '81025352525', 'ICICI Bank', 'Deoghar', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'hrishikeshkadam82@gmail.com', '9833994163', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '1234566', 'ICICI Securities', '', '1234566', '1234566', 'MR Bahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'JOYKS3079A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994166', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '1234567', 'ICICI Securities', '', '1234567', '1234567', 'MR Sanjay Yadav', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'Q', '12', 'S', '', '', 'R', '20000', 'DD', '7359253', '7/5/2019', 'AFVPS8123P', '31/12/1965', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994163', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '1234568', 'ICICI Securities', '', '1234568', '1234568', 'MR Rahul Patel', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'M', '12', 'S', '', '', 'R', '10000', 'DD', '7359253', '7/5/2019', 'DSWTS4178L', '31/12/2000', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994165', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '1234569', 'ICICI Securities', '', '1234569', '1234569', 'MR Vijay Singh', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '60000', 'DD', '7359253', '7/5/2019', 'EOBKS4088A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994164', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '1234570', 'ICICI Securities', '', '1234570', '1234570', 'MR Yahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'FNWSS4178A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994162', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '1234571', 'ICICI Securities', '', '1234571', '1234571', 'MR Tahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'GNASS4178A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994169', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '1234572', 'ICICI Securities', '', '1234572', '1234572', 'MR Aahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'HOOSS4178A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994161', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, '1234573', 'ICICI Securities', '', '1234573', '1234573', 'MR Lahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'IOZNS5078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994168', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, '1234574', 'ICICI Securities', '', '1234574', '1234574', 'MR Ajay Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '50000', 'DD', '7359253', '7/5/2019', 'BOZQS4088X', '31/12/1985', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', 'MR AJAY Verma', 'A/303', 'Shri Ganesh', 'Golden Nest, Mira Road', 'Thane', '401107', 'NO', '', 'MR. Aditya Nayak', '', 'AQEBQ8896L', '', '', '', 'Single', 'C', 'gauravyadav82@gmail.com', '9833994163', 'ICIC0000629', '26-09-2019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

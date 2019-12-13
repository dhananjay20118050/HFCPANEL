-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2019 at 11:37 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.8

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
CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetAlternateBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
   select p.applicationno,a.apsno,p.firstname,p.secondary_bank_name,p.secondry_account_number,p.secondry_account_type
 from pldataentry p inner join pl_apsformdata a on p.applicationno=a.txtappformno WHERE p.applicationno in 
(select a.txtappformno from pl_apsformdata where a.apsno=apsNoo);
END$$

CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetApplicantandOfficeTVR` (IN `apsNoo` VARCHAR(50))  BEGIN
   select firstname,tel from pldataentry WHERE apsNo=apsNoo ;   
END$$

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

CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
select firstname,primary_bank_name,primary_account_number,occupational_detail,name_prefix from pldataentry WHERE apsNo=apsNoo ; 
  
END$$

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

CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetCombinedBankingDetail` (IN `apsNoo` VARCHAR(50))  BEGIN
   select firstname,secondary_bank_name,secondry_account_number,customer_category,primary_branch from pldataentry WHERE apsNo=apsNoo ;    
END$$

CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetDoctor_SelfEmployed` (IN `apsNoo` VARCHAR(50))  BEGIN     
select credit_facility_amount from pldataentry WHERE apsNo=apsNoo ; 
END$$

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

CREATE DEFINER=`bpo_pluser`@`10.1.22.117` PROCEDURE `sp_GetReferenceTVR` (IN `apsNoo` VARCHAR(50))  BEGIN    
 select reference1_name,reference1_phone,reference1_add,kyc_verification_emp_designation,reference1_relationship,reference2_name,
reference2_phone,reference2_add,reference2_relationship,concat(std_code,'-',tel) As tel,residentmobile,concat(companystd,'-',company_tel) As company_tel,exp_current_job,no_of_year,exp_total_job 
 from pldataentry  WHERE apsNo=apsNoo ; 
END$$

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

CREATE TABLE `bot_aps_tracking` (
  `tracking_id` int(11) NOT NULL,
  `TRNREFNO` varchar(255) DEFAULT NULL,
  `start_time` varchar(45) DEFAULT NULL,
  `end_time` varchar(45) DEFAULT NULL,
  `status` varchar(2) DEFAULT NULL,
  `last_process_entry` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `userid` varchar(11) NOT NULL,
  `upload_datetime` varchar(45) NOT NULL,
  `upload_user` int(11) NOT NULL,
  `is_pan_checked` int(1) NOT NULL DEFAULT 0,
  `is_logged_in` int(1) NOT NULL DEFAULT 0,
  `is_processed` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_aps_tracking`
--

INSERT INTO `bot_aps_tracking` (`tracking_id`, `TRNREFNO`, `start_time`, `end_time`, `status`, `last_process_entry`, `ip_address`, `userid`, `upload_datetime`, `upload_user`, `is_pan_checked`, `is_logged_in`, `is_processed`) VALUES
(21, '1234565', '2019-09-23 16:47:54', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No'),
(22, '1234566', '2019-09-23 17:47:43', '2019-09-23 17:52:20', 'Y', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'Yes'),
(23, '1234567', '2019-09-20 18:14:22', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No'),
(24, '1234568', '2019-09-23 17:03:25', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No'),
(25, '1234569', '2019-09-23 17:24:02', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No'),
(26, '1234570', '2019-09-23 17:26:19', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No'),
(27, '1234571', '2019-09-20 18:38:17', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 0, 1, 'No'),
(28, '1234572', '2019-09-23 17:22:14', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 1, 'No'),
(29, '1234573', '2019-09-23 17:13:22', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 1, 'No'),
(30, '1234574', '2019-09-23 17:20:56', '2019-09-23 17:51:02', 'E', 0, '10.27.175.127', '1', '2019-09-19 19:14:08', 1, 1, 0, 'No');

-- --------------------------------------------------------

--
-- Table structure for table `bot_aps_trackingold`
--

CREATE TABLE `bot_aps_trackingold` (
  `tracking_id` int(11) NOT NULL,
  `txtAppFormNo` varchar(255) DEFAULT NULL,
  `apsNo` varchar(255) DEFAULT NULL,
  `start_time` varchar(45) DEFAULT NULL,
  `end_time` varchar(45) DEFAULT NULL,
  `start_userId` int(11) DEFAULT NULL,
  `resume_userId` int(11) DEFAULT NULL,
  `status` varchar(1) DEFAULT NULL,
  `last_process_entry` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `isCibilDownloaded` int(1) DEFAULT 0,
  `isSourcing` int(1) DEFAULT 0,
  `IsCamGen` int(11) DEFAULT NULL,
  `qc_status` varchar(1) DEFAULT 'N',
  `cam_status` varchar(1) DEFAULT 'N',
  `is_auto_qc_done` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `bot_control_mst` (
  `control_id` int(5) NOT NULL,
  `desc` varchar(45) DEFAULT NULL
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

CREATE TABLE `bot_error_logs` (
  `log_id` int(11) NOT NULL,
  `exception_class` varchar(255) DEFAULT NULL,
  `TRNREFNO` varchar(100) DEFAULT NULL,
  `exception_dtl` varchar(5000) DEFAULT NULL,
  `datetime` timestamp NULL DEFAULT current_timestamp(),
  `error_section` varchar(100) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `screenshot_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bot_error_logs`
--

INSERT INTO `bot_error_logs` (`log_id`, `exception_class`, `TRNREFNO`, `exception_dtl`, `datetime`, `error_section`, `userId`, `screenshot_path`) VALUES
(1, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234574', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-20 08:10:39', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_13_40_39.png'),
(2, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234574', 'Unable to get browser\n', '2019-09-20 08:10:44', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_13_40_44.png'),
(3, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234574', 'Window is closed\n', '2019-09-20 08:10:49', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_13_40_49.png'),
(4, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234565', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 08:11:23', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_13_41_23.png'),
(5, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 08:11:48', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_13_41_48.png'),
(6, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 08:11:53', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_13_41_53.png'),
(7, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234567', '', '2019-09-20 08:18:24', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_13_48_24.png'),
(8, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: The session expired. You will be logged out.\n', '2019-09-20 08:27:56', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_13_57_56.png'),
(9, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234567', '', '2019-09-20 08:33:01', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_03_01.png'),
(10, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Could not send or receive data from server.\n', '2019-09-20 08:35:29', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_05_29.png'),
(11, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234567', '', '2019-09-20 08:40:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_10_34.png'),
(12, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-20 08:41:01', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_11_01.png'),
(13, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-20 08:41:05', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_11_05.png'),
(14, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-20 08:41:38', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_11_38.png'),
(15, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 08:43:00', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_00.png'),
(16, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 08:43:05', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_05.png'),
(17, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 08:43:10', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_10.png'),
(18, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 08:43:16', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_16.png'),
(19, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 08:43:21', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_21.png'),
(20, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 08:43:28', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_13_28.png'),
(21, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234572', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 08:44:04', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_14_14_04.png'),
(22, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234572', '', '2019-09-20 08:44:25', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_14_14_25.png'),
(23, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234572', '', '2019-09-20 08:44:45', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_14_14_45.png'),
(24, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 08:45:12', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_15_12.png'),
(25, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234565', '', '2019-09-20 08:50:16', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_20_16.png'),
(26, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234565', '', '2019-09-20 09:00:20', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_30_20.png'),
(27, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234565', '', '2019-09-20 09:05:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_35_25.png'),
(28, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234565', '', '2019-09-20 09:15:30', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_45_30.png'),
(29, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:20:36', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_50_36.png'),
(30, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:23:44', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_53_44.png'),
(31, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:24:10', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_54_10.png'),
(32, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 09:24:44', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_54_44.png'),
(33, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 09:24:50', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_54_50.png'),
(34, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 09:24:57', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_14_54_57.png'),
(35, 'Facebook\\WebDriver\\Exception\\UnexpectedJavascriptException', '1234572', 'JavaScript error\n', '2019-09-20 09:25:38', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_14_55_38.png'),
(36, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234572', '', '2019-09-20 09:25:59', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_14_55_59.png'),
(37, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:26:27', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_56_27.png'),
(38, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Unable to get browser\n', '2019-09-20 09:27:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_57_27.png'),
(39, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 09:27:32', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_57_32.png'),
(40, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 09:27:38', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_57_38.png'),
(41, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 09:27:43', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_57_43.png'),
(42, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 09:27:50', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_14_57_50.png'),
(43, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:28:30', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_14_58_30.png'),
(44, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234570', '', '2019-09-20 09:28:52', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_14_58_52.png'),
(45, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234570', '', '2019-09-20 09:29:12', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_14_59_12.png'),
(46, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:29:40', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_14_59_40.png'),
(47, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234568', '', '2019-09-20 09:30:02', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_15_00_02.png'),
(48, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234568', '', '2019-09-20 09:30:22', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_15_00_22.png'),
(49, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:30:50', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_15_00_50.png'),
(50, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234573', '', '2019-09-20 09:31:11', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_15_01_11.png'),
(51, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234573', '', '2019-09-20 09:31:32', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_15_01_32.png'),
(52, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:39:57', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_09_57.png'),
(53, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:45:19', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_15_19.png'),
(54, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:45:44', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_15_44.png'),
(55, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:46:09', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_16_09.png'),
(56, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:46:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_16_34.png'),
(57, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:46:59', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_16_59.png'),
(58, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 09:47:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_15_17_25.png'),
(59, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234565', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 11:18:49', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_48_49.png'),
(60, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234565', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 11:18:55', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_48_55.png'),
(61, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-20 11:19:04', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_49_04.png'),
(62, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 11:19:09', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_49_09.png'),
(63, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 11:19:15', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_49_15.png'),
(64, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 11:19:22', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_49_22.png'),
(65, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 11:19:45', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_49_45.png'),
(66, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:20:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_50_11.png'),
(67, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Currently focused window has been closed.\n', '2019-09-20 11:20:21', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_50_21.png'),
(68, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234567', 'java.net.SocketException: Connection reset\n', '2019-09-20 11:20:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_50_27.png'),
(69, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234565', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 11:20:47', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_50_47.png'),
(70, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:21:12', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_51_12.png'),
(71, 'Facebook\\WebDriver\\Exception\\NoAlertOpenException', '1234565', 'No alert is active\n', '2019-09-20 11:21:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_51_25.png'),
(72, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-20 11:21:30', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_16_51_30.png'),
(73, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Unable to get browser\n', '2019-09-20 11:23:11', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_53_11.png'),
(74, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 11:23:16', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_53_16.png'),
(75, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #Submit2\n', '2019-09-20 11:26:14', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_56_14.png'),
(76, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234567', '', '2019-09-20 11:26:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_56_25.png'),
(77, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 11:26:54', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_56_54.png'),
(78, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Unable to get browser\n', '2019-09-20 11:27:07', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_57_07.png'),
(79, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234567', 'Connection refused: connect', '2019-09-20 11:27:13', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_57_13.png'),
(80, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #Submit2\n', '2019-09-20 11:27:27', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_57_27.png'),
(81, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 11:27:55', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_57_55.png'),
(82, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:28:40', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_16_58_40.png'),
(83, 'Facebook\\WebDriver\\Exception\\UnexpectedJavascriptException', '1234574', 'JavaScript error\n', '2019-09-20 11:30:02', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_00_02.png'),
(84, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234574', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 11:30:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_00_27.png'),
(85, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234574', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 11:30:53', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_00_53.png'),
(86, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234574', 'java.net.SocketException: Connection reset\n', '2019-09-20 11:31:43', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_01_43.png'),
(87, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Currently focused window has been closed.\n', '2019-09-20 11:32:13', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_02_13.png'),
(88, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 11:32:18', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_02_18.png'),
(89, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Currently focused window has been closed.\n', '2019-09-20 11:34:02', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_04_02.png'),
(90, 'Facebook\\WebDriver\\Exception\\UnexpectedJavascriptException', '1234567', 'JavaScript error\n', '2019-09-20 11:35:54', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_05_54.png'),
(91, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == *[name=\'CRMServer\']\n', '2019-09-20 11:36:24', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_06_24.png'),
(92, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:36:49', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_06_49.png'),
(93, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234567', 'Connection refused: connect', '2019-09-20 11:37:33', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_07_33.png'),
(94, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234567', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-20 11:47:30', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_17_30.png'),
(95, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Unable to get browser\n', '2019-09-20 11:47:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_17_34.png'),
(96, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 11:47:40', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_17_40.png'),
(97, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-20 11:49:39', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_19_39.png'),
(98, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:50:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_20_11.png'),
(99, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:50:37', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_20_37.png'),
(100, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:51:01', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_21_01.png'),
(101, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:51:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_21_27.png'),
(102, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:51:53', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_21_53.png'),
(103, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:52:22', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_22_22.png'),
(104, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 11:52:50', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_17_22_50.png'),
(105, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #menuName\n', '2019-09-20 11:59:44', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_29_44.png'),
(106, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #contractIntRate\n', '2019-09-20 12:03:25', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_33_25.png'),
(107, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234572', 'Unable to find element with css selector == #moreInfoContainer\n', '2019-09-20 12:05:31', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_35_31.png'),
(108, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234572', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:06:08', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_36_08.png'),
(109, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Window is closed\n', '2019-09-20 12:06:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_36_34.png'),
(110, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Window is closed\n', '2019-09-20 12:06:39', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_36_39.png'),
(111, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234572', 'Connection refused: connect', '2019-09-20 12:06:47', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_36_47.png'),
(112, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234572', 'Connection refused: connect', '2019-09-20 12:06:59', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_36_59.png'),
(113, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234574', '', '2019-09-20 12:07:31', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_37_31.png'),
(114, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234574', 'Unable to find element with css selector == *[name=\'AccountBO.Address.PreferredFormat\']\n', '2019-09-20 12:18:23', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_48_23.png'),
(115, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234574', 'Unable to get browser\n', '2019-09-20 12:18:53', 'Finacle Log Out Error', 0, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_48_53.png'),
(116, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234574', 'Window is closed\n', '2019-09-20 12:18:58', 'Finacle Log Out Error', 0, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_17_48_58.png'),
(117, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Currently focused window has been closed.\n', '2019-09-20 12:20:02', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_02.png'),
(118, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 12:20:07', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_07.png'),
(119, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 12:20:12', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_12.png'),
(120, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 12:20:18', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_18.png'),
(121, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 12:20:23', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_23.png'),
(122, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Window is closed\n', '2019-09-20 12:20:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_50_34.png'),
(123, 'Facebook\\WebDriver\\Exception\\WebDriverCurlException', '1234572', '', '2019-09-20 12:21:22', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_17_51_22.png'),
(124, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #contractIntRate\n', '2019-09-20 12:29:02', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_17_59_02.png'),
(125, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Currently focused window has been closed.\n', '2019-09-20 12:33:31', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_18_03_31.png'),
(126, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Currently focused window has been closed.\n', '2019-09-20 12:33:38', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_18_03_38.png'),
(127, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Window is closed\n', '2019-09-20 12:33:43', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_18_03_43.png'),
(128, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234567', 'Unable to find element with css selector == #contractIntRate\n', '2019-09-20 12:36:01', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_18_06_01.png'),
(129, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234574', 'Unable to find element with css selector == #menuName\n', '2019-09-20 12:44:08', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_20_18_14_08.png'),
(130, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234567', 'Currently focused window has been closed.\n', '2019-09-20 12:45:58', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234567_2019_09_20_18_15_58.png'),
(131, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234572', 'Unable to find element with css selector == #menuName\n', '2019-09-20 12:50:20', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_20_18_20_20.png'),
(132, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-20 12:52:06', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_20_18_22_06.png'),
(133, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'AccountBO.PhoneEmail.PhoneEmailType\']\n', '2019-09-20 12:54:38', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_24_38.png'),
(134, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:55:13', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_25_13.png'),
(135, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:55:48', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_25_48.png'),
(136, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:56:24', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_26_24.png'),
(137, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:56:59', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_26_59.png'),
(138, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:57:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_27_34.png'),
(139, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-20 12:58:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_20_18_28_11.png'),
(140, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 12:59:38', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_29_38.png'),
(141, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:00:04', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_30_04.png'),
(142, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:00:30', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_30_30.png'),
(143, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:00:56', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_30_56.png'),
(144, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:01:22', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_31_22.png'),
(145, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:01:48', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_31_48.png'),
(146, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:02:15', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_20_18_32_15.png'),
(147, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 13:03:33', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_33_33.png'),
(148, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:03:59', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_33_59.png'),
(149, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:04:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_34_25.png'),
(150, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:04:50', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_34_50.png'),
(151, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:05:16', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_35_16.png'),
(152, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:05:42', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_35_42.png'),
(153, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234573', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:06:09', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_20_18_36_09.png'),
(154, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 13:07:25', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_37_25.png'),
(155, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234566', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 13:07:37', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_37_37.png'),
(156, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Currently focused window has been closed.\n', '2019-09-20 13:07:44', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_37_44.png'),
(157, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Window is closed\n', '2019-09-20 13:07:49', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_37_49.png'),
(158, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Window is closed\n', '2019-09-20 13:07:55', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_37_55.png'),
(159, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Window is closed\n', '2019-09-20 13:08:02', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_20_18_38_02.png'),
(160, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234571', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-20 13:08:58', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_38_58.png'),
(161, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234571', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 13:09:04', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_39_04.png'),
(162, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234571', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 13:09:21', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_39_21.png'),
(163, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234571', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 13:09:45', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_39_45.png'),
(164, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234571', 'Currently focused window has been closed.\n', '2019-09-20 13:10:05', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_40_05.png'),
(165, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234571', 'Window is closed\n', '2019-09-20 13:10:10', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_40_10.png'),
(166, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234571', 'Window is closed\n', '2019-09-20 13:10:20', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234571_2019_09_20_18_40_20.png'),
(167, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234569', 'Modal dialog present: Do you want to reset the previous session and re-login ?\n', '2019-09-20 13:10:48', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_40_48.png'),
(168, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234569', 'Unable to find element with xpath == /html/body/form/table/tbody/tr[1]/td[2]/table/tbody/tr[1]/td[8]/a\n', '2019-09-20 13:11:14', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_41_14.png'),
(169, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234569', 'Window is closed\n', '2019-09-20 13:11:19', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_41_19.png'),
(170, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234569', 'Window is closed\n', '2019-09-20 13:11:25', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_41_25.png'),
(171, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234569', 'Window is closed\n', '2019-09-20 13:11:30', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_41_30.png'),
(172, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234569', 'Window is closed\n', '2019-09-20 13:11:36', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_20_18_41_36.png'),
(173, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with xpath == /html/body/form/span/table[2]/tbody/tr/td/table/tbody/tr[2]/td[2]\n', '2019-09-23 11:01:10', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_31_10.png'),
(174, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-23 11:01:33', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_31_33.png');
INSERT INTO `bot_error_logs` (`log_id`, `exception_class`, `TRNREFNO`, `exception_dtl`, `datetime`, `error_section`, `userId`, `screenshot_path`) VALUES
(175, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-23 11:01:42', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_31_42.png'),
(176, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-23 11:01:47', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_31_47.png'),
(177, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Window is closed\n', '2019-09-23 11:02:01', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_32_01.png'),
(178, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-23 11:06:13', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_36_13.png'),
(179, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234565', 'Currently focused window has been closed.\n', '2019-09-23 11:12:33', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_42_33.png'),
(180, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234565', '', '2019-09-23 11:15:01', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_45_01.png'),
(181, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234565', 'Modal dialog present: The menu data cannot be fetched . Retry after sometime.\n', '2019-09-23 11:17:27', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_47_27.png'),
(182, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234565', 'Unable to find element with css selector == #accept\n', '2019-09-23 11:20:46', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234565_2019_09_23_16_50_46.png'),
(183, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == input[name=\'radio1\']\n', '2019-09-23 11:24:45', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_16_54_45.png'),
(184, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Currently focused window has been closed.\n', '2019-09-23 11:24:59', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_16_54_59.png'),
(185, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234566', '', '2019-09-23 11:25:30', 'Frame Not Found', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_16_55_30.png'),
(186, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234568', 'java.net.SocketException: Connection reset\n', '2019-09-23 11:25:31', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_16_55_31.png'),
(187, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234568', 'Connection refused: connect', '2019-09-23 11:25:39', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_16_55_39.png'),
(188, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234568', 'Connection refused: connect', '2019-09-23 11:25:47', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_16_55_47.png'),
(189, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234568', 'Connection refused: connect', '2019-09-23 11:25:56', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_16_55_56.png'),
(190, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234568', 'Connection refused: connect', '2019-09-23 11:26:07', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_16_56_07.png'),
(191, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with css selector == *[name=\'EntityDocumentBO.DocTypeCode\']\n', '2019-09-23 11:30:40', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_00_40.png'),
(192, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-23 11:31:15', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_01_15.png'),
(193, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234568', 'Window is closed\n', '2019-09-23 11:31:20', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_01_20.png'),
(194, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234568', 'Window is closed\n', '2019-09-23 11:31:26', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_01_26.png'),
(195, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234568', 'Window is closed\n', '2019-09-23 11:31:39', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_01_39.png'),
(196, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234568', 'Window is closed\n', '2019-09-23 11:31:49', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_01_49.png'),
(197, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234568', 'Unable to find element with css selector == #accept\n', '2019-09-23 11:39:55', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234568_2019_09_23_17_09_55.png'),
(198, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234573', 'Modal dialog present: Are you sure you want to logout?\n', '2019-09-23 11:47:54', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_23_17_17_54.png'),
(199, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234573', 'Currently focused window has been closed.\n', '2019-09-23 11:48:00', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_23_17_18_00.png'),
(200, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234573', 'Window is closed\n', '2019-09-23 11:48:06', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_23_17_18_06.png'),
(201, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234573', 'Window is closed\n', '2019-09-23 11:48:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234573_2019_09_23_17_18_11.png'),
(202, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234574', 'Unable to get browser\n', '2019-09-23 11:50:57', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_23_17_20_57.png'),
(203, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234574', '', '2019-09-23 11:51:28', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234574_2019_09_23_17_21_28.png'),
(204, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234572', 'java.net.SocketException: Connection reset\n', '2019-09-23 11:51:29', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_29.png'),
(205, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234572', 'Connection refused: connect', '2019-09-23 11:51:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_34.png'),
(206, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234572', '', '2019-09-23 11:51:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_34.png'),
(207, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234572', '', '2019-09-23 11:51:34', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_34.png'),
(208, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234572', '', '2019-09-23 11:51:35', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_35.png'),
(209, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234572', '', '2019-09-23 11:51:39', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_21_39.png'),
(210, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234572', 'Unable to find element with css selector == #contractIntRate\n', '2019-09-23 11:53:16', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_23_16.png'),
(211, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Window is closed\n', '2019-09-23 11:53:21', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_23_21.png'),
(212, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234572', 'Window is closed\n', '2019-09-23 11:53:27', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234572_2019_09_23_17_23_27.png'),
(213, 'Facebook\\WebDriver\\Exception\\UnexpectedAlertOpenException', '1234569', 'Modal dialog present: The menu data cannot be fetched . Retry after sometime.\n', '2019-09-23 11:54:29', 'Search Customer ID Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_23_17_24_29.png'),
(214, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234569', 'Unable to find element with css selector == *[name=\'appSelect\']\n', '2019-09-23 11:55:07', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234569_2019_09_23_17_25_07.png'),
(215, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234570', 'Unable to find element with css selector == #accept\n', '2019-09-23 12:03:11', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_23_17_33_11.png'),
(216, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234570', '', '2019-09-23 12:03:24', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234570_2019_09_23_17_33_24.png'),
(217, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'EntityDocumentBO.DocTypeCode\']\n', '2019-09-23 12:09:15', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_39_15.png'),
(218, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-23 12:09:50', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_39_50.png'),
(219, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'loginFrame\']\n', '2019-09-23 12:10:36', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_40_36.png'),
(220, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Currently focused window has been closed.\n', '2019-09-23 12:11:28', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_41_28.png'),
(221, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234566', '', '2019-09-23 12:11:41', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_41_41.png'),
(222, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'CRMServer\']\n', '2019-09-23 12:12:20', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_20.png'),
(223, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234566', '', '2019-09-23 12:12:51', 'Frame Not Found', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_51.png'),
(224, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234566', 'java.net.SocketException: Connection reset\n', '2019-09-23 12:12:52', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_52.png'),
(225, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234566', 'Connection refused: connect', '2019-09-23 12:12:57', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_57.png'),
(226, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:12:57', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_57.png'),
(227, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:12:57', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_57.png'),
(228, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:12:57', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_42_57.png'),
(229, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:13:02', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_43_02.png'),
(230, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == input[name=\'radio1\']\n', '2019-09-23 12:15:48', 'General Tab Entry Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_45_48.png'),
(231, 'Facebook\\WebDriver\\Exception\\NoSuchWindowException', '1234566', 'Currently focused window has been closed.\n', '2019-09-23 12:16:04', 'Start NEW CIFID1', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_46_04.png'),
(232, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234566', '', '2019-09-23 12:16:35', 'Frame Not Found', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_46_35.png'),
(233, 'Facebook\\WebDriver\\Exception\\TimeOutException', '1234566', '', '2019-09-23 12:17:05', 'Start NEW CIFID2', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_05.png'),
(234, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234566', 'java.net.SocketException: Connection reset\n', '2019-09-23 12:17:06', 'Finacle Login Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_06.png'),
(235, 'Facebook\\WebDriver\\Exception\\UnknownServerException', '1234566', 'Connection refused: connect', '2019-09-23 12:17:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_11.png'),
(236, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:17:11', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_11.png'),
(237, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:17:12', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_12.png'),
(238, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:17:12', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_12.png'),
(239, 'Facebook\\WebDriver\\Exception\\NoSuchDriverException', '1234566', '', '2019-09-23 12:17:17', 'Finacle Log Out Error', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_47_17.png'),
(240, 'Facebook\\WebDriver\\Exception\\NoSuchElementException', '1234566', 'Unable to find element with css selector == *[name=\'tdgen.acctName\']\n', '2019-09-23 12:22:20', 'Primary Account Creation', 1, 'C:\\\\xamppp\\\\htdocs\\\\hfc\\\\storage\\\\apps\\\\hfc\\\\screenshots\\\\1234566_2019_09_23_17_52_20.png');

-- --------------------------------------------------------

--
-- Table structure for table `bot_ip_logins`
--

CREATE TABLE `bot_ip_logins` (
  `id` int(10) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `bot_process_dtl` (
  `id` int(10) NOT NULL,
  `process_dtl_id` int(11) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  `process_dtl_desc` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

CREATE TABLE `bot_process_mst` (
  `process_id` int(5) NOT NULL,
  `process_name` varchar(45) DEFAULT NULL
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

CREATE TABLE `bot_rejected_apps` (
  `id` int(11) NOT NULL,
  `txtAppFormNo` varchar(20) NOT NULL,
  `remarks` varchar(500) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `userId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bot_sequence_dtl`
--

CREATE TABLE `bot_sequence_dtl` (
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
  `isSleep` int(11) DEFAULT 0,
  `parent_model` varchar(255) DEFAULT NULL
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

CREATE TABLE `corerole` (
  `roleId` int(11) NOT NULL,
  `roleName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `isDel` int(11) NOT NULL DEFAULT 0,
  `system` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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

CREATE TABLE `coreusers` (
  `userId` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `emailId` varchar(255) NOT NULL,
  `passWord` varchar(255) NOT NULL,
  `isDel` int(11) NOT NULL DEFAULT 0,
  `roleId` int(11) NOT NULL,
  `creationDate` date NOT NULL,
  `empID` varchar(255) NOT NULL,
  `modifyOn` datetime NOT NULL,
  `deletionDate` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `deletedBy` int(11) NOT NULL,
  `isLogin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `coreusers`
--

INSERT INTO `coreusers` (`userId`, `fullName`, `emailId`, `passWord`, `isDel`, `roleId`, `creationDate`, `empID`, `modifyOn`, `deletionDate`, `createdBy`, `deletedBy`, `isLogin`) VALUES
(1, 'Vara Admin', 'vara@united.com', 'vara1234', 0, 1, '2016-08-03', '', '2019-06-07 14:46:11', '2017-10-24 19:10:22', 1, 0, 0),
(2, 'HFC Admin ', 'admin@hfc.com', 'vara123', 0, 2, '2016-08-07', '', '2018-10-29 16:03:26', '2017-10-24 19:10:22', 1, 0, 0),
(3, 'HFC User', 'user@hfc.com', 'User@123', 0, 3, '0000-00-00', 'HE000827', '2019-01-07 18:36:24', '2017-10-24 19:10:22', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `hfccustdata`
--

CREATE TABLE `hfccustdata` (
  `id` int(255) NOT NULL,
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
  ` PAYMODE` varchar(255) DEFAULT NULL,
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
  `is_existing_cust_1` int(1) NOT NULL,
  `is_existing_cust_2` int(1) NOT NULL,
  `is_existing_cust_3` int(1) NOT NULL,
  `edit_cifid_1` int(1) NOT NULL,
  `edit_cifid_2` int(1) NOT NULL,
  `edit_cifid_3` int(1) NOT NULL,
  `AccountNo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hfccustdata`
--

INSERT INTO `hfccustdata` (`id`, `TRNREFNO`, `BRCODE`, `SBCODE`, `BNKSRL`, `APPLNO`, `NAME`, `ADD1`, `ADD2`, `ADD3`, `CITY`, `STATE`, `PIN`, `TYPE`, `TENURE`, `CATE`, `FOLIO`, `EMPCODE`, `STATUS`, `AMOUNT`, ` PAYMODE`, `INSTNO`, `INSTDT`, `PANGIR1`, `DOB`, `NGNAME`, `BANKAC`, `BANKNM`, `BCITY`, `MICR`, `GNAME`, `GPAN`, `ACTYPE`, `RTGSCOD`, `NNAME`, `NADD1`, `NADD2`, `NADD3`, `NCITY`, `NPIN`, `ENCL`, `TELNO`, `JH1NAME`, `JH2NAME`, `JH1PAN`, `JH2PAN`, `JH1RELATION`, `JH2RELATION`, `HLDINGPATT`, `SUBTYPE`, `EMAILID`, `MOBILENO`, `IFSC`, `filename`, `cifid_1`, `cifid_2`, `cifid_3`, `is_existing_cust_1`, `is_existing_cust_2`, `is_existing_cust_3`, `edit_cifid_1`, `edit_cifid_2`, `edit_cifid_3`, `AccountNo`) VALUES
(1, '1234565', 'ICICI Securities', '', '1234565', '1234565', 'MR HRISHIKESH P KADAM', 'A 2804, DOSTI VIHAR,VIJETA', 'VARTAK NAGAR', 'CADBURY JUNCTION', 'THANE', 'MAHARASHTRA', '400606', 'Y', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'AFQWA8896C', '30/08/1997', '', '81025352525', 'ICICI Bank', 'Deoghar', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'hrishikeshkadam82@gmail.com', '9833994163', 'ICIC0000629', '19-09-2019', '661068496', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(2, '1234566', 'ICICI Securities', '', '1234566', '1234566', 'MR Gaurav Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '15', 'S', '', '', 'NRE', '50000', 'DD', '7359253', '7/5/2019', 'BOZQS4078X', '31/12/1985', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', 'MR AJAY Verma', 'A/303', 'Shri Ganesh', 'Golden Nest, Mira Road', 'Thane', '401107', 'NO', '', 'MR. Aditya Nayak', '', 'AQAAQ8896L', '', '', '', 'Single', 'C', 'gauravyadav82@gmail.com', '9833994163', 'ICIC0000629', '19-09-2019', '661068505', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(3, '1234567', 'ICICI Securities', '', '1234567', '1234567', 'MR Rahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'Q', '12', 'S', '', '', 'R', '20000', 'DD', '7359253', '7/5/2019', 'AFVPR8023P', '31/12/1965', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994163', 'ICIC0000629', '19-09-2019', '661068499', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(4, '1234568', 'ICICI Securities', '', '1234568', '1234568', 'MR Pahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'M', '24', 'S', '', '', 'R', '10000', 'DD', '7359253', '7/5/2019', 'DSWSS4078L', '31/12/2000', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994165', 'ICIC0000629', '19-09-2019', '661068502', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(5, '1234569', 'ICICI Securities', '', '1234569', '1234569', 'MR Vahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '60000', 'DD', '7359253', '7/5/2019', 'EOBSS4078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994164', 'ICIC0000629', '19-09-2019', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(6, '1234570', 'ICICI Securities', '', '1234570', '1234570', 'MR Yahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '15', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'FMWSS4078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994162', 'ICIC0000629', '19-09-2019', '661068504', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(7, '1234571', 'ICICI Securities', '', '1234571', '1234571', 'MR Tahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '18', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'GNWSS4078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994169', 'ICIC0000629', '19-09-2019', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(8, '1234572', 'ICICI Securities', '', '1234572', '1234572', 'MR Aahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '32', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'HOXSS4078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994161', 'ICIC0000629', '19-09-2019', '661068501', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(9, '1234573', 'ICICI Securities', '', '1234573', '1234573', 'MR Lahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '32', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'IOWNS4078A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994168', 'ICIC0000629', '19-09-2019', '661068503', NULL, NULL, 0, 0, 0, 0, 0, 0, '1'),
(10, '1234574', 'ICICI Securities', '', '1234574', '1234574', 'MR Bahul Dubey', 'A 303, SHRI GANESH APT', 'SARVODAY COMPLEX', 'GOLDEN NEST', 'MUMBAI', 'MAHARASHTRA', '401107', 'C', '12', 'S', '', '', 'R', '30000', 'DD', '7359253', '7/5/2019', 'JOWKS4079A', '31/12/1997', '', '81025352525', 'ICICI Bank', 'MUMBAI', '814229102', '', '', 'SAVING', '', '', '', '', '', '', '', 'NO', '', '', '', '', '', '', '', 'Single', 'C', 'rahul82@gmail.com', '9833994166', 'ICIC0000629', '19-09-2019', '661068500', NULL, NULL, 0, 0, 0, 0, 0, 0, '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bot_aps_tracking`
--
ALTER TABLE `bot_aps_tracking`
  ADD PRIMARY KEY (`tracking_id`);

--
-- Indexes for table `bot_aps_trackingold`
--
ALTER TABLE `bot_aps_trackingold`
  ADD PRIMARY KEY (`tracking_id`);

--
-- Indexes for table `bot_control_mst`
--
ALTER TABLE `bot_control_mst`
  ADD PRIMARY KEY (`control_id`);

--
-- Indexes for table `bot_error_logs`
--
ALTER TABLE `bot_error_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `bot_ip_logins`
--
ALTER TABLE `bot_ip_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot_process_dtl`
--
ALTER TABLE `bot_process_dtl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot_process_mst`
--
ALTER TABLE `bot_process_mst`
  ADD PRIMARY KEY (`process_id`);

--
-- Indexes for table `bot_rejected_apps`
--
ALTER TABLE `bot_rejected_apps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bot_sequence_dtl`
--
ALTER TABLE `bot_sequence_dtl`
  ADD PRIMARY KEY (`seq_id`);

--
-- Indexes for table `corerole`
--
ALTER TABLE `corerole`
  ADD PRIMARY KEY (`roleId`);

--
-- Indexes for table `coreusers`
--
ALTER TABLE `coreusers`
  ADD PRIMARY KEY (`userId`);

--
-- Indexes for table `hfccustdata`
--
ALTER TABLE `hfccustdata`
  ADD PRIMARY KEY (`TRNREFNO`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bot_aps_tracking`
--
ALTER TABLE `bot_aps_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `bot_aps_trackingold`
--
ALTER TABLE `bot_aps_trackingold`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bot_error_logs`
--
ALTER TABLE `bot_error_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `bot_ip_logins`
--
ALTER TABLE `bot_ip_logins`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bot_process_dtl`
--
ALTER TABLE `bot_process_dtl`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bot_rejected_apps`
--
ALTER TABLE `bot_rejected_apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `corerole`
--
ALTER TABLE `corerole`
  MODIFY `roleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `coreusers`
--
ALTER TABLE `coreusers`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1281;

--
-- AUTO_INCREMENT for table `hfccustdata`
--
ALTER TABLE `hfccustdata`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

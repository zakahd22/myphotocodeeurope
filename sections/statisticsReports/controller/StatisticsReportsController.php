<?php
//IMPORTANT - Global is required.
require_once dirname(__FILE__) . "/../../../common/global.php";
require_once G_PATH.'common/Classes/baseController.php';

class StatisticsReportsController extends baseController {
    public $section = 'StatisticsReports';
    
    private $today;
    private $thisYear;
    private $lastYear;
    private $dateThisYear;
    private $dateLastYear;
    private $thisMonth;
    private $lastMonth;
    private $lastMonthFinish;
    private $lastWeekMonday;
    private $lastWeekSunday;
    private $lastMonthTitle; 
    private $monthTitle;
    
    public function __construct() {
        parent::__construct();
    }
    
    private function getDates($type = 'Y-m-d'){
        $this->today              = date($type, mktime(date("h"), date("i"), date("s"), date("m"), date("d"), date("Y")));
        $this->thisYear           = date("Y");
        $this->lastYear           = date("Y")-1;
        $this->dateThisYear       = date($type, mktime(0, 0, 0, 1, 1, $this->thisYear));
        $this->dateLastYear       = date($type, mktime(0, 0, 0, 1, 1, $this->lastYear));
        $this->thisMonth          = date($type, mktime(0, 0, 0, date("m"), 1, date("Y")));
        $this->lastMonth          = date($type, mktime(0, 0, 0, date("m")-1, 1, date("Y")));
        $this->lastMonthTitle     = date("F, Y" , strtotime($this->lastMonth));
        $this->monthTitle         = date("F, Y" , strtotime($this->thisMonth));
        $daysEndLastWeek          = date("w", date($type)) - 1;
        $daysStartLastWeek        = ($daysEndLastWeek - 1) + 7;
        $this->lastWeekMonday     = date($type, strtotime("-$daysStartLastWeek day", strtotime($this->today)));
        $this->lastWeekSunday     = date($type, strtotime("-$daysEndLastWeek day", strtotime($this->today)));
        
        $this->lastMonthFinish = strtotime ( '-1 day' , strtotime ( $this->thisMonth ) ) ;
        $this->lastMonthFinish = date ( $type , $this->lastMonthFinish );
    }
    
    public function getDateIntervalEventsStatistics($dateInit, $dateEnd, $trashed = false){
        $this->createModel('events');
        $this->createView($this->section, 'StatisticsReports');
        $autocreadedData = $this->eventsModel->stdReport_Years($dateInit, $dateEnd, '0');
        $notAutocreadedData = $this->eventsModel->stdReport_Years($dateInit, $dateEnd, '1');
        
        $autocreadedDataTrashed = 0;
        $notAutocreadedDataTrashed = 0;
        if($trashed){
            $this->createModel('trashed_events', false, 'myphotocode_trashed');
            $autocreadedDataTrashed = $this->trashed_eventsModel->stdReport_Years($dateInit, $dateEnd, '0');
            $notAutocreadedDataTrashed = $this->trashed_eventsModel->stdReport_Years($dateInit, $dateEnd, '1');
        }
            
        $data = array(
            "autocreated" => $autocreadedData[0]['counter'] + $autocreadedDataTrashed[0]['counter'],
            "noAutocreated" => $notAutocreadedData[0]['counter'] + $notAutocreadedDataTrashed[0]['counter'],
            "total" => (($autocreadedData[0]['counter'] + $notAutocreadedData[0]['counter'])+($autocreadedDataTrashed[0]['counter']+$notAutocreadedDataTrashed[0]['counter']))
        );
        return $data;
    }
    
    public function createEventsStatistics(){
        $this->getDates('Ymd');
        //Last Year Statistics
        $lastYearData = $this->getDateIntervalEventsStatistics($this->dateLastYear, $this->dateThisYear, true);
        $lastYearTable = $this->StatisticsReportsView->getMailEventsStatisticsReports(
            'Last Year, '.$this->lastYear,
            $lastYearData
        );
        //This Year Statistics
        $thisYearData = $this->getDateIntervalEventsStatistics($this->dateThisYear, $this->today, true);
        $thisYearTable = $this->StatisticsReportsView->getMailEventsStatisticsReports(
            'This Year, '.$this->thisYear, 
            $thisYearData
        );
        //Last Month Statistics
        $lastMonthData = $this->getDateIntervalEventsStatistics($this->lastMonth, $this->thisMonth);
        $lastMonthTable = $this->StatisticsReportsView->getMailEventsStatisticsReports(
            'Last Month, '.$this->lastMonthTitle, 
            $lastMonthData
        );
        //This Month Statistics
        $thisMonthData = $this->getDateIntervalEventsStatistics($this->thisMonth, $this->today);
        $thisMonthTable = $this->StatisticsReportsView->getMailEventsStatisticsReports(
            'This Month, '.$this->monthTitle, 
            $thisMonthData
        );
        //Last week Statistics
        $lastWeekData = $this->getDateIntervalEventsStatistics($this->lastWeekMonday, $this->lastWeekSunday);
        $lastWeekTable = $this->StatisticsReportsView->getMailEventsStatisticsReports(
            'Last Week', 
            $lastWeekData
        );
        $html = "<table border='1' style='margin-right:50px; margin:auto;'>"
                . "<tr >"
                    . "<td>Period</td>"
                    . "<td>Autocreated</td>"
                    . "<td>Not Autocreated</td>"
                    . "<td>Total</td>"
                . "</tr>"
                . "{$lastYearTable}"
                . "{$thisYearTable}"
                . "{$lastMonthTable}"
                . "{$thisMonthTable}"
                . "{$lastWeekTable}"
                . "</table>";
        return $html;
    }
    
    public function createUpdatePhotosStatistics(){
        
        utils::log("Trace 0", "logAAAAA");
        
        $this->getDates('Y-m-d G:i:s');
        $this->createModel('photos');
        utils::log("Trace 1", "logAAAAA");
        $this->createModel('statistics_photos', FALSE , 'myphotocode_statistics');
        utils::log("Trace 2", "logAAAAA");
        $this->createView($this->section, 'StatisticsReports');
        
        
        $total_lastyear = $this->photosModel->getStatisticReportInfo($this->dateLastYear, $this->dateThisYear);
        $total_lastyear = $total_lastyear[0]['counter'];
        $total_year = $this->photosModel->getStatisticReportInfo($this->dateThisYear, $this->today);
        $total_year = $total_year[0]['counter'];
        $total_lastmonth  = $this->photosModel->getStatisticReportInfo($this->lastMonth, $this->lastMonthFinish);
        $total_lastmonth = $total_lastmonth[0]['counter'];
        $total_month = $this->photosModel->getStatisticReportInfo($this->thisMonth, $this->today);
        $total_month = $total_month[0]['counter'];
        $total_week = $this->photosModel->getStatisticReportInfo($this->lastWeekMonday, $this->lastWeekSunday);
        $total_week = $total_week[0]['counter'];
        
        $total_lastyearTrashed = 0;
        $total_yearTrashed = 0;
        $this->createModel('trashed_photos', false, 'myphotocode_trashed');
        $total_lastyearTrashed = $this->trashed_photosModel->getStatisticReportInfo($this->dateLastYear, $this->dateThisYear);
        $total_yearTrashed = $this->trashed_photosModel->getStatisticReportInfo($this->dateThisYear, $this->today);
        
        $total_lastYear = (int)$total_lastyear + (int)$total_lastyearTrashed;
        $total_year = (int)$total_year + (int)$total_yearTrashed;
        
        $data = array(
            $total_lastYear,
            $total_year,
            $total_lastmonth,
            $total_month,
            $total_week
        );
        
        $select = array(1,2);
        $lastYearView   = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->dateLastYear, $this->dateThisYear, $select);
        $yearView       = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->dateThisYear, $this->today, $select);
        $lastmonthView  = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->lastMonth, $this->lastMonthFinish, $select);
        $monthView      = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->thisMonth, $this->today, $select);
        $weekView       = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->lastWeekMonday, $this->lastWeekSunday, $select);
        
        $lastYearView = utils::get_percent($lastYearView[0]['summation_'], $total_lastYear);
        $yearView = utils::get_percent($yearView[0]['summation_'], $total_year);
        $lastmonthView = utils::get_percent($lastmonthView[0]['summation_'], $total_lastmonth);
        $monthView = utils::get_percent($monthView[0]['summation_'], $total_month);
        $weekView= utils::get_percent($weekView[0]['summation_'], $total_week);
                
        $viewed = array(
            $lastYearView,
            $yearView,
            $lastmonthView,
            $monthView,
            $weekView
        );
        
        $titles = array(
            'Last Year, '.$this->lastYear,
            'This Year, '.$this->thisYear,
            'Last Month, '.$this->lastMonthTitle,
            'This Month, '.$this->monthTitle,
            'Last Week'
        );
        
                
        $htmlData = $this->StatisticsReportsView->getMailPhotoUploadsStatisticsReports($titles, $data, $viewed);
        return $htmlData;
    }
    
    
    public function createOwnerLoginStatistics(){
        $this->getDates('Y-m-d G:i:s');
        $this->createModel('CLD_ownerConnections');
        $this->createView($this->section, 'StatisticsReports');
        //Last Year Statistics
        $lastYearData = $this->CLD_ownerConnectionsModel->getStatisticsReport($this->dateLastYear, $this->dateThisYear);
        $lastYearTable = $this->StatisticsReportsView->getMailOwnersLoginStatisticsReports(
            'Last Year, '.$this->lastYear,
            $lastYearData
        );
        //This Year Statistics
        $thisYearData  = $this->CLD_ownerConnectionsModel->getStatisticsReport($this->dateThisYear, $this->today);
        $thisYearTable = $this->StatisticsReportsView->getMailOwnersLoginStatisticsReports(
            'This Year, '.$this->thisYear, 
            $thisYearData
        );
        //Last Month Statistics
        $lastMonthData  = $this->CLD_ownerConnectionsModel->getStatisticsReport($this->lastMonth, $this->lastMonthFinish);
        $lastMonthTable = $this->StatisticsReportsView->getMailOwnersLoginStatisticsReports(
            'Last Month, '.$this->lastMonthTitle, 
            $lastMonthData
        );
        //This Month Statistics
        $thisMonthData  = $this->CLD_ownerConnectionsModel->getStatisticsReport($this->thisMonth, $this->today);
        $thisMonthTable = $this->StatisticsReportsView->getMailOwnersLoginStatisticsReports(
            'This Month, '.$this->monthTitle, 
            $thisMonthData
        );
        //Last week Statistics
        $lastWeekData  = $this->CLD_ownerConnectionsModel->getStatisticsReport($this->lastWeekMonday, $this->lastWeekSunday);
        $lastWeekTable = $this->StatisticsReportsView->getMailOwnersLoginStatisticsReports(
            'Last Week', 
            $lastWeekData
        );
        
        $html = "<table border='1' style='margin-right:50px; margin:auto;'>"
                . "<tr ><td>Period</td><td>Location</td><td>Total</td></tr>"
                . "{$lastYearTable}"
                . "{$thisYearTable}"
                . "{$lastMonthTable}"
                . "{$thisMonthTable}"
                . "{$lastWeekTable}"
                . "</table>";
        
        return $html;
    }
    
    
    public function createPhotoFilesStatistics(){
        $this->getDates('Y-m-d');
        $this->createModel('statistics_photo_files', FALSE , 'myphotocode_statistics');
        $this->createView($this->section, 'StatisticsReports');
        
        $total_lastyear =   $this->statistics_photo_filesModel->getStatisticReportInfo($this->dateLastYear, $this->dateThisYear);
        $total_year =      $this->statistics_photo_filesModel->getStatisticReportInfo($this->dateThisYear, $this->today);
        $total_lastmonth  = $this->statistics_photo_filesModel->getStatisticReportInfo($this->lastMonth, $this->lastMonthFinish);
        $total_month =      $this->statistics_photo_filesModel->getStatisticReportInfo($this->thisMonth, $this->today);
        $total_week =       $this->statistics_photo_filesModel->getStatisticReportInfo($this->lastWeekMonday, $this->lastWeekSunday);
        
        $total_lastyear =   $total_lastyear[0]['summation_'];
        $total_year =       $total_year[0]['summation_'];
        $total_lastmonth =  $total_lastmonth[0]['summation_'];
        $total_month =      $total_month[0]['summation_'];
        $total_week =       $total_week[0]['summation_'];
        
        $titles = array(
            'Last Year, '.$this->lastYear,
            'This Year, '.$this->thisYear,
            'Last Month, '.$this->lastMonthTitle,
            'This Month, '.$this->monthTitle,
            'Last Week'
        );
        
        $data = array(
            (int)$total_lastYear,
            (int)$total_year,
            (int)$total_lastmonth,
            (int)$total_month,
            (int)$total_week
        );
        
        $htmlData = $this->StatisticsReportsView->getMailPhotoFilesStatisticsReports($titles, $data);
        return $htmlData;
    }
    
    
  
    public function createCloudShareStatistics(){
        $this->getDates('Y-m-d');
        $this->createModel('statistics_photos', FALSE , 'myphotocode_statistics');
        $this->createView($this->section, 'StatisticsReports');
    
        $x = 0;
        $statisticsArray = array();
        for ($i = 2; $i <= 5; $i++) {
            
            $lastYear   = "";
            $year       = "";
            $lastmonth  = "";
            $month      = "";
            $week       = "";
            
            if($i == 2){
                $select = array(1,2);
                $lastYear =   $this->statistics_photosModel->getStatisticVisitCloudInfo($this->dateLastYear, $this->dateThisYear, $select);
                $year =       $this->statistics_photosModel->getStatisticVisitCloudInfo($this->dateThisYear, $this->today, $select);
                $lastmonth  = $this->statistics_photosModel->getStatisticVisitCloudInfo($this->lastMonth, $this->lastMonthFinish, $select);
                $month =      $this->statistics_photosModel->getStatisticVisitCloudInfo($this->thisMonth, $this->today, $select);
                $week =       $this->statistics_photosModel->getStatisticVisitCloudInfo($this->lastWeekMonday, $this->lastWeekSunday, $select);
            }
            else{
                $select = $i;

                $lastYear =   $this->statistics_photosModel->getStatisticSocialInfo($this->dateLastYear, $this->dateThisYear, $select);
                $year =       $this->statistics_photosModel->getStatisticSocialInfo($this->dateThisYear, $this->today, $select);
                $lastmonth =  $this->statistics_photosModel->getStatisticSocialInfo($this->lastMonth, $this->lastMonthFinish, $select);
                $month =      $this->statistics_photosModel->getStatisticSocialInfo($this->thisMonth, $this->today, $select);
                $week =       $this->statistics_photosModel->getStatisticSocialInfo($this->lastWeekMonday, $this->lastWeekSunday, $select);
            }
            
            $lastYear   =   $lastYear[0]['summation_'];
            $year       =   $year[0]['summation_'];
            $lastmonth  =   $lastmonth[0]['summation_'];
            $month      =   $month[0]['summation_'];
            $week       =   $week[0]['summation_'];
            
            $array = array($lastYear, $year, $lastmonth, $month, $week);

            $statisticsArray[$x] = $array;
            $x ++;
        }
        
        $titles = array(
            'Last Year, '.$this->lastYear,
            'This Year, '.$this->thisYear,
            'Last Month, '.$this->lastMonthTitle,
            'This Month, '.$this->monthTitle,
            'Last Week'
        );
        
        
        $htmlData = $this->StatisticsReportsView->getMailCloudStatisticsReports($statisticsArray, $titles);
        
        return $htmlData;
    }
    
    
}
        
<?php
//IMPORTANT - Global is required.
//require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH.'common/Classes/baseController.php';
require_once G_PATH.'common/Classes/geoplugin.class.php';

class StatisticsController extends baseController{
    private function geoLocale($ip){
        if($ip){
            $geoplugin = new geoPlugin();
            try {
                $geoplugin->locate($ip);
            } catch (Exception $e){
                utils::log($e, 'log_geoplugin');
            }
            return $geoplugin;
        }
        else {
            return false;
        }
    }
    
    public function getIpUser(){
        $ip = NULL;
        if (isset($_SERVER)) {
            $client = @$_SERVER['HTTPS_CLIENT_IP'];
            $forward = @$_SERVER['HTTPS_X_FORWARDED_FOR'];
            $remote = $_SERVER['REMOTE_ADDR'];
        }
        $result = "Unknown";
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }
    
    public function saveStdOwnerLogin($id_user, $userType, $ip){
        $geoplugin = false;
        $date = utils::get_datetime('Y-m-d H:i:s');
        $city = "-";
        if($id_user && $userType){
            if($ip){
                $geolocale = $this->geoLocale($ip);
                $state = $geolocale->city;
                $country = $geolocale->countryName;
            }
            if(!$geolocale){
                $country = "-";
                $state = "-";
            }
            
            $this->createModel('CLD_ownerConnections');
            $this->entity->loadEntity('CLD_ownerConnections');
            $this->entity->setValue("user", $id_user);
            $this->entity->setValue("type_user", $userType);
            $this->entity->setValue("data", $date);
            $this->entity->setValue("pais", $country);
            $this->entity->setValue("state", $state);
            $this->entity->setValue("ciutat", $city);
//            utils::log(' user => ' . $id_user .
//                       '\n type_user => ' . $userType .
//                       '\n data => ' . $date .
//                       '\n pais => ' . $country .
//                       '\n state => ' . $state .
//                       '\n ciutat => ' . $city .
//                       '\n ip => ' . $ip ,
//                'locationOwnerLogin');
            $this->CLD_ownerConnectionsModel->insertCLD_ownerConnections();
        }
        
    }
    
    public function saveStdLookPhotos($typeInfo = false, $imgCode = false, $ip = false){
        $this->createModel('photos');
        $this->createModel('statistics_photos', false, 'myphotocode_statistics');
        $date = utils::get_datetime('Y-m-d');
        $state = NULL;
        $country = NULL;
        $event = NULL;
        $geolocale = $this->geoLocale($ip);
        if($geolocale){
            $state = $geolocale->city;
            $country = $geolocale->countryName;
        }
        if($imgCode){
            $event = $this->photosModel->getEventPhoto($imgCode);
            $event = $event[0]['event_id'];
        }
        try{
            $statistics_photos = $this->statistics_photosModel->getStatistic($imgCode, $date, $typeInfo, $state, $country);
            if($statistics_photos[0]['counter']==1){
                $num_show = $statistics_photos[0]['num_show'] + 1;
                $updates = array(
                    'num_show' => $num_show
                );
                $this->statistics_photosModel->updateStd_photos($statistics_photos[0]['id'], $updates);
            } else {
                $this->entity->loadEntity('statistics_photos');
                $this->entity->setValue("code_photo", $imgCode);
                $this->entity->setValue("event", $event);
                $this->entity->setValue("date", $date);
                $this->entity->setValue("type_info", $typeInfo);
                $this->entity->setValue("state", $state);
                $this->entity->setValue("country", $country);
                $this->entity->setValue("num_show", 1);
                $this->statistics_photosModel->insertStd_photos();
            }
        } catch (Exception $e){
            utils::log('Error: '.$e, 'logStatisticsController');
        }
    }    
    
    public function saveStdScript($typeInfo = false, $imgCode = false, $event = false, $date = NULL, $state = NULL, $country = NULL){
        $this->createModel('statistics_photos', false, 'myphotocode_statistics');
        if ($date == NULL){
            $date = utils::get_datetime('Y-m-d');
        }
        $return = false;
        $statistics_photos = $this->statistics_photosModel->getStatistic($imgCode, $date, $typeInfo, $state, $country);
        if($statistics_photos[0]['counter']==1){
            try{
                $num_show = $statistics_photos[0]['num_show'] + 1;
                $updates = array(
                    'num_show' => $num_show
                );
                $this->statistics_photosModel->updateStd_photos($statistics_photos[0]['id'], $updates);
                $return = true;
            } catch (Exception $e){
                utils::log('Error: '.$e, 'logStatisticsController');
            }
        } else {
            try{
                $this->entity->loadEntity('statistics_photos');
                $this->entity->setValue("code_photo", $imgCode);
                $this->entity->setValue("event", $event);
                $this->entity->setValue("date", $date);
                $this->entity->setValue("type_info", $typeInfo);
                $this->entity->setValue("state", $state);
                $this->entity->setValue("country", $country);
                $this->entity->setValue("num_show", 1);
                $this->statistics_photosModel->insertStd_photos();
                $return = true;
            } catch (Exception $e){
                utils::log('Error: '.$e, 'logStatisticsController');
            }
        }
        return $return;
    }    
    
    public function getStatisticsEvent($idEvent){
        $this->createModel('view_statistic_photos', false, 'myphotocode_statistics');
        return $this->view_statistic_photosModel->getStatisticsEvent($idEvent);
    }
    public function getStatisticsPhoto($photoCode){
        $this->createModel('view_statistic_photos', false, 'myphotocode_statistics');
        return $this->view_statistic_photosModel->getStatisticsPhoto($photoCode);
    }
    public function getStatisticsPhotoType($photoCode, $type){
        $this->createModel('view_statistic_photos', false, 'myphotocode_statistics');
        return $this->view_statistic_photosModel->getStatisticsPhotoType($photoCode, $type);
    }
    public function getStatisticsPhotoTypeFromTable($photoCode, $type){
        $this->createModel('view_statistic_photos', false, 'myphotocode_statistics');
        return $this->view_statistic_photosModel->getStatisticsPhotoTypeFromTable($photoCode, $type);
    }   
    public function  getStatisticsAllByEvent($idEvent){
        $this->createModel('view_statistic_photos', false, 'myphotocode_statistics');
        return $this->view_statistic_photosModel->getStatisticsAllByEvent($idEvent);
    }
    
}
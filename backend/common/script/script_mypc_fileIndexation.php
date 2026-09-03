<?php
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "common/Classes/compressorUtility.php";

class fileIndexaction extends baseController{
    private $txtDir;
    private $txtDirReaded;
    private $txtFile;
    private $txtFileReaded;
    private $path;
    private $msg;
    private $time_start;
    private $time_end;
    private $files;
    private $files_zip;
    private $files_in_zip;
    private $dir_in_zip;
    private $dir;
    
    /*
     * Al crear la clase nova genera les dades necesaries per executar sense
     * problemes l'script.
     */
    public function __construct() {
        parent::__construct(false);
        
        $this->txtFile = G_PATH . "log/script_mypc_indexation-files.txt";
        $this->txtDir  = G_PATH . "log/script_mypc_indexation.txt";
        $this->path    = G_PATH . "events/*";
        $this->files = 0;
        $this->files_zip = 0;
        $this->files_in_zip = 0;
        $this->dir_in_zip = 0;
        $this->dir = 0;
        $this->readFilesTXT();
        
        $this->getTimeScript(true);
        $this->readDirectory($this->path);
        $this->getTimeScript();
        $this->endScript();
    }
    
    /*
     * Metode per si s'ha d'indexar fitxers d'un altre path
     */
    public function setPath($path){
        $this->path = $path;
    }
    
    /*
     * Obte el temps d'inici i final del script
     */
    private function getTimeScript($start = false){
        if($start){
            $this->time_start = microtime(true);
        }
        else {
            $this->time_end = microtime(true);
        }
    }

    /*
     * Llegeix recursivament els directoris
     * i si es un arxiu l'envia a una altre funcio
     */
    private function readDirectory($dir){
        foreach(glob($dir) as $file) {
            if($this->files < 40000){
                if (filetype($file) == 'dir'){
                    if(!$this->checkDirectory($file)){
                        $this->dir++;
                        $this->readDirectory($file."/*");
                        $this->saveDirectoryList($file);
                    }
                }
                else {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    if(!$this->checkFile($filename)){
                        $this->thisFile($file);
                    }
                }
            }
        }
    }
    
    private function readFilesTXT(){
        if(file_exists($this->txtDir)){
            $txt = file_get_contents($this->txtDir);
            $this->txtDirReaded = explode(',', $txt);
        }
        if(file_exists($this->txtFile)){
            $txt = file_get_contents($this->txtFile);
            $this->txtFileReaded = explode(',', $txt);
        }
    }
    
    /*
     * Comprova si el directori existeix amb el txt que va generat automaticament
     * per no tornar a llegirlo en un futur
     */
    private function checkDirectory($path){
        $rtn = false;
        if($this->txtDirReaded){
            if(in_array(trim($path), $this->txtDirReaded)){
//            foreach($this->txtDirReaded as $dir){
//                if(trim($dir) == trim($path)){
                    $rtn = true;
                    $this->msg = "{$path} -Pass-";
//                    echo $this->msg;
                    utils::log($this->msg, 'logMYPC_fileIndexation');
                    return $rtn;
//                }
            }
        }
        else {
            return $rtn;
        }
    }
    
    private function checkFile($filename){
        $rtn = false;
        if($this->txtFileReaded){
            if(in_array(trim($filename), $this->txtFileReaded)){
//            foreach($this->txtFileReaded as $dir){
//                if(trim($dir) == trim($filename)){
                    $rtn = true;
//                    $this->msg = "{$filename} -Pass-\n";
//                    echo $this->msg;
//                    utils::log($this->msg, 'logMYPC_fileIndexation');
                    return $rtn;
//                }
            }
        }
        else {
            return $rtn;
        }
    }
    
    /*
     * Guarda el directory ja llegit
     */
    private function saveDirectoryList($path){
        try {
            if(file_exists($this->txtDir)){
                $file = fopen($this->txtDir, "a");
                fwrite($file, ",{$path}");
            }
            else {
                $file = fopen($this->txtDir, "w");
                fwrite($file, "{$path}");
            }
            fclose($file);
        }
        catch(Exception $e){
            echo "Error save directory \n";
            echo $e."\n";
        }
    }
    
    /*
     * Guarda el fitxer ja llegit
     */
    private function saveFileList($filename){
        try {
            if(file_exists($this->txtFile)){
                $file = fopen($this->txtFile, "a");
                fwrite($file, ",{$filename}");
            }
            else {
                $file = fopen($this->txtFile, "w");
                fwrite($file, "{$filename}");
            }
            fclose($file);
        }
        catch(Exception $e){
            echo "Error save directory \n";
            echo $e."\n";
        }
    }
    
    /*
     * Comproba l'arxiu que li arriba si es una imatge o video per analitzarla
     * I si es un ZIP ho envia a una altre funció
     * 
     */
    private function thisFile($path){
        $this->msg = "";
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file = pathinfo($path, PATHINFO_BASENAME);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        if(($type == "jpg" || $type == "gif" || $type == "mp4" || $type == "mpg" || $type == "ogg") && ($filename != 'background' || $filename != 'banner')){
            $this->files++;
            $this->msg .= "{$file} - ";
            $code = explode('-', pathinfo($path, PATHINFO_FILENAME));
            $code = str_replace('GIF', '', $code[0]);
            $filesize = filesize($path);
            if($this->checkFileIn_PhotoFiles($file)){
                $this->msg .="Exist";
            }
            else {
                $this->msg .= $this->addFileDataInDB($code, $path, $file, $type, $filesize);
            }
            $this->saveFileList($file);
        }
        else if(pathinfo($path, PATHINFO_EXTENSION) == "zip"){
            if(strpos($filename, 'photosAndVideos') != true){
                $this->files_zip++;
                $this->checkZipFile($path);
            }
        }
        if($this->msg != ''){
            //echo $this->msg;
            utils::log($this->msg, 'logMYPC_fileIndexation');
        }
    }
    
    /*
     * Analitza el zip, els arxius que hi ha dins per insertar a la BD o no
     */
    private function checkZipFile($path){
        $zip = new compressorUtility();
        $status = $zip->iterateCompressedEventZip($path);
        if($status){
            foreach ($status as $fileZip){
                if($fileZip['size'] > 0){
                    $this->files_in_zip++;
                    $this->msg .= "{$path} -> {$filename} ";
                    $filename = substr($fileZip['name'], 1);
                    $filenameInDir = explode('//',$filename);
                    if($filenameInDir[1]){
                        $filename = $filenameInDir[1];
                    }
                    if($this->checkFileIn_PhotoFiles($filename)){
                        $this->msg .="Exist";
                    }
                    else {
                        $type = explode('.', $filename);
                        $type = $type[1];
                        $filesize = $fileZip['size'];
                        $filename1 = explode('.',$filename);
                        $code = explode('-',$filename1[0]);
                        $code = $code[0];
                        $this->msg .= $this->addFileDataInDB($code, $path, $filename, $type, $filesize);
                    }
                }
                else {
                    $this->dir_in_zip++;
                }
            }
        }
    }
    
    /*
     * Analitza si existeix l'arxiu a photo_files
     */
    private function checkFileIn_PhotoFiles($file){
        $mypcDB = new baseController;
        $mypcDB->createModel('photo_Files');
        $exist = $mypcDB->photo_FilesModel->getFile($file);
        if($exist[0]['id']){
            return true;
        }
        else {
            return false;
        }
    }
    
    /*
     * Inserta les dades a la BD si existeix a la taula Photos
     */
    private function addFileDataInDB($code, $path, $file, $type, $filesize){
        $mypcDB = new baseController;
        $mypcDB->createModel('photo_Files');
        $mypcDB->createModel('photos');
        $insertedPath = str_replace(G_PATH, '', $path);
        $exist = $mypcDB->photosModel->getPhoto($code);
        if($exist[0]['id']){
            $mypcDB->entity->loadEntity('photo_Files');
            $mypcDB->entity->setValue("photoId", $exist[0]['id']);
            $mypcDB->entity->setValue("ServerId", 1);
            $mypcDB->entity->setValue("name", $file);
            $mypcDB->entity->setValue("path", $insertedPath);
            $mypcDB->entity->setValue("fileType", $type);
            $mypcDB->entity->setValue("fileSize", $filesize);
            $mypcDB->entity->setValue("photobooth", $exist[0]['pbs_id']);
            $mypcDB->entity->setValue("dongle", $exist[0]['booth_id']);
            $mypcDB->entity->setValue("date", $exist[0]['Appusr_datetime']);
            $mypcDB->photo_FilesModel->insertphoto_Files();
            return "Inserted";
        }
        else {
            return "Not exist in photos";
        }
    }
    
    /*
     * Funcio que s'executa al final del Script per mostrar les dades que s'han
     * registrat
     */
    private function endScript(){
        $time = bcsub($this->time_end, $this->time_start, 4);
        $total = $this->files+$this->dir+$this->files_zip+$this->files_in_zip+$this->files_in_zip+$this->dir_in_zip;
        echo "---------------------------------------------------------\n";
        echo "Num files detected:                   {$this->files}\n";
        echo "Num directories:                      {$this->dir}\n";
        echo "Num zip detected:                     {$this->files_zip}\n";
        echo "Num files detected in zip:            {$this->files_in_zip}\n";
        echo "Num directories detected in zip:      {$this->dir_in_zip}\n";
        echo "Total Files:                          {$total}\n";
        echo "Total time:                           {$time} s.\n";
        echo "---------------------------------------------------------\n";
    }
    
}

utils::echo_(">>> Starting Script to File Indexation <<< \n", false);
new fileIndexaction();
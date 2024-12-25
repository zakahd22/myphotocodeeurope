<?php

namespace instagram\smartprint\domain;

require_once "InstagramUser.php";

class InstagramUserBuilder {

    private $token;
    private $accessToken;
    private $numPhotos;
    private $lastDownloadedPhotos;
    private $allPhotosServed;
    private $lastPhoto;
    private $idBooth;
    private $gameCode;
    private $totalPhotos;
    private $type;

    public function withIdBooth($idBooth)
    {
        $this->idBooth = $idBooth;
        return $this;
    }

    public function withAllPhotosServed($allPhotosServed)
    {
        $this->allPhotosServed = $allPhotosServed;
        return $this;
    }

    public function withLastPhoto($lastPhoto)
    {
        $this->lastPhoto = $lastPhoto;
        return $this;
    }

    public function withGameCode($gameCode)
    {
        $this->gameCode = $gameCode;
        return $this;
    }

    public function withToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function withNumPhotos($numPhotos)
    {
        $this->numPhotos = $numPhotos;
        return $this;
    }

    public function withLastDownloadedPhotos($lastDownloadedPhotos)
    {
        $this->lastDownloadedPhotos = $lastDownloadedPhotos;
        return $this;
    }

    public function withTotalPhotos($totalPhotos){
        $this->totalPhotos = $totalPhotos;
        return $this;
    }
    
     public function withType($type){
        $this->type = $type;
        return $this;
    }

    public function build()
    {
        return InstagramUser::of(
            $this->token,
            $this->accessToken,
            $this->totalPhotos,
            $this->numPhotos,
            $this->lastDownloadedPhotos,
            $this->allPhotosServed,
            $this->lastPhoto,
            $this->idBooth,
            $this->gameCode,
            $this->type
                
        );
    }
}
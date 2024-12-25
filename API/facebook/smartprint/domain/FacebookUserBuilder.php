<?php

namespace facebook\smartprint\domain;

require_once "FacebookUser.php";

class FacebookUserBuilder {

    private $token;
    private $accessToken;
    private $numPhotos;
    private $lastDownloadedPhotos;
    private $allPhotosServed;
    private $lastPhoto;
    private $idBooth;
    private $gameCode;
    private $totalPhotos;

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

    public function build()
    {
        return FacebookUser::of(
            $this->token,
            $this->accessToken,
            $this->totalPhotos,
            $this->numPhotos,
            $this->lastDownloadedPhotos,
            $this->allPhotosServed,
            $this->lastPhoto,
            $this->idBooth,
            $this->gameCode
        );
    }
}